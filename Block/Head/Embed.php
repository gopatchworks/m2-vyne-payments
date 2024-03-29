<?php

declare(strict_types=1);

namespace Vyne\Payments\Block\Head;

use Vyne\Payments\Helper\Data as VyneHelper;

class Embed extends \Magento\Framework\View\Element\AbstractBlock
{
    /**
     * @var VyneHelper
     */
    protected $vyneHelper;

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        VyneHelper $vyneHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->vyneHelper = $vyneHelper;
    }

}
