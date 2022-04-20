<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Vyne\Magento\Model\Source;

use Vyne\Magento\Model\Payment\Vyne;

class PaymentAction implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => Vyne::PAYMENT_TYPE_AUTH, 'label' => __('Authorize Only')],
            ['value' => Vyne::PAYMENT_TYPE_AUCAP, 'label' => __('Authorize & Capture')]
        ];
    }
}
