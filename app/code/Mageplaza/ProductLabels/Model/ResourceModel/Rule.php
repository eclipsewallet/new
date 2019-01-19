<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ProductLabels
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductLabels\Model\ResourceModel;

use Magento\Framework\DB\Select;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Rule\Model\ResourceModel\AbstractResource;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Rule
 * @package Mageplaza\ProductLabels\Model\ResourceModel
 */
class Rule extends AbstractResource
{
    /**
     * Date model
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Rule constructor.
     * @param Context $context
     * @param DateTime $date
     * @param StoreManagerInterface $storeManager
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        DateTime $date,
        StoreManagerInterface $storeManager,
        $connectionName = null
    )
    {
        $this->_date         = $date;
        $this->_storeManager = $storeManager;

        parent::__construct($context, $connectionName);
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageplaza_productlabels_rule', 'rule_id');
    }

    /**
     * @param null $ruleId
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getRuleById($ruleId = null)
    {
        $adapter = $this->getConnection();
        $select  = $adapter->select()->from($this->getMainTable())->where('rule_id = ?', $ruleId);

        return $adapter->fetchRow($select);
    }

    /**
     * Get RuleIds are running (doesn't include conditions)
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMatchingRuleIds()
    {
        $adapter = $this->getConnection();
        $storeId = $this->_storeManager->getStore()->getId();

        $select = $adapter->select()
            ->from($this->getMainTable())
            ->where('store_ids = 0 OR store_ids IN (?)', (int)$storeId)
            ->where('enabled = ?', 1)
            ->where('from_date IS NULL OR from_date <= ?', $this->_date->date())
            ->where('to_date IS NULL OR to_date >= ?', $this->_date->date())
            ->order('priority ' . Select::SQL_ASC);

        $ruleIds = array_unique($adapter->fetchCol($select));

        return $ruleIds;
    }

    /**
     * @param AbstractModel $object
     *
     * @return $this|AbstractResource
     */
    public function _beforeSave(AbstractModel $object)
    {
        /** save store Ids */
        if (is_array($object->getStoreIds())) {
            $object->setStoreIds(implode(',', $object->getStoreIds()));
        }

        if (is_array($object->getCustomerGroupIds())) {
            $object->setCustomerGroupIds(implode(',', $object->getCustomerGroupIds()));
        }

        if (is_array($object->getLabelImage())) {
            $object->setLabelImage($object->getLabelImage()['value']);
        }

        if (is_array($object->getListImage())) {
            $object->setListImage($object->getListImage()['value']);
        }

        return $this;
    }
}