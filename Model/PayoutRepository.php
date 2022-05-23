<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Vyne\Magento\Model;

use Vyne\Magento\Api\Data\PayoutInterfaceFactory;
use Vyne\Magento\Api\Data\PayoutSearchResultsInterfaceFactory;
use Vyne\Magento\Api\PayoutRepositoryInterface;
use Vyne\Magento\Model\ResourceModel\Payout as ResourcePayout;
use Vyne\Magento\Model\ResourceModel\Payout\CollectionFactory as PayoutCollectionFactory;
use Vyne\Magento\Model\Client\Token as VyneToken;
use Vyne\Magento\Helper\Logger as VyneLogger;
use Vyne\Magento\Helper\Data as VyneHelper;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class PayoutRepository implements PayoutRepositoryInterface
{

    protected $resource;

    protected $payoutFactory;

    protected $payoutCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataPayoutFactory;

    protected $extensionAttributesJoinProcessor;

    private $storeManager;

    private $collectionProcessor;

    protected $extensibleDataObjectConverter;

    /**
     * @var VyneLogger
     */
    protected $vyneLogger;

    /**
     * @var VyneHelper
     */
    protected $vyneHelper;

    /**
     * @var VyneToken
     */
    protected $vyneToken;

    /**
     * @var \Magento\Quote\Api\PaymentMethodManagementInterface
     */
    protected $paymentMethodManagement;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var searchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param ResourcePayout $resource
     * @param PayoutFactory $payoutFactory
     * @param PayoutInterfaceFactory $dataPayoutFactory
     * @param PayoutCollectionFactory $payoutCollectionFactory
     * @param PayoutSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param VyneLogger $vyneLogger
     * @param VyneHelper $vyneHelper
     * @param VyneToken $vyneToken
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement
     */
    public function __construct(
        ResourcePayout $resource,
        PayoutFactory $payoutFactory,
        PayoutInterfaceFactory $dataPayoutFactory,
        PayoutCollectionFactory $payoutCollectionFactory,
        PayoutSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        VyneLogger $vyneLogger,
        VyneHelper $vyneHelper,
        VyneToken $vyneToken,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement
    ) {
        $this->resource = $resource;
        $this->payoutFactory = $payoutFactory;
        $this->payoutCollectionFactory = $payoutCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPayoutFactory = $dataPayoutFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->vyneLogger = $vyneLogger;
        $this->vyneHelper = $vyneHelper;
        $this->vyneToken = $vyneToken;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Vyne\Magento\Api\Data\PayoutInterface $payout
    ) {
        /* if (empty($payout->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $payout->setStoreId($storeId);
        } */
        
        $payoutData = $this->extensibleDataObjectConverter->toNestedArray(
            $payout,
            [],
            \Vyne\Magento\Api\Data\PayoutInterface::class
        );
        
        $payoutModel = $this->payoutFactory->create()->setData($payoutData);
        
        try {
            $this->resource->save($payoutModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the payout: %1',
                $exception->getMessage()
            ));
        }
        return $payoutModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentInformation(
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Vyne\Magento\Api\Data\PayoutInterface $payoutData
    )
    {
        // 1. save payout data
        $this->save($payoutData);

        // 2. set payment information
        $quote = $this->getQuoteModel($cartId);
        $payment = $quote->getPayment();
        $payment->setData('vyne_payout_id', $payoutData->getVynePayoutId())->save();
        $this->vyneLogger->logMixed($payment->getData());

        $quote_payment_id = $this->paymentMethodManagement->set($cartId, $paymentMethod);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getToken($cartId)
    {
        $quote = $this->getQuoteModel($cartId);
        $currency = $quote->getStore()->getCurrentCurrency()->getCode();

        // NOTE: $quote->getGrandTotal after shipping method specified contains calculated shipping amount
        $quote_total = $quote->getGrandTotal();

        $result = array();
        $result['token'] = $this->VyneToken->getToken();
        $result['amount'] = $quote_total;

        return $result;
    }

    /**
     * retrieve fully loaded quote model to interact with Quote Properly
     *
     * @param string
     * @return \Magento\Quote\Model\Quote
     */
    private function getQuoteModel($cartId)
    {
        $cartId = $this->vyneHelper->getQuoteIdFromMask($cartId);
        /** @var \Magento\Quote\Api\CartRepositoryInterface $quoteRepository */
        $quoteRepository = $this->getCartRepository();

        /** @var \Magento\Quote\Model\Quote $quote */
        return $quoteRepository->getActive($cartId);
    }

    /**
     * retrieve shipping_address for current quote, return null if there is no shipping address (virtual or downloadable products)
     *
     * @return Magento\Quote\Model\Quote\Address|null
     */
    private function getShippingAddress($quote)
    {
        if ($quote->getShippingAddress()) {
            return $quote->getShippingAddress();
        }

        return null;
    }

    /**
     * Get Cart repository
     *
     * @return \Magento\Quote\Api\CartRepositoryInterface
     * @deprecated 100.2.0
     */
    private function getCartRepository()
    {
        if (!$this->cartRepository) {
            $this->cartRepository = ObjectManager::getInstance()
                ->get(\Magento\Quote\Api\CartRepositoryInterface::class);
        }
        return $this->cartRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function get($payoutId)
    {
        $payout = $this->payoutFactory->create();
        $this->resource->load($payout, $payoutId);
        if (!$payout->getId()) {
            throw new NoSuchEntityException(__('Payout with id "%1" does not exist.', $payoutId));
        }
        return $payout->getDataModel();
    }

    /**
     * retrieve buyer buy vyne payout using vyne_payout_id
     *
     * @param string
     * @return Vyne\Magento\Api\Data\PayoutInterface
     */
    public function getByVynePayoutId($vyne_payout_id)
    {
        $payoutSearchCriteria = $this->searchCriteriaBuilder->addFilter('vyne_payout_id', $vyne_payout_id, 'eq')->create();
        $payoutSearchResults = $this->getList($payoutSearchCriteria);

        if ($payoutSearchResults->getTotalCount() > 0) {
            list($item) = $payoutSearchResults->getItems();
            return $item;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->payoutCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Vyne\Magento\Api\Data\PayoutInterface::class
        );
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Vyne\Magento\Api\Data\PayoutInterface $payout
    ) {
        try {
            $payoutModel = $this->payoutFactory->create();
            $this->resource->load($payoutModel, $payout->getPayoutId());
            $this->resource->delete($payoutModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Payout: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($payoutId)
    {
        return $this->delete($this->get($payoutId));
    }
}

