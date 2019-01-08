<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 04/12/2018
 * Time: 11:31
 */

namespace Marvelic\Job\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class DateConfig extends Column
{
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
        $source = json_decode($item['date_config'], true);
        $result = $source['period']. " ". $source['type_time'];
        return $result;
    }
}