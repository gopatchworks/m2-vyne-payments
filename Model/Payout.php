<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Vyne\Magento\Model;

use Vyne\Magento\Api\Data\PayoutInterface;
use Vyne\Magento\Api\Data\PayoutInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class Payout extends \Magento\Framework\Model\AbstractModel
{
    protected $payoutDataFactory;

    protected $dataObjectHelper;

    protected $_eventPrefix = 'vyne_payouts';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param PayoutInterfaceFactory $payoutDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Vyne\Magento\Model\ResourceModel\Payout $resource
     * @param \Vyne\Magento\Model\ResourceModel\Payout\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        PayoutInterfaceFactory $payoutDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Vyne\Magento\Model\ResourceModel\Payout $resource,
        \Vyne\Magento\Model\ResourceModel\Payout\Collection $resourceCollection,
        array $data = []
    ) {
        $this->payoutDataFactory = $payoutDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve payout model with payout data
     * @return PayoutInterface
     */
    public function getDataModel()
    {
        $payoutData = $this->getData();
        
        $payoutDataObject = $this->payoutDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $payoutDataObject,
            $payoutData,
            PayoutInterface::class
        );
        
        return $payoutDataObject;
    }
}

