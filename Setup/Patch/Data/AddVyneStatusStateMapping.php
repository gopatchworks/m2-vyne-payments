<?php
declare(strict_types=1);

namespace Vyne\Payments\Setup\Patch\Data;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Sales\Model\Order;
use Vyne\Payments\Model\Payment\Vyne as VynePayment;

class AddVyneStatusStateMapping implements DataPatchInterface, PatchVersionInterface
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
     * @inheritDoc
     */
    public function apply()
    {
        $states = [
            [
                'status'           => VynePayment::STATUS_PAYMENT_RECEIVED,
                'state'            => Order::STATE_PROCESSING,
                'is_default'       => 0,
                'visible_on_front' => 1,
            ],
            //[
                //'status'           => VynePayment::STATUS_REFUND,
                //'state'            => Order::STATE_PROCESSING,
                //'is_default'       => 0,
                //'visible_on_front' => 1,
            //],
            //[
                //'status'           => VynePayment::STATUS_PARTIAL_REFUND,
                //'state'            => Order::STATE_PROCESSING,
                //'is_default'       => 0,
                //'visible_on_front' => 1,
            //],
        ];

        $this->moduleDataSetup->getConnection()->insertMultiple(
            $this->moduleDataSetup->getTable('sales_order_status_state'),
            $states
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function revert(): void
    {
        $adapter = $this->moduleDataSetup->getConnection();
        $adapter->delete(
            $this->moduleDataSetup->getTable('sales_order_status_state'),
            $adapter->quoteInto('status = ?', VynePayment::STATUS_PAYMENT_RECEIVED)
        );
        $adapter->delete(
            $this->moduleDataSetup->getTable('sales_order_status_state'),
            $adapter->quoteInto('status = ?', VynePayment::STATUS_REFUND)
        );
        $adapter->delete(
            $this->moduleDataSetup->getTable('sales_order_status_state'),
            $adapter->quoteInto('status = ?', VynePayment::STATUS_PARTIAL_REFUND)
        );
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '1.0.6';
    }

    /**
     * @inheritDoc
     */
    public function getAliases(): array
    {
        return [];
    }
}
