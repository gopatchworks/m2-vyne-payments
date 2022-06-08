<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Vyne\Magento\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Api\CustomerRepositoryInterface;

class Customer extends AbstractHelper
{
    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

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
        CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
    }

    /**
     * retrieve current customer from session
     *
     * @return customer
     */
    public function getCurrentCustomer()
    {
        if ($this->customer === null) {
            $customer = $this->customerSession->getCustomer();

            $this->customer = $customer;
        }

        return $this->customer;
    }

    /**
     * retrive current customer id
     *
     * @return int
     */
    public function getCurrentCustomerId()
    {
        $customer = $this->getCurrentCustomer();
        if (is_null($this->customer)) {
            return null;
        }

        return $customer->getId();
    }
}
