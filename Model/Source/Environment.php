<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Vyne\Payments\Model\Source;

class Environment implements \Magento\Framework\Option\ArrayInterface
{
    const ENV_STAGING = 'staging';
    const ENV_PRD = 'production';

    public function toOptionArray()
    {
        return [
            ['value' => self::ENV_STAGING, 'label' => __('Staging')],
            ['value' => self::ENV_PRD, 'label' => __('Production')],
        ];
    }

    public function toArray()
    {
        return [
            self::ENV_STAGING => __('Staging'),
            self::ENV_PRD => __('Production')
        ];
    }
}
