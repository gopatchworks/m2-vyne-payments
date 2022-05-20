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
        // initialize vyne payment class
        $this->initPayment();

        if ($order_id = $this->checkoutSession->getLastOrderId()) {
            try {
                $order_details = $this->vyneHelper->extractOrderData($order_id);
                $this->logger->logMixed(['id' => $order_id, 'order_details' => $order_details]);

                $redirect_url = $this->paymentApi->paymentRedirect($order_details);
                return $this->resultRedirect->setUrl($redirect_url);
            }
            catch (\Exception $e) {
                $this->logger->logException($e);
            }

            return $this->failedVynePayment();
        }

        // if no last order found - display message
        $this->messageManager->addNoticeMessage(__('Vyne payment failed. Please contact us for support'));
        return $this->resultRedirect->setUrl('/');
    }

    /**
     * if payment is failed
     * 1. add error to session
     * 2. reinit shopping cart
     * 3. redirect to shopping cart
     *
     * @return ResultInterface
     */
    public function failedVynePayment()
    {
        $this->messageManager->addNoticeMessage(__('Vyne payment failed. Please contact us for support'));
        $this->vyneHelper->reinitCart($this->checkoutSession->getLastRealOrderId());

        return $this->resultRedirect->setUrl('/checkout/cart/');
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
