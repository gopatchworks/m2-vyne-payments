<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vyne\Payments\Api\Data;

interface TokenSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Token list.
     * @return \Vyne\Payments\Api\Data\TokenInterface[]
     */
    public function getItems();

    /**
     * Set id list.
     * @param \Vyne\Payments\Api\Data\TokenInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
