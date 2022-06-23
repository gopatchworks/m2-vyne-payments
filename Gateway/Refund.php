<?php

namespace Vyne\Magento\Gateway;

use GuzzleHttp\Psr7\Request;

class Refund extends ApiAbstract
{
    const ENDPOINT_REFUND = 'api/v1/refunds/init';

    // refund statuses
    const STATUS_NEW = 'The refund is submitted.';
    const STATUS_PROCESSING = 'The refund is processing and the transfer is in progress.';
    const STATUS_COMPLETED = 'The refund is successful and the transfer was completed.';
    const STATUS_FALIED = 'Processing the refund or transferring the money to the consumer failed.';

    // refund status groups
    const GROUP_PROCESSING = 'PROCESSING';
    const GROUP_FAILED = 'FAILED';
    const GROUP_COMPLETED = 'COMPLETED';
    const GROUP_NEW = 'NEW';

    /**
     * retrieve payment redirect using given order data
     *
     * @param array
     * @return string
     */
    public function paymentRefund($transaction_id, $amount)
    {
        $refund_data = [
            'payments' => [
                [
                    'paymentId' => $transaction_id,
                    'amount' => $amount
                ]
            ]
        ];

        $request = $this->refundRequest($refund_data);
        $options = $this->getConfig()->createHttpClientOptions();

        $response = $this->sendRequest($request, $options);

        return $response;
    }

    /**
     * prepare request body for payment refund request
     *
     * @param  array
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function refundRequest($refund_data)
    {
        $headers = $this->getConfig()->getAllApiKeys();
        $httpBody = \GuzzleHttp\json_encode($refund_data);

        return new Request(
            'POST',
            $this->getEndpointUrl(self::ENDPOINT_REFUND),
            $headers,
            $httpBody
        );
    }

}
