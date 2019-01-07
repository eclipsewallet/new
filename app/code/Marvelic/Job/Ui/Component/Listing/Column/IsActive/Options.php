<?php

namespace Marvelic\Job\Ui\Component\Listing\Column\IsActive;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class IsActive
 */
class Options implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('ENABLED'),
                'value' => 1
            ],
            [
                'label' => __('DISABLED'),
                'value' => 0
            ],
        ];
    }
}
