<?php

namespace Vyne\Magento\Observer;

use Magento\Framework\Event\ObserverInterface;
use Vyne\Magento\Helper\Data as VyneHelper;
use Vyne\Magento\Helper\Logger as VyneLogger;
use Vyne\Magento\Helper\Order as OrderHelper;

class OrderPlaceAfter implements ObserverInterface
{
    /**
     * @var VyneHelper
     */
    protected $vyneHelper;

    /**
     * @var VyneLogger
     */
    protected $vyneLogger;

    /**
     * @var OrderHelper
     */
    protected $orderHelper;

    /**
     * @param VyneHelper $vyneHelper
     * @param OrderHelper $orderHelper
     */
    public function __construct(
        VyneHelper $vyneHelper,
        VyneLogger $vyneLogger,
        OrderHelper $orderHelper
    ) {
        $this->vyneHelper = $vyneHelper;
        $this->vyneLogger = $vyneLogger;
        $this->orderHelper = $orderHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
    }
}
