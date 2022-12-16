<?php
namespace Vyne\Magento\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Cms\Api\BlockRepositoryInterface;

class Promotion extends \Magento\Framework\DataObject implements SectionSourceInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    protected $searchCriteriaBuilderFactory;

    /**
     * @var BlockRepositoryInterface
     */
    protected $blockRepository;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filter;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param BlockRepositoryInterface $blockRepository
     * @param \Magento\Cms\Model\Template\FilterProvider $filter
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        BlockRepositoryInterface $blockRepository,
        \Magento\Cms\Model\Template\FilterProvider $filter,
        \Magento\Framework\View\LayoutInterface $layout,
        array $data = []
    ) {
        parent::__construct($data);
        $this->customerSession = $customerSession;
        $this->resourceBlock = $resourceBlock;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->blockRepository = $blockRepository;
        $this->filter = $filter;
        $this->layout = $layout;
    }

    /**
     * @inheritdoc
     */
    public function getSectionData()
    {
        // if customer not logged in, force $customer_group_id to \Magento\Customer\Model\Group::NOT_LOGGED_IN_ID
        $customer_group_id = \Magento\Customer\Model\Group::NOT_LOGGED_IN_ID;
        if ($this->customerSession->getCustomer()->getId()) {
            $customer_group_id = $this->customerSession->getCustomer()->getGroupId();
        }

        return [
            'vyne-minicart' => $this->getVyneMinicartPromotion(),
            'customer_group_id' => $customer_group_id
        ];
    }

    /**
     * retrieve Vyne promotion content for minicart 
     *
     * @param integer $customer_group_id
     *
     * @return array
     */
    public function getVyneMinicartPromotion($customer_group_id = null)
    {
        $block_ids = $this->resourceBlock->lookupCmsBlockIds($customer_group_id);

        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder->addFilter('block_id', $block_ids, 'in')->create();
        $searchResults = $this->blockRepository->getList($searchCriteria);

        $result = array();
        foreach ($searchResults->getItems() as $block) {
            $result[$block->getId()] = $this->filter->getBlockFilter()->filter($block->getContent());
        }

        return $result;
    }
}
