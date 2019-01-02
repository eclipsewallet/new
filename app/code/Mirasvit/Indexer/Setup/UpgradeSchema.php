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


namespace Mirasvit\Indexer\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $installer->getConnection()->addColumn(
                $installer->getTable('mst_indexer_history'),
                'message',
                [
                    'type'     => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment'  => 'Message',
                ]
            );

            $installer->getConnection()->addColumn(
                $installer->getTable('mst_indexer_history'),
                'tick_at',
                [
                    'type'     => Table::TYPE_DATETIME,
                    'nullable' => true,
                    'comment'  => 'Tick At',
                ]
            );
        }
    }
}
