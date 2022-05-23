<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Vyne\Magento\Api\Data;

interface PayoutSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Payout list.
     * @return \Vyne\Magento\Api\Data\PayoutInterface[]
     */
    public function getItems();

    /**
     * Set id list.
     * @param \Vyne\Magento\Api\Data\PayoutInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
