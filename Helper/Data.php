<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Vyne\Magento\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const VYNE_ENABLED = 'payment/vyne/active';
    const VYNE_INSTRUCTION = 'payment/vyne/instructions';
    const VYNE_DEBUG = 'payment/vyne/debug';
    const VYNE_INTENT = 'payment/vyne/payment_action';
    const VYNE_ORDER_STATUS = 'payment/vyne/order_status';
    const VYNE_ENV = 'payment/vyne/environment';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceHelper;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceHelper
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceHelper,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->scopeConfig = $scopeConfig;
        $this->_priceHelper = $priceHelper;
    }

    /**
     * check payment method enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool) $this->scopeConfig->getValue(self::VYNE_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * check payment debug enabled
     *
     * @return bool
     */
    public function isDebugOn()
    {
        return (bool) $this->scopeConfig->getValue(self::VYNE_DEBUG, ScopeInterface::SCOPE_STORE);
    }

    /**
     * retrieve vyne payment instructions
     *
     * @return string
     */
    public function getPaymentInstructions()
    {
        return (string) $this->scopeConfig->getValue(self::VYNE_INSTRUCTION, ScopeInterface::SCOPE_STORE);
    }

    /**
     * retrieve Vyne Intent
     *
     * @return string
     */
    public function getVyneIntent()
    {
        return (string) $this->scopeConfig->getValue(self::VYNE_INTENT, ScopeInterface::SCOPE_STORE);
    }

    /**
     * retrieve Vyne Environment
     *
     * @return string
     */
    public function getVyneEnvironment()
    {
        return (string) $this->scopeConfig->getValue(self::VYNE_ENV, ScopeInterface::SCOPE_STORE);
    }

    /**
     * retrieve Vyne New Order Status
     *
     * @return string
     */
    public function getVyneNewOrderStatus()
    {
        return (string) $this->scopeConfig->getValue(self::VYNE_ORDER_STATUS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * If there is mask for quote, return unmasked quote_id.
     * Otherwise, return input param
     *
     * @param string
     * @return string
     */
    public function getQuoteIdFromMask($cartId)
    {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

        if ($quoteIdMask->getQuoteId()) {
            return $quoteIdMask->getQuoteId();
        }

        return $cartId;
    }

    /**
     * format currency with symbol and no container
     *
     * @param string | number
     * @return string
     */
    public function formatCurrency($amount)
    {
        return $this->_priceHelper->convertAndFormat($amount, false);
    }

    /**
     * placeholder to determine partial refund is available
     *
     * @return boolean
     */
    public function blockPartialRefund()
    {
        return $this->isEnabled();
    }

    /**
     * verify vyne payment ready status . it requires
     * 1. module enabled
     *
     * @return boolean
     */
    public function checkVyneReady()
    {
        $isEnabled = $this->isEnabled();

        return $isEnabled;
    }
}
