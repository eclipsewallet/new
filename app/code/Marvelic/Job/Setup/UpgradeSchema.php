<?php

namespace Marvelic\Job\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Upgrade the extension
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    protected $eavSetup;

    /**
     * UpgradeSchema constructor.
     * @param EavSetup $eavSetup
     */
    public function __construct(
        EavSetup $eavSetup
    ) {
        $this->eavSetup = $eavSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->addFieldDateConfig($setup);
        }
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $this->updateFieldDateConfig($setup);
        }
    }

    public function addFieldDateConfig(SchemaSetupInterface $setup)
    {
        $setup->startSetup();
        $setup->getConnection()->addColumn(
            $setup->getTable('marvelic_job_export'),
            'date_config',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'Date config'
            ]
        );
        $setup->endSetup();
    }

    public function updateFieldDateConfig(SchemaSetupInterface $setup)
    {
        $setup->startSetup();
        $setup->getConnection()->changeColumn(
            'marvelic_job_export',
            'date_config',
            'date_config',
            ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT]
        );

        $setup->endSetup();
    }

}
