<?php
namespace Vyne\Magento\Cron;

class Recurring
{
    const BATCH_SIZE = 100;

    /**
     * @var \Vyne\Magento\Helper\Logger
     */
    public $vyneLogger;

    public function __construct(
        \Vyne\Magento\Helper\Logger $vyneLogger
    )
    {
        $this->vyneLogger = $vyneLogger;
    }

    /**
     * process recurring payment
     *
     * @return void
     */
    public function execute()
    {
    }
}
