<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Vyne\Magento\Model\Client;

class Token extends Base
{
    /**
     * retrieve embed token for frotend checkout form
     *
     * @param string
     * @param string
     * @param string
     *
     * @return string
     */
    public function getToken($amount, $currency, $buyer_id)
    {
        try {
            $token_params = array();
            $this->vyneLogger->logMixed($embed_params);
        }
        catch (\Exception $e) {
            $this->vyneLogger->logException($e);
        }
    }
}
