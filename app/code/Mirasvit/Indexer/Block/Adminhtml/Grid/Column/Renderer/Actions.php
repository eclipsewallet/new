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
use Magento\Framework\Url\Helper\Data as UrlHelper;
use Magento\Backend\Block\Context;
use Magento\Framework\DataObject;
use Magento\Indexer\Model\IndexerFactory;

class Actions extends AbstractRenderer
{
    /**
     * @var UrlHelper
     */
    private $urlHelper;

    /**
     * @var IndexerFactory
     */
    private $indexerFactory;

    public function __construct(
        UrlHelper $urlHelper,
        IndexerFactory $indexerFactory,
        Context $context,
        array $data = []
    ) {
        $this->urlHelper = $urlHelper;
        $this->indexerFactory = $indexerFactory;

        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function render(DataObject $row)
    {
        $indexer = $this->indexerFactory->create()->load($row->getIndexerId());
        /** @var \Magento\Indexer\Model\Indexer $row */

        /** @var \Magento\Indexer\Model\Indexer $row */
        $links[] = '<a href="'
            . $this->getUrl('mst_indexer/indexer/massScheduleFullReindex', ['indexer_ids' => $indexer->getId()])
            . '">' . __('Schedule Reindex') . '</a>';

        $links[] = '<a href="'
            . $this->getUrl('mst_indexer/indexer/massRunFullReindex', ['indexer_ids' => $indexer->getId()])
            . '">' . __('Run Reindex') . '</a>';

        return '<div class="' . $row->getIndexerId() . '">' . implode(' | ', $links) . '</div>';
    }
}
