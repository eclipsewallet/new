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

namespace Mageplaza\CustomerAttributes\Ui\Component\Listing;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Mageplaza\CustomerAttributes\Model\ResourceModel\Attribute\CollectionFactory;
use Mageplaza\CustomerAttributes\Ui\Component\ColumnFactory;

/**
 * Class Columns
 * @package Mageplaza\CustomerAttributes\Ui\Component\Listing
 */
class Columns extends \Magento\Ui\Component\Listing\Columns
{
    /**
     * Default columns max order
     */
    const DEFAULT_COLUMNS_MAX_ORDER = 100;

    /**
     * @var int
     */
    protected $columnSortOrder;

    /**
     * @var array
     */
    protected $filterMap = [
        'default'            => 'text',
        'boolean'            => 'select',
        'select'             => 'select',
        'select_visual'      => 'select',
        'multiselect'        => 'select',
        'multiselect_visual' => 'select',
        'date'               => 'dateRange',
    ];

    /**
     * @var ColumnFactory
     */
    protected $columnFactory;

    /**
     * @var CollectionFactory
     */
    protected $attributeCollectionFactory;

    /**
     * Columns constructor.
     *
     * @param ContextInterface $context
     * @param ColumnFactory $columnFactory
     * @param CollectionFactory $attributeCollectionFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        ColumnFactory $columnFactory,
        CollectionFactory $attributeCollectionFactory,
        array $components = [],
        array $data = []
    )
    {
        $this->columnFactory = $columnFactory;
        $this->attributeCollectionFactory = $attributeCollectionFactory;

        parent::__construct($context, $components, $data);
    }

    /**
     * @return array
     */
    protected function getAttributeList()
    {
        $attributes = $this->attributeCollectionFactory->create();

        $result = [];
        foreach ($attributes as $attribute) {
            if (!$attribute->getIsUsedInSalesOrderGrid()) {
                continue;
            }

            $result[] = $attribute;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $columnSortOrder = self::DEFAULT_COLUMNS_MAX_ORDER;
        foreach ($this->getAttributeList() as $attribute) {
            $config = [];
            if (!isset($this->components[$attribute->getAttributeCode()])) {
                $config['sortOrder'] = ++$columnSortOrder;
                if ($attribute->getIsFilterableInGrid()) {
                    $config['filter'] = $this->getFilterType($attribute->getFrontendInput());
                }
                $column = $this->columnFactory->create($attribute, $this->getContext(), $config);
                $column->prepare();
                $this->addComponent($attribute->getAttributeCode(), $column);
            }
        }
        parent::prepare();
    }

    /**
     * Retrieve filter type by $frontendInput
     *
     * @param string $frontendInput
     *
     * @return string
     */
    protected function getFilterType($frontendInput)
    {
        return isset($this->filterMap[$frontendInput]) ? $this->filterMap[$frontendInput] : $this->filterMap['default'];
    }
}
