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

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        if ($installer->getConnection()->isTableExists($installer->getTable('mst_indexer_history'))) {
            $installer->getConnection()->dropTable($installer->getTable('mst_indexer_history'));
        }
        /**
         * Create table 'mst_indexer_history'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_indexer_history')
        )->addColumn(
            'history_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'History Id'
        )->addColumn(
            'started_at',
            Table::TYPE_DATETIME,
            null,
            ['nullable' => false],
            'Started at'
        )->addColumn(
            'execution_time',
            Table::TYPE_INTEGER,
            11,
            ['nullable' => true],
            'Execution time'
        )->addColumn(
            'indexer_id',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Action'
        )->addColumn(
            'summary',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Summary'
        )->setComment(
            'Indexer History'
        );

        $installer->getConnection()->createTable($table);
    }
}
