<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Vyne\Magento\Model\Client;

use Vyne\api\TransactionsApi;
use Vyne\model\TransactionCaptureRequest;
use Vyne\model\TransactionRefundRequest;

class Transaction extends Base
{

    /**
     * get transaction api instance
     */
    public function getApiInstance()
    {
        try {
            // api instance
        }
        catch (\Exception $e) {
            $this->vyneLogger->logException($e);
        }
    }

    /**
     * retrieve details of transaction
     *
     * @param string
     * @return \Vyne\model\Transaction|\Vyne\model\Error401Unauthorized|\Vyne\model\ErrorGeneric
     */
    public function getTransactionDetail($transaction_id)
    {
        try {
            return $this->getApiInstance()->getTransaction($transaction_id);
        }
        catch (\Exception $e) {
            $this->vyneLogger->logException($e);
        }
    }

    /**
     * authorize new transaction
     *
     * @param  \Vyne\model\TransactionRequest $transaction_request transaction_request (optional)
     * @return boolean
     */
    public function authorize()
    {
        // authorize logic
    }

    /**
     * capture transaction online
     *
     * @param  string $transaction_id 
     * @param  float (optional)
     *
     * @return void
     */
    public function capture($transaction_id, $amount = null)
    {
        try {
            // capture logic
        }
        catch (\Exception $e) {
            $this->vyneLogger->logException($e);
        }
    }

    /**
     * refund transaction online
     *
     * @param  string $transaction_id The ID for the transaction to get the information for. (required)
     * @param  float (optional) - for partial capture
     *
     * @return void
     */
    public function refund($transaction_id, $amount = null)
    {
        try {
            // refund logic
        }
        catch (\Exception $e) {
            $this->vyneLogger->logException($e);
        }
    }
}
