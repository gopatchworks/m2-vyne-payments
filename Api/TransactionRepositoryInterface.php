<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Vyne\Magento\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface TransactionRepositoryInterface
{

    /**
     * Save Transaction
     * @param \Vyne\Magento\Api\Data\TransactionInterface $transaction
     * @return \Vyne\Magento\Api\Data\TransactionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Vyne\Magento\Api\Data\TransactionInterface $transaction
    );

    /**
     * Retrieve token for Vyne Webform Checkout
     * @param string
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getToken($cartId);

    /**
     * Set Payment Information - Associate transaction payment detail with magento payment object
     * @param string
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param \Vyne\Magento\Api\Data\TransactionInterface $transaction
     *
     * @return \Vyne\Magento\Api\Data\TransactionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setPaymentInformation(
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Vyne\Magento\Api\Data\MethodInterface $methodData,
        \Vyne\Magento\Api\Data\ServiceInterface $serviceData,
        \Vyne\Magento\Api\Data\TransactionInterface $transactionData
    );

    /**
     * Retrieve Transaction
     * @param string $transactionId
     * @return \Vyne\Magento\Api\Data\TransactionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($transactionId);

    /**
     * retrieve buyer buy vyne transaction using vyne_transaction_id
     *
     * @param string
     * @return Vyne\Magento\Api\Data\TransactionInterface
     */
    public function getByVyneTransactionId($vyne_transaction_id);

    /**
     * Retrieve Transaction matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Vyne\Magento\Api\Data\TransactionSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Transaction
     * @param \Vyne\Magento\Api\Data\TransactionInterface $transaction
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Vyne\Magento\Api\Data\TransactionInterface $transaction
    );

    /**
     * Delete Transaction by ID
     * @param string $transactionId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($transactionId);
}

