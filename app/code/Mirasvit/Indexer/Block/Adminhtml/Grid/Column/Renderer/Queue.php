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


namespace Mirasvit\Indexer\Block\Adminhtml\Grid\Column\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Backend\Block\Context;
use Magento\Framework\DataObject;
use Magento\Indexer\Model\IndexerFactory;

class Queue extends AbstractRenderer
{
    /**
     * @var IndexerFactory
     */
    private $indexerFactory;

    public function __construct(
        IndexerFactory $indexerFactory,
        Context $context
    ) {
        $this->indexerFactory = $indexerFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function render(DataObject $row)
    {
        $indexer = $this->indexerFactory->create()->load($row->getIndexerId());
        /** @var \Magento\Indexer\Model\Indexer $row */
        $view = $indexer->getView();

        if (!$view) {
            return '<span class="' . $row->getId() . '">0</span>';
        }

        if (!$view->isEnabled()) {
            return '<span class="' . $row->getId() . '">0</span>';
        }

        $changelog = $view->getChangelog();

        $lastVersionId = $view->getState()->getVersionId();

        try {
            $currentVersionId = $view->getChangelog()->getVersion();
            $ids = $changelog->getList($lastVersionId, $currentVersionId);
        } catch (\Exception $e) {
            $ids = [];
        }

        return '<span class="' . $row->getId() . '">' . count($ids) . '</span>';
    }
}
