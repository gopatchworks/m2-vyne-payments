<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vyne\Payments\Api\Data;

interface TokenInterface
{

    const ID = 'id';
    const ACCESS_TOKEN = 'access_token';
    const MERCHANT_ID = 'merchant_id';
    const SCOPE = 'scope';
    const ISS = 'iss';
    const MERCHANT = 'merchant';
    const MFA_REQUIRED = 'mfa_required';
    const TOKEN_TYPE = 'token_type';
    const EXPIRE_IN = 'expire_in';
    const CREATED_AT = 'created_at';

    /**
     * Get id
     * @return int|null
     */
    public function getId();

    /**
     * Set id
     * @param int $id
     * @return \Vyne\Payments\Token\Api\Data\TokenInterface
     */
    public function setId($id);

    /**
     * Get access_token
     * @return string|null
     */
    public function getAccessToken();

    /**
     * Set access_token
     * @param string $access_token
     * @return \Vyne\Payments\Token\Api\Data\TokenInterface
     */
    public function setAccessToken($access_token);

    /**
     * Get merchant_id
     * @return string|null
     */
    public function getMerchantId();

    /**
     * Set merchant_id
     * @param string $merchant_id
     * @return \Vyne\Payments\Token\Api\Data\TokenInterface
     */
    public function setMerchantId($merchant_id);

    /**
     * Get scope
     * @return string|null
     */
    public function getScope();

    /**
     * Set scope
     * @param string $scope
     * @return \Vyne\Payments\Token\Api\Data\TokenInterface
     */
    public function setScope($scope);

    /**
     * Get iss
     * @return string|null
     */
    public function getIss();

    /**
     * Set iss
     * @param string $iss
     * @return \Vyne\Payments\Token\Api\Data\TokenInterface
     */
    public function setIss($iss);

    /**
     * Get merchant
     * @return string|null
     */
    public function getMerchant();

    /**
     * Set merchant
     * @param string $merchant
     * @return \Vyne\Payments\Token\Api\Data\TokenInterface
     */
    public function setMerchant($merchant);

    /**
     * Get mfa_required
     * @return boolean|null
     */
    public function getMfaRequired();

    /**
     * Set mfa_required
     * @param boolean $mfa_required
     * @return \Vyne\Payments\Token\Api\Data\TokenInterface
     */
    public function setMfaRequired($mfa_required);

    /**
     * Get token_type
     * @return string|null
     */
    public function getTokenType();

    /**
     * Set token_type
     * @param string $token_type
     * @return \Vyne\Payments\Token\Api\Data\TokenInterface
     */
    public function setTokenType($token_type);

    /**
     * Get expire_in
     * @return int|null
     */
    public function getExpireIn();

    /**
     * Set expire_in
     * @param int $expire_in
     * @return \Vyne\Payments\Token\Api\Data\TokenInterface
     */
    public function setExpireIn($expire_in);

    /**
     * Get created_at
     * @return datetime|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param datetime $created_at
     * @return \Vyne\Payments\Token\Api\Data\TokenInterface
     */
    public function setCreatedAt($created_at);
}
