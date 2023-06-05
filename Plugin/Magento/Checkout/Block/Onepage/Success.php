<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Vyne\Magento\Plugin\Magento\Checkout\Block\Onepage;

use Vyne\Magento\Helper\Data as VyneHelper;
use Vyne\Magento\Helper\Logger as VyneLogger;

class Success
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var VyneHelper
     */
    protected $vyneHelper;

    /**
     * @var VyneLogger
     */
    protected $logger;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param VyneHelper $vyneHelper
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        VyneHelper $vyneHelper,
        VyneLogger $logger
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->orderRepository = $orderRepository;
        $this->vyneHelper = $vyneHelper;
        $this->logger = $logger;
    }

    /**
     * modify success template for Vyne payment only
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return void
     */
    public function aroundSetTemplate(
        \Magento\Checkout\Block\Onepage\Success $subject,
        \Closure $proceed,
        $template
    ) {
        $templates_to_override = ['Magento_Checkout::success.phtml', 'Magento_InventoryInStorePickupFrontend::success.phtml'];
        $order_id = $this->checkoutSession->getLastOrderId();
        $order = $this->orderRepository->get($order_id);
        $payment = $order->getPayment();
        if ($payment && $payment->getMethod() == \Vyne\Magento\Model\Payment\Vyne::PAYMENT_METHOD_CODE && in_array($template, $templates_to_override)) {
            $this->logger->logMixed(['Vyne aroundSetTemplate - Vyne payment method detected', $template]);
            $template = 'Vyne_Magento::checkout/success.phtml';
        }

        // default behavior
        return $proceed($template);
    }
}
