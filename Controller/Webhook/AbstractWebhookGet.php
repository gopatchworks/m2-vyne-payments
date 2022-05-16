<?php

declare(strict_types=1);

namespace Vyne\Magento\Controller\Webhook;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Vyne\Magento\Helper\Logger as VyneLogger;
use Vyne\Magento\Helper\Cart as VyneCart;
use Vyne\Magento\Helper\Order as VyneOrder;

/**
 * Vyne Payment Webhook Order Controller
 *
 * @package Vyne\Magento\Controller\Decision
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
     * @var \Vyne\Magento\Helper\Data
     */
    protected $vyneHelper;

    /**
     * @var \Vyne\Magento\Helper\Cart
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

    public function __construct(
        Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        OrderRepositoryInterface $orderRepository,
        \Vyne\Magento\Helper\Data $vyneHelper,
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
