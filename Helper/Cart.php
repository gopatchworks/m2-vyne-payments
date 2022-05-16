<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Vyne\Magento\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Vyne\Magento\Helper\Logger as VyneLogger;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Api\CustomerRepositoryInterface;

class Cart extends AbstractHelper
{
    /**
     * @var CustomerSession
     */
    protected $_customerSession;

    /**
     * @var Magento\Sales\Model\Order\Config
     */
    protected $_orderConfig;

    /**
     * @var Logger
     */
    protected $vyneLogger;

    /**
     * @var Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var Customer
     */
    private $customer = null;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param CustomerHelper $customerHelper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        CustomerSession $customerSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        Logger $vyneLogger
    ) {
        parent::__construct($context);
        $this->_customerSession = $customerSession;
        $this->_orderConfig = $orderConfig;
        $this->_orderFactory = $orderFactory;
        $this->vyneLogger = $vyneLogger;
    }

    /**
     * Check order view availability
     *
     * @param \Magento\Sales\Model\Order $order
     */
    protected function _canViewOrder($order)
    {
        $customerId = $this->_customerSession->getCustomerId();
        $availableStates = $this->_orderConfig->getVisibleOnFrontStatuses();
        if ($order->getId() && ($order->getCustomerId() == $customerId)
            && in_array($order->getState(), $availableStates, $strict = true)
            ) {
            return true;
        }
        return false;
    }

    /**
     * Try to load valid order by order_id and register it
     *
     * @param Integer $increment_id
     */
    protected function _loadValidOrder($increment_id = null)
    {
        if (!$increment_id) {
            return false;
        }

        $order = $this->_orderFactory->create()->load($increment_id);
        if ($this->_canViewOrder($order)) {
            return $order;
        }

        return false;
    }

    /**
     * rebuild cart content if payment failed
     *
     * @param Integer $last_order_id
     */
    public function reinitCart($last_order_id)
    {
        $order = $this->_loadValidOrder($last_order_id);
        if (!$order) {
            return;
        }
        $order->addStatusHistoryComment(__("Order #%1 cancelled.", $order->getIncrementId()));
        $order->setState('pending')->setStatus('pending')->save();

        $this->_orderManagement->cancel($order->getId());

        $items = $order->getItemsCollection();
        foreach ($items as $item) {
            try {
                $this->cart->addOrderItem($item);
            } catch (\Exception $e) {
                $this->messageManager->addNoticeMessage(__("Cannot add item '{$item->getName()}' to the shopping cart."));
                return false;
            }
        }

        $this->cart->save();
    }
}
