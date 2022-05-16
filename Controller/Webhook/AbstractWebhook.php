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
use Vyne\Magento\Model\Client\Transaction as VyneTransaction;
use Vyne\Magento\Api\TransactionRepositoryInterface;
use Vyne\Magento\Helper\Logger as VyneLogger;
use Vyne\Magento\Helper\Order as VyneOrder;

/**
 * Vyne Payment Webhook Order Controller
 *
 * @package Vyne\Magento\Controller\Decision
 */
abstract class AbstractWebhook extends Action implements HttpPostActionInterface, CsrfAwareActionInterface
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
        TransactionRepositoryInterface $transactionRepositoryInterface,
        VyneOrder $vyneOrder,
        VyneLogger $vyneLogger
    ) {
        parent::__construct($context);

        $this->transactionApi = $transactionApi;
        $this->transactionRepository = $transactionRepositoryInterface;
        $this->vyneOrder = $vyneOrder;
        $this->vyneLogger = $vyneLogger;
    }

    /**
     * @inheritDoc
     */
    abstract public function execute();

    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
