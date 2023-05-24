<?php
declare(strict_types=1);

namespace Vyne\Magento\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface TokenRepositoryInterface
{

    /**
     * Save Token
     * @param \Vyne\Magento\Api\Data\TokenInterface $token
     * @return \Vyne\Magento\Api\Data\TokenInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Vyne\Magento\Api\Data\TokenInterface $token
    );

    /**
     * Retrieve Token
     * @param string $tokenId
     * @return \Vyne\Magento\Api\Data\TokenInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($tokenId);

    /**
     * Retrieve Token matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Vyne\Magento\Api\Data\TokenSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Token
     * @param \Vyne\Magento\Api\Data\TokenInterface $token
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Vyne\Magento\Api\Data\TokenInterface $token
    );

    /**
     * Delete Token by ID
     * @param string $tokenId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($tokenId);
}
