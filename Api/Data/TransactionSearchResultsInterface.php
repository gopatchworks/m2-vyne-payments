<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Vyne\Magento\Api\Data;

interface TransactionSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Transaction list.
     * @return \Vyne\Magento\Api\Data\TransactionInterface[]
     */
    public function getItems();

    /**
     * Set id list.
     * @param \Vyne\Magento\Api\Data\TransactionInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
