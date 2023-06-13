<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Vyne\Payments\Plugin\Magento\Sales\Model\Order\Email\Sender;

use Vyne\Payments\Helper\Data as VyneHelper;
use Vyne\Payments\Helper\Logger as VyneLogger;

class OrderSender
{
    /**
     * @var Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var VyneHelper
     */
    protected $helper;

    /**
     * @var VyneLogger
     */
    protected $logger;

    /**
     * @param VyneHelper $helper
     * @param VyneLogger $logger
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        VyneHelper $helper,
        VyneLogger $logger
    ) {
        $this->request = $request;
        $this->helper = $helper;
        $this->logger = $logger;
    }

    /**
     * only send notification email once by webhook callback
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return void
     */
    public function aroundSend(
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $subject,
        \Closure $proceed,
        \Magento\Sales\Model\Order $order,
        $forceSyncMode = false
    ) {
        $this->logger->logMixed(['method' => $order->getPayment()->getMethod()]);
        $result = false;

        if ($order->getPayment()->getMethod() == \Vyne\Payments\Model\Payment\Vyne::PAYMENT_METHOD_CODE) {
            if (!$order->getEmailSent()
                && $order->getPayment()
                && $order->getPayment()->getLastTransId()) {
                $result = $proceed($order, $forceSyncMode);
            }
        }
        else {
            $result = $proceed($order, $forceSyncMode);
        }

        return $result;
    }
}
