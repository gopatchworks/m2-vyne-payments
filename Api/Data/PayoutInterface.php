<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Vyne\Magento\Api\Data;

interface PayoutInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ID = 'id';
    const PAYMENT_ID = 'payment_id';
    const STATUS = 'status';
    const AMOUNT = 'amount';
    const CURRENCY = 'currency';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * Get id
     * @return string|null
     */
    public function getId();

    /**
     * Set id
     * @param string $id
     * @return \Vyne\Magento\Api\Data\PayoutInterface
     */
    public function setId($id);

    /**
     * Get payment_id
     * @return string|null
     */
    public function getPaymentId();

    /**
     * Set payment_id
     * @param string $payment_id
     * @return \Vyne\Magento\Api\Data\PayoutInterface
     */
    public function setPaymentId($payment_id);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Vyne\Magento\Api\Data\PayoutInterface
     */
    public function setStatus($status);

    /**
     * Get amount
     * @return float|null
     */
    public function getAmount();

    /**
     * Set amount
     * @param float $amount
     * @return \Vyne\Magento\Api\Data\PayoutInterface
     */
    public function setAmount($amount);

    /**
     * Get currency
     * @return string|null
     */
    public function getCurrency();

    /**
     * Set currency
     * @param string $currency
     * @return \Vyne\Magento\Api\Data\PayoutInterface
     */
    public function setCurrency($currency);

    /**
     * Get created_at
     * @return timestamp|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $created_at
     * @return \Vyne\Magento\Api\Data\PayoutInterface
     */
    public function setCreatedAt($created_at);

    /**
     * Get updated_at
     * @return timestamp|null
     */
    public function getUpdatedAt();

    /**
     * Set updated_at
     * @param string $updated_at
     * @return \Vyne\Magento\Api\Data\PayoutInterface
     */
    public function setUpdatedAt($updated_at);
}
