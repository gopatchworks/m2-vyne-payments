<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vyne\Magento\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Vyne\Magento\Api\Data\TokenInterface;
use Vyne\Magento\Api\Data\TokenInterfaceFactory;
use Vyne\Magento\Api\Data\TokenSearchResultsInterfaceFactory;
use Vyne\Magento\Api\TokenRepositoryInterface;
use Vyne\Magento\Model\ResourceModel\Token as ResourceToken;
use Vyne\Magento\Model\ResourceModel\Token\CollectionFactory as TokenCollectionFactory;

class TokenRepository implements TokenRepositoryInterface
{

    /**
     * @var ResourceToken
     */
    protected $resource;

    /**
     * @var TokenInterfaceFactory
     */
    protected $tokenFactory;

    /**
     * @var TokenCollectionFactory
     */
    protected $tokenCollectionFactory;

    /**
     * @var Token
     */
    protected $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;


    /**
     * @param ResourceToken $resource
     * @param TokenInterfaceFactory $tokenFactory
     * @param TokenCollectionFactory $tokenCollectionFactory
     * @param TokenSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceToken $resource,
        TokenInterfaceFactory $tokenFactory,
        TokenCollectionFactory $tokenCollectionFactory,
        TokenSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->tokenFactory = $tokenFactory;
        $this->tokenCollectionFactory = $tokenCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(TokenInterface $token)
    {
        try {
            $this->resource->save($token);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the token: %1',
                $exception->getMessage()
            ));
        }
        return $token;
    }

    /**
     * @inheritDoc
     */
    public function get($tokenId)
    {
        $token = $this->tokenFactory->create();
        $this->resource->load($token, $tokenId);
        if (!$token->getId()) {
            throw new NoSuchEntityException(__('Token with id "%1" does not exist.', $tokenId));
        }
        return $token;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->tokenCollectionFactory->create();
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(TokenInterface $token)
    {
        try {
            $tokenModel = $this->tokenFactory->create();
            $this->resource->load($tokenModel, $token->getTokenId());
            $this->resource->delete($tokenModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Token: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($tokenId)
    {
        return $this->delete($this->get($tokenId));
    }

    /**
     * retrieve latest token and verify if it is valid using the expire_in field
     *
     * @return TokenInterface|null
     */
    public function getValidToken()
    {
        $collection = $this->tokenCollectionFactory->create();
        $collection->setOrder('created_at', 'DESC')->setPageSize(1)->setCurPage(1);
        $token = $collection->getFirstItem();
        if ($token && $token->getId()
            && $token->getExpireIn() > time() - strtotime($token->getCreatedAt())
        ) {
            return $token;
        }

        return null;
    }
}
