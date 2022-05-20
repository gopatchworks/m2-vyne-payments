<?php

namespace Vyne\Magento\Gateway;

use GuzzleHttp\Psr7\Request;

class Payout extends ApiAbstract
{
    const ENDPOINT_PAYOUT = 'api/v1/payouts';

    const STATUS_NEW = 'The payout is submitted.';
    const STATUS_PROCESSING = 'The payout is processing and the transfer is in progress.';
    const STATUS_COMPLETED = 'The payout is successful and the transfer was completed.';
    const STATUS_FALIED = ' Processing the payout or transferring the money to the consumer failed.';

    /**
     * retrieve payment redirect using given order data
     *
     * @param array
     * @return string
     */
    public function submitPayout($payout_data)
    {
        $request = $this->payoutRequest($payoutData);
        $options = $this->getConfig()->createHttpClientOptions();

        $response = $this->sendRequest($request, $options);

        return $redirect->redirectUrl;
    }

    /**
     * prepare request body for payment redirect request
     *
     * @param  array
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function payoutRequest($payout_data)
    {
        $headers = $this->getConfig()->getAllApiKeys();
        $httpBody = \GuzzleHttp\json_encode($payout_data);

        return new Request(
            'POST',
            $this->getEndpointUrl(self::ENDPOINT_PAYOUT),
            $headers,
            $httpBody
        );
    }

}
