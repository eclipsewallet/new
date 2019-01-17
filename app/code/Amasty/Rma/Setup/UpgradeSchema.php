<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('amasty_amrma_request'),
                'last_show',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT]
            );
        }

        if ($context->getVersion() && version_compare($context->getVersion(), '1.2.3', '<')) {
            $setup->getConnection()->dropForeignKey(
                $setup->getTable('amasty_amrma_item'),
                $setup->getFkName(
                    'amasty_amrma_item',
                    'product_id',
                    'catalog_product_entity',
                    'entity_id'
                )
            );
        }

        $setup->endSetup();
    }
}
