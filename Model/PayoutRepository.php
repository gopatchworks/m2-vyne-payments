<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vyne\Magento\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Vyne\Magento\Api\Data\PayoutInterface;
use Vyne\Magento\Api\Data\PayoutInterfaceFactory;
use Vyne\Magento\Api\Data\PayoutSearchResultsInterfaceFactory;
use Vyne\Magento\Api\PayoutRepositoryInterface;
use Vyne\Magento\Model\ResourceModel\Payout as ResourcePayout;
use Vyne\Magento\Model\ResourceModel\Payout\CollectionFactory as PayoutCollectionFactory;

class PayoutRepository implements PayoutRepositoryInterface
{

    /**
     * @var ResourcePayout
     */
    protected $resource;

    /**
     * @var PayoutInterfaceFactory
     */
    protected $payoutFactory;

    /**
     * @var PayoutCollectionFactory
     */
    protected $payoutCollectionFactory;

    /**
     * @var Payout
     */
    protected $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;


    /**
     * @param ResourcePayout $resource
     * @param PayoutInterfaceFactory $payoutFactory
     * @param PayoutCollectionFactory $payoutCollectionFactory
     * @param PayoutSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourcePayout $resource,
        PayoutInterfaceFactory $payoutFactory,
        PayoutCollectionFactory $payoutCollectionFactory,
        PayoutSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->payoutFactory = $payoutFactory;
        $this->payoutCollectionFactory = $payoutCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(PayoutInterface $payout)
    {
        try {
            $this->resource->save($payout);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the payout: %1',
                $exception->getMessage()
            ));
        }
        return $payout;
    }

    /**
     * @inheritDoc
     */
    public function get($payoutId)
    {
        $payout = $this->payoutFactory->create();
        $this->resource->load($payout, $payoutId);
        if (!$payout->getId()) {
            throw new NoSuchEntityException(__('Payout with id "%1" does not exist.', $payoutId));
        }
        return $payout;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->payoutCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(PayoutInterface $payout)
    {
        try {
            $payoutModel = $this->payoutFactory->create();
            $this->resource->load($payoutModel, $payout->getPayoutId());
            $this->resource->delete($payoutModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Payout: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($payoutId)
    {
        return $this->delete($this->get($payoutId));
    }
}
