<?php

declare(strict_types=1);

namespace Vyne\Payments\Controller\Webhook;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Vyne\Payments\Helper\Logger as VyneLogger;
use Vyne\Payments\Helper\Cart as VyneCart;
use Vyne\Payments\Helper\Order as VyneOrder;
use Vyne\Payments\Gateway\Payment as PaymentApi;

/**
 * Vyne Payment Webhook Order Controller
 *
 * @package Vyne\Payments\Controller\Decision
 */
abstract class AbstractWebhookGet extends Action
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Vyne\Payments\Helper\Data
     */
    protected $vyneHelper;

    /**
     * @var \Vyne\Payments\Helper\Cart
     */
    protected $vyneCart;

    /**
     * @var vyneOrder
     */
    protected $vyneOrder;

    /**
     * @var VyneLogger
     */
    protected $vyneLogger;

    /**
     * @var ResultFactory
     */
    protected $resultRedirect;

    public function __construct(
        Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        OrderRepositoryInterface $orderRepository,
        \Vyne\Payments\Helper\Data $vyneHelper,
        VyneCart $vyneCart,
        VyneOrder $vyneOrder,
        VyneLogger $vyneLogger
    ) {
        parent::__construct($context);

        $this->resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $this->checkoutSession = $checkoutSession;
        $this->orderRepository = $orderRepository;
        $this->vyneHelper = $vyneHelper;
        $this->vyneCart = $vyneCart;
        $this->vyneOrder = $vyneOrder;
        $this->vyneLogger = $vyneLogger;
    }

    /**
     * @inheritDoc
     */
    abstract public function execute();
}
