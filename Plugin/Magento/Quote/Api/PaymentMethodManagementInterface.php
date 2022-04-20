<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Vyne\Magento\Plugin\Magento\Quote\Api;

use Vyne\Magento\Model\Client\Token as VyneToken;
use Vyne\Magento\Helper\Data as VyneHelper;

class PaymentMethodManagementInterface
{
    /**
     * @var VyneToken
     */
    protected $vyneToken;

    /**
     * @var VyneHelper
     */
    protected $vyneHelper;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        VyneToken $vyneToken,
        VyneHelper $vyneHelper
    ) {
        $this->vyneToken = $vyneToken;
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

