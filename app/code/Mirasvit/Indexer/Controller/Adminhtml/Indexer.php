<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-indexer
 * @version   1.0.12
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Indexer\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Indexer\Model\ResourceModel\Indexer\State\CollectionFactory as StateCollectionFactory;
use Magento\Indexer\Model\Indexer\CollectionFactory as IndexerCollectionFactory;

abstract class Indexer extends Action
{
    /**
     * @var IndexerRegistry
     */
    protected $indexRegistry;

    /**
     * @var StateCollectionFactory
     */
    protected $stateCollectionFactory;

    /**
     * @var IndexerCollectionFactory
     */
    protected $indexerCollectionFactory;


    public function __construct(
        IndexerRegistry $indexerRegistry,
        StateCollectionFactory $stateCollectionFactory,
        IndexerCollectionFactory $indexerCollectionFactory,
        Action\Context $context
    ) {
        $this->indexRegistry = $indexerRegistry;
        $this->stateCollectionFactory = $stateCollectionFactory;
        $this->indexerCollectionFactory = $indexerCollectionFactory;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Indexer::index');
    }
}
