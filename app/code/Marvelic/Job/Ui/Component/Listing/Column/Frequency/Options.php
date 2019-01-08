<?php
namespace Marvelic\Job\Ui\Component\Listing\Column\Frequency;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Options
 */
class Options implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            'NONE' => [
                'title' => __('None'),
                'label' => __('None (manual run only)'),
                'value' => 'NONE',
                'expr' => '',
            ],
            'MIN' => [
                'title' => __('Minute'),
                'label' => __('Every minute'),
                'value' => 'MIN',
                'expr' => '*/1 * * * *',
            ],
            'H' => [
                'title' => __('Hour'),
                'label' => __('Every hour'),
                'value' => 'H',
                'expr' => '* */1 * * *',
            ],
            'D' => [
                'title' => __('Day'),
                'label' => __('Every day at 3:00am'),
                'value' => 'D',
                'expr' => '0 3 * * *',
            ],
            'W' => [
                'title' => __('Week'),
                'label' => __('Every Monday at 3:00am'),
                'value' => 'W',
                'expr' => '0 3 * * 1',
            ],
            'M' => [
                'title' => __('Month'),
                'label' => __('Every 1st day of month at 3:00am'),
                'value' => 'M',
                'expr' => '0 3 1 * *',
            ],
            'C' => [
                'title' => __('Custom'),
                'label' => __('Custom'),
                'value' => 'C',
                'expr' => '* * * * *',
                'custom' => true
            ]
        ];
    }
}
