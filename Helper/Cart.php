<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Vyne\Payments\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Vyne\Payments\Helper\Logger as VyneLogger;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;

class Cart extends AbstractHelper
{
    const ORDER_STATE_PENDING = 'pending';
    /**
     * @var CustomerSession
     */
    protected $_customerSession;

    /**
     * @var \Magento\Sales\Api\OrderManagementInterface 
     */
    protected $_orderManagement;

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
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var CustomerCart
     */
    protected $cart;

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
        \Magento\Sales\Api\OrderManagementInterface $orderManagement,
        \Magento\Sales\Model\Order\Config $orderConfig,
        CustomerCart $cart,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        Logger $vyneLogger
    ) {
        parent::__construct($context);
        $this->_customerSession = $customerSession;
        $this->_orderManagement = $orderManagement;
        $this->_orderConfig = $orderConfig;
        $this->cart = $cart;
        $this->_orderFactory = $orderFactory;
        $this->messageManager = $messageManager;
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
     * @param Integer $order_id
     */
    protected function _loadValidOrder($order_id = null)
    {
        if (!$order_id) {
            return false;
        }

        $order = $this->_orderFactory->create()->load($order_id);
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
        $order->setState(self::ORDER_STATE_PENDING)->setStatus(self::ORDER_STATE_PENDING)->save();

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
