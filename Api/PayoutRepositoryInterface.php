<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
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
     * Retrieve Payout
     * @param string $payoutId
     * @return \Vyne\Magento\Api\Data\PayoutInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($payoutId);

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
