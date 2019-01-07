<?php


namespace Marvelic\Job\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $table_marvelic_job_export = $setup->getConnection()->newTable($setup->getTable('marvelic_job_export'));

        $table_marvelic_job_export->addColumn(
            'export_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'Entity ID'
        );

        $table_marvelic_job_export->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'title'
        );

        $table_marvelic_job_export->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['default' => '1','nullable' => False],
            'Is Job Active'
        );

        $table_marvelic_job_export->addColumn(
            'cron',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            [],
            'Cron schedule'
        );

        $table_marvelic_job_export->addColumn(
            'frequency',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            10,
            [],
            'frequency'
        );

        $table_marvelic_job_export->addColumn(
            'entity',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            [],
            'Entity Type'
        );

        $table_marvelic_job_export->addColumn(
            'behavior_data',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Behavior Data (json)'
        );

        $table_marvelic_job_export->addColumn(
            'export_source',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Export Source'
        );

        $table_marvelic_job_export->addColumn(
            'source_data',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Source Data (json)'
        );

        $table_marvelic_job_export->addColumn(
            'file_updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'File Updated At'
        );

        $setup->getConnection()->createTable($table_marvelic_job_export);
    }
}
