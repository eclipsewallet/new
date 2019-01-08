<?php

namespace Marvelic\Job\Ui\Component\Grid\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Frequency extends Column
{
    /**
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array              $components
     * @param array              $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            $frequencyModes = [
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
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$fieldName])) {
                    $mode = $item[$fieldName];
                    $value = $frequencyModes[$mode]['title'];
                    $item[$fieldName] = $value;
                }
            }
        }

        return $dataSource;
    }
}
