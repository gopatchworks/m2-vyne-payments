<?php

namespace Vyne\Payments\Controller\Gateway;

use Magento\Framework\Controller\ResultFactory;
use Vyne\Payments\Gateway\Payment as PaymentApi;

abstract class GatewayAbstract extends \Magento\Framework\App\Action\Action
{
    /**
     * @var ResultFactory
     */
    protected $resultRedirect;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Vyne\Payments\Helper\Data
     */
    protected $vyneHelper;

    /**
     * @var \Vyne\Payments\Helper\Cart
     */
    protected $vynecartHelper;

    /**
     * @var \Vyne\Payments\Helper\Logger
     */
    protected $vyneLogger;

    /**
     * @var PaymentApi
     */
    protected $paymentApi = false;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Vyne\Payments\Helper\Data $vyneHelper,
        \Vyne\Payments\Helper\Cart $vynecartHelper,
        \Vyne\Payments\Helper\Logger $vyneLogger
    ) {
        parent::__construct($context);
        $this->resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->vyneHelper = $vyneHelper;
        $this->vynecartHelper = $vynecartHelper;
        $this->logger = $vyneLogger;
    }

    /**
     * child classes handled this function
     */
    abstract public function execute();

    /**
     * initialize payment object and token
     *
     * @return PaymentApi
     */
    public function initPayment()
    {
        $this->paymentApi = $this->vyneHelper->initPayment();
    }
}
