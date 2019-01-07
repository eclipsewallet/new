<?php

namespace Marvelic\Job\Ui\Component\Listing\Column\DateConfig;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Options
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
        $options = [
            [
                'label' => '-- Please Select --',
                'value' => ''
            ],
            [
                'label' => __('Hours'),
                'value' => 'hours'
            ],
            [
                'label' => __('Days'),
                'value' => 'days'
            ],
            [
                'label' => __('Week'),
                'value' => 'week'
            ],
            [
                'label' => __('Month'),
                'value' => 'month'
            ]
        ];

        return $options;

    }
}
