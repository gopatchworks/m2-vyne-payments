<?php

declare(strict_types=1);

namespace Vyne\Payments\Controller\Webhook;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Vyne\Payments\Helper\Logger as VyneLogger;
use Vyne\Payments\Helper\Order as VyneOrder;

/**
 * Vyne Payment Webhook Order Controller
 *
 * @package Vyne\Payments\Controller\Decision
 */
abstract class AbstractWebhookPost extends Action implements HttpPostActionInterface, CsrfAwareActionInterface
{
    /**
     * @var vyneOrder
     */
    public $vyneOrder;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /*
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var VyneLogger
     */
    protected $vyneLogger;

    public function __construct(
        Context $context,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        VyneOrder $vyneOrder,
        VyneLogger $vyneLogger
    ) {
        parent::__construct($context);

        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
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
