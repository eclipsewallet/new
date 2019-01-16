<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_CustomerAttributes
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CustomerAttributes\Model\ResourceModel\Attribute\Address;

use Magento\Eav\Model\Config;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactory;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Swatches\Model\Swatch;
use Mageplaza\CustomerAttributes\Helper\Data;
use Psr\Log\LoggerInterface;

/**
 * Class Collection
 * @package Mageplaza\CustomerAttributes\Model\ResourceModel\Attribute\Address
 */
class Collection extends \Magento\Customer\Model\ResourceModel\Address\Attribute\Collection implements SearchResultInterface
{
    /**
     * @var \Magento\Framework\Api\Search\AggregationInterface
     */
    protected $aggregations;

    /**
     * @var \Magento\Framework\Api\Search\SearchCriteriaInterface
     */
    protected $searchCriteria;

    /**
     * @var int
     */
    protected $totalCount;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * Collection constructor.
     *
     * @param EntityFactory $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param Config $eavConfig
     * @param StoreManagerInterface $storeManager
     * @param Data $dataHelper
     * @param null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactory $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        Config $eavConfig,
        StoreManagerInterface $storeManager,
        Data $dataHelper,
        $connection = null,
        AbstractDb $resource = null
    )
    {
        $this->dataHelper = $dataHelper;

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $eavConfig, $storeManager, $connection, $resource);
    }

    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->addExcludeHiddenFrontendFilter()->addSystemHiddenFilter();

        $this->setOrder('sort_order', self::SORT_ORDER_ASC);

        return $this;
    }

    /**
     * @return void
     * @throws \Zend_Serializer_Exception
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        foreach ($this->_items as $item) {
            if ($item->getData('additional_data')) {
                $additionalData = $this->dataHelper->unserialize($item->getData('additional_data'));
                if (!empty($additionalData[Swatch::SWATCH_INPUT_TYPE_KEY])) {
                    $frontendInput = $item->getData('frontend_input') . '_' . $additionalData[Swatch::SWATCH_INPUT_TYPE_KEY];
                    $item->setData('frontend_input', $frontendInput);
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'frontend_input' && isset($condition['in'])) {
            foreach ($condition['in'] as &$cond) {
                $item = explode('_', $cond);
                if (isset($item[1]) && $item[1] == Swatch::SWATCH_INPUT_TYPE_VISUAL) {
                    $cond = $item[0];

                    $filter = ['like' => '%"' . Swatch::SWATCH_INPUT_TYPE_KEY . '":"' . Swatch::SWATCH_INPUT_TYPE_VISUAL . '"%'];
                    $this->addFieldToFilter('additional_data', ['like' => $filter]);
                }
            }
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * @return \Magento\Framework\Api\Search\AggregationInterface
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * @param \Magento\Framework\Api\Search\AggregationInterface $aggregations
     *
     * @return void
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }

    /**
     * @return \Magento\Framework\Api\Search\SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
        return $this->searchCriteria;
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $this->searchCriteria = $searchCriteria;

        return $this;
    }

    /**
     * @return int
     */
    public function getTotalCount()
    {
        if (!$this->totalCount) {
            $this->totalCount = $this->getSize();
        }

        return $this->totalCount;
    }

    /**
     * @param int $totalCount
     *
     * @return $this
     */
    public function setTotalCount($totalCount)
    {
        $this->totalCount = $totalCount;

        return $this;
    }

    /**
     * Set items list.
     *
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\Document[] $items
     *
     * @return $this
     * @throws \Exception
     */
    public function setItems(array $items = null)
    {
        if ($items) {
            foreach ($items as $item) {
                $this->addItem($item);
            }
            unset($this->totalCount);
        }

        return $this;
    }
}
