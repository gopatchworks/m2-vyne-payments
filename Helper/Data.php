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
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Vyne\Magento\Gateway\Configuration as VyneConfig;

class Data extends AbstractHelper
{
    const VYNE_ENABLED = 'payment/vyne/active';
    const VYNE_INSTRUCTION = 'payment/vyne/instructions';
    const VYNE_CLIENT_ID = 'payment/vyne/client_id';
    const VYNE_CLIENT_SECRET = 'payment/vyne/client_secret';
    const VYNE_DESTINATION_ACCOUNT = 'payment/vyne/destination_account';
    const VYNE_DEBUG = 'payment/vyne/debug';
    const VYNE_INTENT = 'payment/vyne/payment_action';
    const VYNE_ORDER_STATUS = 'payment/vyne/order_status';
    const VYNE_ENV = 'payment/vyne/environment';
    const VYNE_MEDIA_TYPE = 'payment/vyne/media_type';

    const VYNE_WEBHOOK_PAYMENT = 'vyne/webhook/payment';

    const COUNTRY_CODE_PATH = 'general/country/default';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var VyneConfig
     */
    protected $vyneConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var EncryptorInterface
     */
    protected $_encryptor;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceHelper;

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
        EncryptorInterface $encryptor,
        UrlInterface $urlBuilder,
        OrderRepositoryInterface $orderRepository,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->scopeConfig = $scopeConfig;
        $this->_encryptor = $encryptor;
        $this->urlBuilder = $urlBuilder;
        $this->orderRepository = $orderRepository;
        $this->storeManager = $storeManager;
        $this->priceHelper = $priceHelper;
    }

    /**
     * get Vyne config object
     *
     * @return Vyne\Magento\Gateway\VyneConfig
     */
    public function getVyneConfig()
    {
        if (!$this->vyneConfig) {
            $this->vyneConfig = new VyneConfig();

            $this->vyneConfig
                 ->setDebug($this->isDebugOn())
                 ->setEnvironment($this->getVyneEnvironment())
                 ->setClientCredential($this->getVyneClientId(), $this->getVyneClientSecret());
        }

        return $this->vyneConfig;
    }

    /**
     * retrieve order data in Vyne friendly format
     *
     * @param string order entity_id
     * @return array
     */
    public function extractOrderData($entity_id)
    {
        $order = $this->orderRepository->get($entity_id);
        $currency_code = $order->getOrderCurrencyCode();
        $currency_code = "GBP";
        $data = [
            'amount' => number_format(floatval($order->getGrandTotal()), 2),
            'currency' => $currency_code,
            'destinationAccount' => $this->getDestinationAccount(),
            'description' => (string) __('Web payment'),
            'callbackUrl' => $this->getVyneWebhookPayment(),
            'mediaType' => $this->getMediaType(),
            'countries' => [$this->getCountryCodeByWebsite()],
            //'customerReference' => 'P739570946',
            //'merchantReference' => '76FG7JQ'
        ];

        return $data;
    }

    /**
     * get webhook payment controller
     *
     * @return string
     */
    public function getVyneWebhookPayment()
    {
        return $this->urlBuilder->getUrl(self::VYNE_WEBHOOK_PAYMENT);
    }

    /**
     * check payment method enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool) $this->scopeConfig->getValue(
            self::VYNE_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * get media type of Vyne, can be either URL or QR code
     *
     * @return string
     */
    public function getMediaType()
    {
        return (string) $this->scopeConfig->getValue(
            self::VYNE_MEDIA_TYPE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * get destination bank account
     * Specifies the ID of the merchant's destination bank account with Vyne (e.g. GBP1)
     *
     * @return string
     */
    public function getDestinationAccount()
    {
        return (string) $this->scopeConfig->getValue(
            self::VYNE_DESTINATION_ACCOUNT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Country code by website scope
     *
     * @return string
     */
    public function getCountryCodeByWebsite()
    {
        return "GB";
        return $this->scopeConfig->getValue(
            self::COUNTRY_CODE_PATH,
            ScopeInterface::SCOPE_WEBSITES
        );
    }

    /**
     * check payment debug enabled
     *
     * @return bool
     */
    public function isDebugOn()
    {
        return (bool) $this->scopeConfig->getValue(
            self::VYNE_DEBUG,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * retrieve vyne payment instructions
     *
     * @return string
     */
    public function getPaymentInstructions()
    {
        return (string) $this->scopeConfig->getValue(
            self::VYNE_INSTRUCTION,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * retrieve vyne client_id
     *
     * @return string
     */
    public function getVyneClientId()
    {
        $encrypted_client_id = (string) $this->scopeConfig->getValue(
            self::VYNE_CLIENT_ID,
            ScopeInterface::SCOPE_STORE
        );

        return $this->_encryptor->decrypt($encrypted_client_id);
    }

    /**
     * retrieve vyne client_secret
     *
     * @return string
     */
    public function getVyneClientSecret()
    {
        $encrypted_client_secret = (string) $this->scopeConfig->getValue(
            self::VYNE_CLIENT_SECRET,
            ScopeInterface::SCOPE_STORE
        );

        return $this->_encryptor->decrypt($encrypted_client_secret);
    }

    /**
     * retrieve Vyne Intent
     *
     * @return string
     */
    public function getVyneIntent()
    {
        return (string) $this->scopeConfig->getValue(
            self::VYNE_INTENT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * retrieve Vyne Environment
     *
     * @return string
     */
    public function getVyneEnvironment()
    {
        return (string) $this->scopeConfig->getValue(
            self::VYNE_ENV,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * retrieve Vyne New Order Status
     *
     * @return string
     */
    public function getVyneNewOrderStatus()
    {
        return (string) $this->scopeConfig->getValue(
            self::VYNE_ORDER_STATUS,
            ScopeInterface::SCOPE_STORE
        );
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
        return $this->priceHelper->convertAndFormat($amount, false);
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

    /**
     * decode payload from Vyne
     *
     * @param string
     * @return stdClass Object
     */
    public function decodeJWTBase64($payvyne_payemnt_payload)
    {
        $envelope = explode('.', $payvyne_payemnt_payload);
        // only take body
        $body = $envelope[1];
        $processed_body = str_replace('_', '/', str_replace('-','+',$body));

        return json_decode(base64_decode($processed_body));
    }

    /**
     * translate Vyne Payment status to Magento Order Status
     *
     * @param string
     * @return string
     */
    public function getOrderStatus($payment_status)
    {
        return 'processing';
    }
}
