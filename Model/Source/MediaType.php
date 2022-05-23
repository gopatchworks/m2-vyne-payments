<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Vyne\Magento\Model\Source;

class MediaType implements \Magento\Framework\Option\ArrayInterface
{
    const TYPE_URL = 'URL';
    const TYPE_QR = 'QR';

    public function toOptionArray()
    {
        return [
            ['value' => self::TYPE_URL, 'label' => __('URL')],
            //['value' => self::TYPE_QR, 'label' => __('QR')],
        ];
    }

    public function toArray()
    {
        return [
            self::TYPE_URL => __('URL'),
            //self::TYPE_QR => __('QR')
        ];
    }
}
