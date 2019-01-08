<?php
namespace Marvelic\Job\Ui\Component\Listing\Column\Cron;

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
        $this->options = [
          ['label' => __('Minutes'), 'value' => ''],
          ['label' => __('Hours'), 'value' => ''],
          ['label' => __('Days'), 'value' => ''],
          ['label' => __('Months'), 'value' => ''],
          ['label' => __('Days of Week'), 'value' => '']
        ];

        return $this->options;
    }
}
