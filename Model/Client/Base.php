<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Vyne\Magento\Model\Client;

use Vyne\Magento\Helper\Data as VyneHelper;
use Vyne\Magento\Helper\Logger as VyneLogger;

class Base
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
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        VyneHelper $vyneHelper,
        VyneLogger $vyneLogger,
        array $data = []
    ) {
        $this->vyneHelper = $vyneHelper;
        $this->vyneLogger = $vyneLogger;
    }

    /**
     * prepare configuration values and assign to vyne configuration
     *
     * @return \Vyne\VyneConfig
     */
    protected function getVyneConfig()
    {
        // get config class
    }
}
