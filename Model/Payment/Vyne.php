<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Vyne\Magento\Model\Payment;

use Vyne\Magento\Helper\Data as VyneHelper;
use Vyne\Magento\Helper\Logger as VyneLogger;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Vyne\Magento\Gateway\Payment as PaymentApi;
use Vyne\Magento\Gateway\Refund as RefundApi;

class Vyne extends \Magento\Payment\Model\Method\AbstractMethod
{
    const PAYMENT_TYPE_AUTH = 'authorize';
    const PAYMENT_TYPE_AUCAP = 'capture';
    const PAYMENT_METHOD_CODE = 'vyne';

    const STATUS_PARTIAL_REFUND = 'vyne_partial_refund';
    const STATUS_REFUND = 'vyne_refund';

    /**
     * @var string
     */
    protected $_infoBlockType = \Vyne\Magento\Block\Info::class;

    /**
     * @var string
     */
    protected $_code = self::PAYMENT_METHOD_CODE;

    /**
     * Payment Method feature
     *
     * @var bool
     */
    protected $_isGateway = true;

    /**
     * Payment Method feature
     *
     * @var bool
     */
    protected $_canAuthorize = true;

    /**
     * Payment Method feature
     *
     * @var bool
     */
    protected $_canCapture = true;

    /**
     * Payment Method feature
     *
     * @var bool
     */
    protected $_canCapturePartial = true;

    /**
     * Payment Method feature
     *
     * @var bool
     */
    protected $_canCaptureOnce = true;

    /**
     * Payment Method feature
     *
     * @var bool
     */
    protected $_canRefund = true;

    /**
     * Payment Method feature
     *
     * @var bool
     */
    protected $_canRefundInvoicePartial = true;

    /**
     * Payment Method feature
     *
     * @var bool
     */
    protected $_canVoid = true;

    /**
     * Payment Method feature
     *
     * @var bool
     */
    protected $_canUseInternal = false;

    /**
     * @var VyneHelper
     */
    protected $VyneHelper;

    /**
     * @var VyneLogger
     */
    protected $VyneLogger;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param Logger $logger
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @param DirectoryHelper $directory
     * @param VyneHelper $vyneHelper
     * @param VyneLogger $vyneLogger
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        VyneHelper $vyneHelper,
        VyneLogger $vyneLogger,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        DirectoryHelper $directory = null
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data
        );
        $this->vyneHelper = $vyneHelper;
        $this->vyneLogger = $vyneLogger;
    }

    public function isAvailable(
        \Magento\Quote\Api\Data\CartInterface $quote = null
    ) {
        return parent::isAvailable($quote);
    }

    /**
     * Void payment - If the transaction was not yet captured the authorization will instead be voided.
     *
     * @param \Magento\Framework\DataObject|\Magento\Payment\Model\InfoInterface|Payment $payment
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function void(\Magento\Payment\Model\InfoInterface $payment)
    {
        // void payment logic
    }

    /**
     * Capture payment
     *
     * @param \Magento\Framework\DataObject|\Magento\Payment\Model\InfoInterface|Payment $payment
     * @param float $amount
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        // capture payment logic
        return $this;
    }

    /**
     * Refund capture
     * UPDATED - handle refund by webhook & Plugin
     *
     * @param \Magento\Framework\DataObject|\Magento\Payment\Model\InfoInterface|Payment $payment
     * @param float $amount
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        /*utilizing plugin to refund*/
        /*see Vyne\Magento\Plugin\Magento\Sales\Controller\Adminhtml\Order\Creditmemo\Save */

        return $this;
    }


    /**
     * Cancel payment
     *
     * @param \Magento\Framework\DataObject|\Magento\Payment\Model\InfoInterface|Payment $payment
     * @return $this
     */
    public function cancel(\Magento\Payment\Model\InfoInterface $payment)
    {
        $this->void($payment);

        return $this;
    }
}

