<?php

namespace Vyne\Magento\Controller\Gateway;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;

class Redirect extends GatewayAbstract implements HttpGetActionInterface, CsrfAwareActionInterface
{
    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if ($this->_checkoutSession->getLastRealOrderId()) {
            // process payment processing request
            
            $client_id = 'GzepWxJgqd5haD0ODDP8dA';
            $client_secret = 'Klq-fTFTbVJ5bwJL4BGspj8a2Nw3GJufM1rbJq5ROa4';
            $response = $this->_paymentApi->requestToken($client_id, $client_secret);

            $status_code = $response->getStatusCode();
            if ($status_code == 200) {
                $response_body_content = $response->getBody()->getContents();
                if (is_string($response_body_content) && strlen($response_body_content) > 0) {
                    $content = json_decode($response_body_content);

                    // continue with redirect
                    $token = $content->access_token;

                    // prepare order detial
                    $order_details = [
                        'amount' => '35.95',
                        'currency' => 'GBP',
                        'destinationAccount' => 'GBP1',
                        'description' => 'Web payment',
                        'callbackUrl' => 'https://8e31-183-91-15-231.ngrok.io/index.php/vyne/webhook/payment',
                        'mediaType' => "URL", // default is URL, other option is QR
                        'countries' => ["GB"], // default value is GB
                        'customerReference' => 'm2-123', // optional
                        'merchantReference' => 'm2-99' // optional
                    ];

                    $this->_logger->logMixed(['id' => $this->_checkoutSession->getLastRealOrderId(), 'order_details' => $order_details]);

                    try {
                        $redirect = $this->_paymentApi->requestPaymentRedirect($token, $order_details);
                        if ($redirect->getStatusCode() == 200) {
                            $redirect_content = $redirect->getBody()->getContents();
                            $redirect_obj = json_decode($redirect_content);

                            $this->_logger->logMixed(['redirectUrl' => $redirect_obj->redirectUrl]);
                            return $this->_resultRedirect->setUrl($redirect_obj->redirectUrl);
                        }
                    }
                    catch (\Exception $e) {
                        $this->_logger->logException($e);
                    }
                }
            }

            return $this->failedVynePayment();
        }

        return $this->_resultRedirect->setUrl('/');
    }

    public function failedVynePayment()
    {
        die('test');
        $this->messageManager->addNoticeMessage(__('Vyne payment failed. Please contact us for support'));
        //$this->_vyneHelper->reinitCart($this->_checkoutSession->getLastRealOrderId());

        return $this->_resultRedirect->setUrl('/checkout/cart/');
    }

    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
