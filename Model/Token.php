<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vyne\Magento\Model;

use Magento\Framework\Model\AbstractModel;
use Vyne\Magento\Api\Data\TokenInterface;

class Token extends AbstractModel implements TokenInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Vyne\Magento\Model\ResourceModel\Token::class);
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * @inheritDoc
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getAccessToken()
    {
        return $this->getData(self::ACCESS_TOKEN);
    }

    /**
     * @inheritDoc
     */
    public function setAccessToken($access_token)
    {
        return $this->setData(self::ACCESS_TOKEN, $access_token);
    }

    /**
     * @inheritDoc
     */
    public function getMerchantId()
    {
        return $this->getData(self::MERCHANT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setMerchantId($merchant_id)
    {
        return $this->setData(self::MERCHANT_ID, $merchant_id);
    }

    /**
     * @inheritDoc
     */
    public function getScope()
    {
        return $this->getData(self::SCOPE);
    }

    /**
     * @inheritDoc
     */
    public function setScope($scope)
    {
        return $this->setData(self::SCOPE, $scope);
    }

    /**
     * @inheritDoc
     */
    public function getIss()
    {
        return $this->getData(self::ISS);
    }

    /**
     * @inheritDoc
     */
    public function setIss($iss)
    {
        return $this->setData(self::ISS, $iss);
    }

    /**
     * @inheritDoc
     */
    public function getMerchant()
    {
        return $this->getData(self::MERCHANT);
    }

    /**
     * @inheritDoc
     */
    public function setMerchant($merchant)
    {
        return $this->setData(self::MERCHANT, $merchant);
    }

    /**
     * @inheritDoc
     */
    public function getMfaRequired()
    {
        return $this->getData(self::MFA_REQUIRED);
    }

    /**
     * @inheritDoc
     */
    public function setMfaRequired($mfa_required)
    {
        return $this->setData(self::MFA_REQUIRED, $mfa_required);
    }

    /**
     * @inheritDoc
     */
    public function getTokenType()
    {
        return $this->getData(self::TOKEN_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setTokenType($token_type)
    {
        return $this->setData(self::TOKEN_TYPE, $token_type);
    }

    /**
     * @inheritDoc
     */
    public function getExpireIn()
    {
        return $this->getData(self::EXPIRE_IN);
    }

    /**
     * @inheritDoc
     */
    public function setExpireIn($expire_in)
    {
        return $this->setData(self::EXPIRE_IN, $expire_in);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($created_at)
    {
        return $this->setData(self::CREATED_AT, $created_at);
    }
}
