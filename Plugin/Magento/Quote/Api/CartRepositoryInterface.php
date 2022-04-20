<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Vyne\Magento\Plugin\Magento\Quote\Api;

use Vyne\Magento\Helper\Data as VyneHelper;
use Vyne\Magento\Helper\Customer as CustomerHelper;

class CartRepositoryInterface
{
    /**
     * @var VyneHelper
     */
    protected $vyneHelper;

    /**
     * @var CustomerHelper
     */
    protected $customerHelper;

    /**
     * @param VyneHelper $vyneHelper
     * @param CustomerHelper $customerHelper
     */
    public function __construct(
        VyneHelper $vyneHelper,
        CustomerHelper $customerHelper
    ) {
        $this->vyneHelper = $vyneHelper;
        $this->customerHelper = $customerHelper;
    }

    /**
     * whenever cart is saved, interact with vyne
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return void
     */
    public function aroundSave(
        \Magento\Quote\Api\CartRepositoryInterface $subject,
        \Closure $proceed,
        \Magento\Quote\Api\Data\CartInterface $quote
    ) {
        if ($this->vyneHelper->checkVyneReady()) {
            $this->customerHelper->connectQuoteWithVyne($quote);
        }

        // NOTE: additional fix for multishipping checkout issue  https://github.com/magento/magento2/pull/26637
        // issue in vendor/magento/module-quote/Model/QuoteAddressValidator.php [function] validateForCart
        // when customer logged in, $cart->getCustomerIsGuest() still return true
        if ($quote->getCustomer() && $quote->getCustomer()->getId()) {
            $quote->setCustomerIsGuest(false);
        }

        $result = $proceed($quote);

        return $result;
    }
}
