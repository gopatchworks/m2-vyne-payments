<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Vyne\Magento\Api\Data;

interface TokenInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const TOKEN_ID = 'id';
    const ACCESS_TOKEN = 'access_token';
    const TOKEN_TYPE = 'token_type';
    const EXPIRES_IN = 'expires_in';
    const SCOPE = 'scope';
    const MERCHANT_ID = 'merchant_id';
    const ISS = 'iss';
    const MFA_REQUIRED = 'mfa_required';

    /**
     * Get token_id
     * @return string|null
     */
    public function getTokenId();

    /**
     * Set token_id
     * @param string $tokenId
     * @return \Vyne\Magento\Api\Data\TokenInterface
     */
    public function setTokenId($tokenId);

    /**
     * Get access_token
     * @return string|null
     */
    public function getAccessToken();

    /**
     * Set access_token
     * @param string $access_token
     * @return \Vyne\Magento\Api\Data\TokenInterface
     */
    public function setAccessToken($access_token);

    /**
     * Get token_type
     * @return string|null
     */
    public function getTokenType();

    /**
     * Set token_type
     * @param string $token_type
     * @return \Vyne\Magento\Api\Data\TokenInterface
     */
    public function setTokenType($token_type);

    /**
     * Get expires_in
     * @return string|null
     */
    public function getExpiresIn();

    /**
     * Set expires_in
     * @param string $token_type
     * @return \Vyne\Magento\Api\Data\TokenInterface
     */
    public function setExpiresIn($expires_in);

    /**
     * Get scope
     * @return string|null
     */
    public function getScope();

    /**
     * Set scope
     * @param string $scope
     * @return \Vyne\Magento\Api\Data\TokenInterface
     */
    public function setScope($scope);

    /**
     * Get merchant_id
     * @return string|null
     */
    public function getMerchantId();

    /**
     * Set merchant_id
     * @param string $merchant_id
     * @return \Vyne\Magento\Api\Data\TokenInterface
     */
    public function setMerchantId($merchant_id);

    /**
     * Get iss
     * @return string|null
     */
    public function getIss();

    /**
     * Set iss
     * @param string $iss
     * @return \Vyne\Magento\Api\Data\TokenInterface
     */
    public function setIss($iss);

    /**
     * Get mfa_required
     * @return boolean
     */
    public function getMfaRequried();

    /**
     * Set mfa_required
     * @param boolean $mfa_required
     * @return \Vyne\Magento\Api\Data\TokenInterface
     */
    public function setMfaRequired($mfa_required);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Vyne\Magento\Api\Data\TokenExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Vyne\Magento\Api\Data\TokenExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Vyne\Magento\Api\Data\TokenExtensionInterface $extensionAttributes
    );
}
