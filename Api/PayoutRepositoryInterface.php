<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Vyne\Magento\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface PayoutRepositoryInterface
{

    /**
     * Save Payout
     * @param \Vyne\Magento\Api\Data\PayoutInterface $payout
     * @return \Vyne\Magento\Api\Data\PayoutInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Vyne\Magento\Api\Data\PayoutInterface $payout
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
     * Set Payment Information - Associate payout payment detail with magento payment object
     * @param string
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param \Vyne\Magento\Api\Data\PayoutInterface $payout
     *
     * @return \Vyne\Magento\Api\Data\PayoutInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setPaymentInformation(
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Vyne\Magento\Api\Data\PayoutInterface $payoutData
    );

    /**
     * Retrieve Payout
     * @param string $payoutId
     * @return \Vyne\Magento\Api\Data\PayoutInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($payoutId);

    /**
     * retrieve buyer buy vyne payout using vyne_payout_id
     *
     * @param string
     * @return Vyne\Magento\Api\Data\PayoutInterface
     */
    public function getByVynePayoutId($vyne_payout_id);

    /**
     * Retrieve Payout matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Vyne\Magento\Api\Data\PayoutSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Payout
     * @param \Vyne\Magento\Api\Data\PayoutInterface $payout
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Vyne\Magento\Api\Data\PayoutInterface $payout
    );

    /**
     * Delete Payout by ID
     * @param string $payoutId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($payoutId);
}

