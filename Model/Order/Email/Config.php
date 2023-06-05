<?php

namespace Vyne\Magento\Model\Order\Email;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Config
{
    const XML_PATH_IDENT_SALES_NAME = 'trans_email/ident_sales/name';
    const XML_PATH_IDENT_SALES_EMAIL = 'trans_email/ident_sales/email';
    const XML_PATH_IDENT_SUPPORT_EMAIL = 'trans_email/ident_support/email';
    const XML_PATH_STORE_INFORMATION_PHONE = 'general/store_information/phone';
    const XML_PATH_STORE_INFORMATION_HOURS = 'general/store_information/hours';
    const XML_PATH_SALES_EMAIL_ASYNC_SENDING = 'sales_email/general/async_sending';
    const XML_PATH_SALES_EMAIL_SENDING_LIMIT = 'sales_email/general/sending_limit';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * Get Config
     *
     * @param string $path
     * @param string|int|null $storeId
     * @return bool
     */
    public function getConfig(string $path, $storeId = null)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Get store identifier
     *
     * @return  int
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * Get Store name
     *
     * @return string
     */
    public function getStoreName()
    {
        return $this->storeManager->getStore()->getName();
    }

    /**
     * Return Sales Sender Name
     *
     * @return mixed
     */
    public function getSalesSenderName()
    {
        return $this->getConfig(self::XML_PATH_IDENT_SALES_NAME, $this->getStoreId());
    }

    /**
     * Return Sales Sender Email
     *
     * @return mixed
     */
    public function getSalesSenderEmail()
    {
        return $this->getConfig(self::XML_PATH_IDENT_SALES_EMAIL, $this->getStoreId());
    }

    /**
     * Return Customer Support Email
     *
     * @return mixed
     */
    public function getCustomerSupportEmail()
    {
        return $this->getConfig(self::XML_PATH_IDENT_SUPPORT_EMAIL, $this->getStoreId());
    }

    /**
     * Return Store Phone
     *
     * @return mixed
     */
    public function getStorePhone()
    {
        return $this->getConfig(self::XML_PATH_STORE_INFORMATION_PHONE, $this->getStoreId());
    }

    /**
     * Return Store Hours
     *
     * @return mixed
     */
    public function getStoreHours()
    {
        return $this->getConfig(self::XML_PATH_STORE_INFORMATION_HOURS, $this->getStoreId());
    }

    /**
     * Return Is Async Sending Status
     *
     * @return bool
     */
    public function isAsyncSending()
    {
        return $this->getConfig(self::XML_PATH_SALES_EMAIL_ASYNC_SENDING, $this->getStoreId());
    }

    /**
     * Return Sales Email Sending Limit
     *
     * @return int
     */
    public function getSalesEmailSendingLimit()
    {
        return (int)$this->getConfig(self::XML_PATH_SALES_EMAIL_SENDING_LIMIT, $this->getStoreId());
    }
}
