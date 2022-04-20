<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Vyne\Magento\Model;

use Vyne\Magento\Api\Data\TransactionInterface;
use Vyne\Magento\Api\Data\TransactionInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class Transaction extends \Magento\Framework\Model\AbstractModel
{
    protected $transactionDataFactory;

    protected $dataObjectHelper;

    protected $_eventPrefix = 'vyne_transactions';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param TransactionInterfaceFactory $transactionDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Vyne\Magento\Model\ResourceModel\Transaction $resource
     * @param \Vyne\Magento\Model\ResourceModel\Transaction\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        TransactionInterfaceFactory $transactionDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Vyne\Magento\Model\ResourceModel\Transaction $resource,
        \Vyne\Magento\Model\ResourceModel\Transaction\Collection $resourceCollection,
        array $data = []
    ) {
        $this->transactionDataFactory = $transactionDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve transaction model with transaction data
     * @return TransactionInterface
     */
    public function getDataModel()
    {
        $transactionData = $this->getData();
        
        $transactionDataObject = $this->transactionDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $transactionDataObject,
            $transactionData,
            TransactionInterface::class
        );
        
        return $transactionDataObject;
    }
}

