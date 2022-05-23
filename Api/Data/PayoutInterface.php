<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Vyne\Magento\Api\Data;

interface PayoutInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const payout_ID = 'payout_id';
    const ID = 'id';
    const METHOD_ID = 'method_id';
    const BUYER_ID = 'buyer_id';
    const SERVICE_ID = 'service_id';
    const STATUS = 'status';
    const AMOUNT = 'amount';
    const CAPTURED_AMOUNT = 'captured_amount';
    const REFUNDED_AMOUNT = 'refunded_amount';
    const CURRENCY = 'currency';
    const EXTERNAL_IDENTIFIER = 'external_identifier';
    const ENVIRONMENT = 'environment';
    const VYNE_payout_ID = 'vyne_payout_id';

    /**
     * Get payout_id
     * @return string|null
     */
    public function getPayoutId();

    /**
     * Set payout_id
     * @param string $payoutId
     * @return \Vyne\Magento\Api\Data\PayoutInterface
     */
    public function setPayoutId($payoutId);

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
     * Get method_id
     * @return string|null
     */
    public function getMethodId();

    /**
     * Set method_id
     * @param string $method_id
     * @return \Vyne\Magento\Api\Data\PayoutInterface
     */
    public function setMethodId($method_id);

    /**
     * Get buyer_id
     * @return string|null
     */
    public function getBuyerId();

    /**
     * Set buyer_id
     * @param string $buyer_id
     * @return \Vyne\Magento\Api\Data\PayoutInterface
     */
    public function setBuyerId($buyer_id);

    /**
     * Get service_id
     * @return string|null
     */
    public function getServiceId();

    /**
     * Set service_id
     * @param string $service_id
     * @return \Vyne\Magento\Api\Data\PayoutInterface
     */
    public function setServiceId($service_id);

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
     * @return integer|null
     */
    public function getAmount();

    /**
     * Set amount
     * @param integer $amount
     * @return \Vyne\Magento\Api\Data\PayoutInterface
     */
    public function setAmount($amount);

    /**
     * Get captured_amount
     * @return integer|null
     */
    public function getCapturedAmount();

    /**
     * Set captured_amount
     * @param integer $captured_amount
     * @return \Vyne\Magento\Api\Data\PayoutInterface
     */
    public function setCapturedAmount($captured_amount);

    /**
     * Get refunded_amount
     * @return integer|null
     */
    public function getRefundedAmount();

    /**
     * Set refunded_amount
     * @param string $refunded_amount
     * @return \Vyne\Magento\Api\Data\PayoutInterface
     */
    public function setRefundedAmount($refunded_amount);

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
     * Get external_identifier
     * @return string|null
     */
    public function getExternalIdentifier();

    /**
     * Set external_identifier
     * @param string $external_identifier
     * @return \Vyne\Magento\Api\Data\PayoutInterface
     */
    public function setExternalIdentifier($external_identifier);

    /**
     * Get environment
     * @return string|null
     */
    public function getEnvironment();

    /**
     * Set environment
     * @param string $environment
     * @return \Vyne\Magento\Api\Data\PayoutInterface
     */
    public function setEnvironment($environment);

    /**
     * Get vyne_payout_id
     * @return string|null
     */
    public function getVynePayoutId();

    /**
     * Set vyne_payout_id
     * @param string $vyne_payout_id
     * @return \Vyne\Magento\Api\Data\PayoutInterface
     */
    public function setVynePayoutId($vyne_payout_id);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Vyne\Magento\Api\Data\PayoutExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Vyne\Magento\Api\Data\PayoutExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Vyne\Magento\Api\Data\PayoutExtensionInterface $extensionAttributes
    );
}
