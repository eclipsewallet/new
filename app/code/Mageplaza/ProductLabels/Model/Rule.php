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

namespace Mageplaza\ProductLabels\Model;

use Magento\Backend\Model\Session;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Model\ResourceModel\Iterator;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Rule\Model\AbstractModel;
use Mageplaza\ProductLabels\Helper\Data as HelperData;

/**
 * Class Rule
 * @package Mageplaza\ProductLabels\Model
 */
class Rule extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'mageplaza_productlabels_rule';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'mageplaza_productlabels_rule';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_productlabels_rule';

    /**
     * Store matched product Ids
     *
     * @var array
     */
    protected $_productIds;

    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var Session
     */
    protected $_backendSession;

    /**
     * Store matched product Ids with rule id
     *
     * @var array
     */
    protected $dataProductIds;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Iterator
     */
    protected $resourceIterator;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * Rule constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param TimezoneInterface $localeDate
     * @param RequestInterface $request
     * @param Session $backendSession
     * @param ProductFactory $productFactory
     * @param CollectionFactory $productCollectionFactory
     * @param HelperData $helperData
     * @param Iterator $resourceIterator
     * @param AbstractDb|null $resourceCollection
     * @param AbstractResource|null $resource
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        TimezoneInterface $localeDate,
        RequestInterface $request,
        Session $backendSession,
        ProductFactory $productFactory,
        CollectionFactory $productCollectionFactory,
        HelperData $helperData,
        Iterator $resourceIterator,
        AbstractDb $resourceCollection = null,
        AbstractResource $resource = null
    )
    {
        $this->_request                 = $request;
        $this->_backendSession          = $backendSession;
        $this->productFactory           = $productFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->_helperData              = $helperData;
        $this->resourceIterator         = $resourceIterator;

        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mageplaza\ProductLabels\Model\ResourceModel\Rule');
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get array of product ids which are matched by rule
     *
     * @return array|null
     * @throws \Zend_Serializer_Exception
     */
    public function getMatchingProductIds()
    {
        if ($this->_productIds === null) {
            $data = $this->_request->getPost('rule');
            /** Fix filter grid error */
            if ($data) {
                $this->_backendSession->setProductLabelsData(['rule' => $data]);
            } else {
                $productLabelsData = $this->_backendSession->getProductLabelsData();
                $data              = $productLabelsData['rule'];
            }

            if (!$data) {
                $data = [];
            }

            $this->_productIds = [];
            $this->setCollectedAttributes([]);

            /** @var $productCollection \Magento\Catalog\Model\ResourceModel\Product\Collection */
            $productCollection = $this->productCollectionFactory->create();
            $productCollection
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
                ->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);

            $this->loadPost($data);
            $this->setConditionsSerialized($this->_helperData->serialize($this->getConditions()->asArray()));
            $this->getConditions()->collectValidatedAttributes($productCollection);

            $this->resourceIterator->walk(
                $productCollection->getSelect(),
                [[$this, 'callbackValidateProduct']],
                [
                    'attributes' => $this->getCollectedAttributes(),
                    'product'    => $this->productFactory->create(),
                ]
            );
        }

        return $this->_productIds;
    }

    /**
     * Callback function for product matching
     *
     * @param $args
     */
    public function callbackValidateProduct($args)
    {
        $product = clone $args['product'];
        $product->setData($args['row']);

        if ($this->getConditions()->validate($product)) {
            $this->_productIds[] = $product->getId();
        }
    }

    /**
     * Get rule condition combine model instance
     *
     * @return \Magento\CatalogRule\Model\Rule\Condition\Combine|\Magento\Rule\Model\Action\Collection|\Magento\Rule\Model\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->getActionsInstance();
    }

    /**
     * @return \Magento\CatalogRule\Model\Rule\Condition\Combine|\Magento\Rule\Model\Action\Collection
     */
    public function getActionsInstance()
    {
        return ObjectManager::getInstance()->create(\Magento\CatalogRule\Model\Rule\Condition\Combine::class);
    }
}