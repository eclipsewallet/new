<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 04/12/2018
 * Time: 16:33
 */

namespace Marvelic\Job\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Marvelic\Job\Ui\Component\Listing\Column\EntityType\Options;

class EntityType extends Column
{
    /**
     * @var Options
     */
    protected $_options;

    /**
     * ExportSource constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     * @param Options $options
     */
    public function __construct(ContextInterface $context,
                                UiComponentFactory $uiComponentFactory,
                                array $components = [],
                                array $data = [],
                                Options $options)
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->_options = $options;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')] = $this->prepareItem($item);
            }
        }

        return $dataSource;
    }

    /**
     * @param $item
     *
     * @return |null
     */
    protected function prepareItem($item)
    {
        $option = $this->_options->toArray();
        $result = $option[$item['entity']];

        return $result;
    }
}