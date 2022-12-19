<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Vyne\Magento\Model\Source;

class ColourScheme implements \Magento\Framework\Option\ArrayInterface
{
    const COLOUR_LIGHT = 'Light';
    const COLOUR_DARK = 'Dark';

    public function toOptionArray()
    {
        return [
            ['value' => self::COLOUR_LIGHT, 'label' => __('Light')],
            ['value' => self::COLOUR_DARK, 'label' => __('Dark')],
        ];
    }

    public function toArray()
    {
        return [
            self::COLOUR_LIGHT => __('Light'),
            self::COLOUR_DARK => __('Dark')
        ];
    }
}
