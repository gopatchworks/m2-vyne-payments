<?php

namespace Vyne\Payments\Observer;

use Magento\Framework\Event\ObserverInterface;
use Vyne\Payments\Helper\Data as VyneHelper;
use Vyne\Payments\Helper\Logger as VyneLogger;
use Vyne\Payments\Helper\Order as OrderHelper;

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
