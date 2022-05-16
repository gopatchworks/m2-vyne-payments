<?php

declare(strict_types=1);

namespace Vyne\Magento\Controller\Webhook;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Vyne\Magento\Model\Client\Transaction as VyneTransaction;
use Vyne\Magento\Api\TransactionRepositoryInterface;
use Vyne\Magento\Helper\Logger as VyneLogger;
use Vyne\Magento\Helper\Order as VyneOrder;

/**
 * Vyne Payment Webhook Order Controller
 *
 * @package Vyne\Magento\Controller\Decision
 */
abstract class AbstractWebhookGet extends Action
{
    /**
     * @var VyneTransaction
     */
    public $transactionApi;

    /**
     * @var TransactionRepositoryInterface
     */
    public $transactionRepository;

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
     * @var vyneOrder
     */
    public $vyneOrder;

    /**
     * @var VyneLogger
     */
    protected $vyneLogger;

    public function __construct(
        Context $context,
        VyneTransaction $transactionApi,
        \Magento\Checkout\Model\Session $checkoutSession,
        TransactionRepositoryInterface $transactionRepositoryInterface,
        OrderRepositoryInterface $orderRepository,
        \Vyne\Magento\Helper\Data $vyneHelper,
        VyneOrder $vyneOrder,
        VyneLogger $vyneLogger
    ) {
        parent::__construct($context);

        $this->resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $this->transactionApi = $transactionApi;
        $this->checkoutSession = $checkoutSession;
        $this->transactionRepository = $transactionRepositoryInterface;
        $this->orderRepository = $orderRepository;
        $this->vyneHelper = $vyneHelper;
        $this->vyneOrder = $vyneOrder;
        $this->vyneLogger = $vyneLogger;
    }

    /**
     * @inheritDoc
     */
    abstract public function execute();
}
