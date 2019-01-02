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



namespace Mirasvit\Indexer\Plugin;

use Magento\Indexer\Block\Backend\Grid\Column\Renderer\Status as StatusRenderer;
use Magento\Indexer\Model\Indexer;
use Mirasvit\Indexer\Api\Data\StateInterface;
use Magento\Framework\DataObject;

class StatusRendererPlugin
{
    /**
     * @param StatusRenderer $block
     * @param \Closure $proceed
     * @param DataObject $row
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundRender(StatusRenderer $block, \Closure $proceed, DataObject $row)
    {
        /** @var Indexer $row */
        if ($row->getStatus() == StateInterface::STATUS_SCHEDULED) {
            return '<span class="grid-severity-major"><span>' . __('Reindex Scheduled') . '</span></span>';
        }

        return $proceed($row);
    }
}