<?php
namespace Vyne\Magento\Gateway;

use GuzzleHttp\Psr7\Request;

class Payment extends ApiAbstract
{
    const ENDPOINT_PAYMENT = 'api/v1/payments';

    /**
     *  The payment request has been created. Awaiting consumer interaction.
     *  Redirect consumer to the hosted checkout (or bank when using integrated checkout).
     */
    const STATUS_CREATED = 'Payment created';

    /**
     * The payment request response is pending.
     * Inform the consumer that the payment is in progress.
     */
    const STATUS_PROCESSING = 'Processing';

    /**
     * The payment has been abandoned
     * Inform the consumer that the payment has expired due to inactivity.
     */
    const STATUS_EXPIRED = 'User abandoned';

    /**
     * The consumer did not give consent to set up the payment (by selecting cancel at the hosted checkout).
     * Redirect the consumer to select a payment method again (restart the payment flow).
     */
    const STATUS_NO_CONSENT = 'User cancelled';

    /**
     * The consumer did not authorise the payment request in their bank (by selecting reject/do not authorise/cancel).
     * Redirect the consumer to select a payment method again (restart the payment flow) and inform them no funds were taken.
     */
    const STATUS_NO_CONFIRM = 'User rejected';

    /**
     * The payment request has been rejected by the bank, or the consumer failed to confirm the transaction.
     * Inform the consumer that the payment was declined.
     */
    const STATUS_FAILED = 'Declined';

    /**
     * The payment has been accepted by the payer's bank and is scheduled to be executed imminently.
     * Inform the consumer that the payment was accepted and is being processed.
     */
    const STATUS_COMPLETED = 'Accepted';

    /**
     * The payment has executed and funds have been received in the destination account.
     * Inform the consumer that the payment was successful.
     */
    const STATUS_SETTLED = 'Funds received';

    /**
     * A part of the original payment amount has been successfully refunded to the payer's bank account.
     * Inform the consumer that the refund was successful.
     */
    const STATUS_PART_REFUNDED = 'Partially refunded';

    /**
     * The total original payment amount has been successfully refunded to the payer's bank account.
     * Inform the consumer that the refund was successful.
     */
    const STATUS_REFUNDED = 'Refunded';

    const GROUP_PROCESSING = 'processing';
    const GROUP_CANCEL = 'cancel';
    const GROUP_SUCCESS = 'complete';
    const GROUP_PAYMENT_REVIEW = 'payment_review';
    const GROUP_REFUND = 'refund';

    /**
     * retrieve classified statuses
     *
     * @return array
     */
    public static function getTransactionStatuses()
    {
        $processing_statuses = [
            'CREATED' => self::STATUS_CREATED,
            'PROCESSING' => self::STATUS_PROCESSING
        ];
        $payment_review_statuses = [
            'COMPLETED' => self::STATUS_COMPLETED,
        ];
        $success_statuses = [
            'SETTLED' => self::STATUS_SETTLED
        ];
        $cancel_statuses = [
            'NO_CONSENT' => self::STATUS_NO_CONSENT,
            'NO_CONFIRM' => self::STATUS_NO_CONFIRM,
            'FAILED' => self::STATUS_FAILED,
            'EXPIRED' => self::STATUS_EXPIRED
        ];
        $refund_statuses = [
            'PART_REFUNDED' => self::STATUS_PART_REFUNDED,
            'REFUNDED' => self::STATUS_REFUNDED,
        ];

        return [
            self::GROUP_PROCESSING => $processing_statuses,
            self::GROUP_PAYMENT_REVIEW => $payment_review_statuses,
            self::GROUP_SUCCESS => $success_statuses,
            self::GROUP_CANCEL => $cancel_statuses,
            self::GROUP_REFUND => $refund_statuses
        ];
    }

    /**
     * retrieve transaction group code
     *
     * @return string
     */
    public static function getTransactionAction($paymentStatus)
    {
        foreach (self::getTransactionStatuses() as $group => $values){
            if (array_key_exists($paymentStatus, $values)) {
                return $group;
            }
        }

        return false;
    }

    /**
     * retrieve payment redirect using given order data
     *
     * @param array
     * @return string
     */
    public function paymentRedirect($order_data)
    {
        $request = $this->redirectRequest($order_data);
        $options = $this->getConfig()->createHttpClientOptions();

        $response = $this->sendRequest($request, $options);

        return $response->redirectUrl;
    }

    /**
     * prepare request body for payment redirect request
     *
     * @param  array
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function redirectRequest($order_data)
    {
        $headers = $this->getConfig()->getAllApiKeys();
        $httpBody = \GuzzleHttp\json_encode($order_data);

        return new Request(
            'POST',
            $this->getEndpointUrl(self::ENDPOINT_PAYMENT),
            $headers,
            $httpBody
        );
    }

}
