<?php

namespace Vyne\Magento\Controller\Gateway;

use Magento\Framework\Controller\ResultFactory;
use Vyne\Magento\Gateway\Payment as PaymentApi;

class GatewayAbstract extends \Magento\Framework\App\Action\Action
{
    protected $_resultRedirect;
    protected $_customerSession;
    protected $_checkoutSession;
    protected $_vyneHelper;
    protected $_vyneLogger;
    protected $_paymentApi;

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
        \Vyne\Magento\Helper\Logger $vyneLogger,
        PaymentApi $paymentApi
    ) {
        parent::__construct($context);
        $this->_resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_vyneHelper = $vyneHelper;
        $this->_logger = $vyneLogger;
        $this->_paymentApi = $paymentApi;
    }

    /**
     * child classes handled this function
     */
    public function execute()
    {
    }
}
