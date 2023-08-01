<?php

namespace Vyne\Payments\Setup\Patch\Data;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Vyne\Payments\Model\Payment\Vyne as VynePayment;

/**
 * Class AddVyneOrderStates
 * @package Vyne\Payments\Setup\Patch
 */
class AddVyneRefundStatuses implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * AddVyneOrderStates constructor.
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $data = [];
        $statuses = [
            VynePayment::STATUS_REFUND => __('Refunded By Vyne'),
            VynePayment::STATUS_PARTIAL_REFUND => __('Partially Refunded By Vyne'),
        ];
        foreach ($statuses as $code => $info) {
            $data[] = ['status' => $code, 'label' => $info];
        }
        try {
            $this->moduleDataSetup->getConnection()->insertArray(
                $this->moduleDataSetup->getTable('sales_order_status'),
                ['status', 'label'],
                $data
            );
        }
        catch (\Exception $e) {
            // do nothing
        }
        /**
         * Prepare database after install
         */
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '1.0.2';
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
