<?php


namespace Wiki\VAT\Setup;

use Magento\Framework\DB\Ddl\Table;
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
    )
    {
        $installer = $setup;
        $installer->startSetup();
        $connection = $installer->getConnection();
        $salesOrder = $installer->getTable('sales_order');

        $connection->addColumn(
                $salesOrder,
                'wk_vat_information',
                ['type' => Table::TYPE_TEXT, 'visible' => false, 'comment' => 'Vat Information']
            );

        $installer->endSetup();
    }
}
