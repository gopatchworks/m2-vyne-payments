<?php
declare(strict_types=1);

namespace Vyne\Payments\Observer;

use Vyne\Payments\Helper\Logger as VyneLogger;
use Vyne\Payments\Helper\Data as VyneHelper;
use Magento\Quote\Model\QuoteFactory;

class OrderPaymentSaveBefore implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var VyneLogger
     */
    protected $vyneLogger;

    /**
     * @var VyneHelper
     */
    protected $vyneHelper;

    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @param VyneLogger $vyneLogger
     * @param QuoteFactory $quoteFactory
     */
    public function __construct(
        VyneLogger $vyneLogger,
        VyneHelper $vyneHelper,
        QuoteFactory $quoteFactory
    ) {
        $this->vyneLogger = $vyneLogger;
        $this->vyneHelper = $vyneHelper;
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
    }
}

