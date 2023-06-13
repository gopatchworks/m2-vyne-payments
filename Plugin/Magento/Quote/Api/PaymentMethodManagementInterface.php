<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Vyne\Payments\Plugin\Magento\Quote\Api;

use Vyne\Payments\Helper\Data as VyneHelper;

class PaymentMethodManagementInterface
{
    /**
     * @var VyneHelper
     */
    protected $vyneHelper;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        VyneHelper $vyneHelper
    ) {
        $this->vyneHelper = $vyneHelper;
    }

    /**
     * prepare vyne for checkout
     *
     * @return string redirect url or error message.
     */
    public function aroundSet(
        \Magento\Quote\Api\PaymentMethodManagementInterface $subject,
        \Closure $proceed,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $method
    ) {
        $cartId = $this->vyneHelper->getQuoteIdFromMask($cartId);
        $result = $proceed($cartId, $method);

        return $result;
    }
}

