<?php

namespace Vyne\Magento\Gateway;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use Vyne\Magento\Helper\Logger;
use Vyne\Magento\Helper\Logger as VyneLogger;

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
     * @var VyneLogger
     */
    protected $vyneLogger;

    public function __construct(Configuration $config = null, ClientInterface $client = null)
    {
        parent::__construct($config, $client);
        $this->vyneLogger = new Logger();
    }

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
        $this->vyneLogger->logMixed(sprintf('Refund payload: %s', $refund_data));

        $request = $this->refundRequest($refund_data);
        $options = $this->getConfig()->createHttpClientOptions();

        $response = $this->sendRequest($request, $options);
        $this->vyneLogger->logMixed(sprintf('Refund response: %s', json_encode($response)));

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
