<?php

namespace Vyne\Magento\Controller\Gateway;

use Magento\Framework\Controller\ResultFactory;
use Vyne\Magento\Gateway\Payment as PaymentApi;

class GatewayAbstract extends \Magento\Framework\App\Action\Action
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
     * @var \Vyne\Magento\Helper\Data
     */
    protected $vyneHelper;

    /**
     * @var \Vyne\Magento\Helper\Logger
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
        \Vyne\Magento\Helper\Data $vyneHelper,
        \Vyne\Magento\Helper\Logger $vyneLogger
    ) {
        parent::__construct($context);
        $this->resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->vyneHelper = $vyneHelper;
        $this->logger = $vyneLogger;
    }

    /**
     * child classes handled this function
     */
    public function execute()
    {
    }

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
