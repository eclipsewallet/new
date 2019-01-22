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
 * @package   mirasvit/module-cache-warmer
 * @version   1.2.12
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\CacheWarmer\Setup\UpgradeSchema;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Mirasvit\CacheWarmer\Api\Data\PageInterface;
use Mirasvit\CacheWarmer\Api\Repository\PageRepositoryInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema1011 implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $connection = $setup->getConnection();
        $connection->addColumn(
            $setup->getTable(PageInterface::TABLE_NAME),
            PageInterface::STORE_ID,
            [
                'type'     => Table::TYPE_INTEGER,
                'nullable' => true,
                'unsigned' => false,
                'comment'  => PageInterface::STORE_ID,
                'after' => PageInterface::PRODUCT_ID,
            ]
        );
    }
}
