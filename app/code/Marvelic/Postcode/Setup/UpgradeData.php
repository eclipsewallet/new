<?php


namespace Marvelic\Postcode\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;

class UpgradeData implements UpgradeDataInterface
{

    private $customerSetupFactory;

    /**
     * Constructor
     *
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), "1.0.1", "<")) {
        
            $customerSetup->addAttribute(\Magento\Customer\Model\Indexer\Address\AttributeProvider::ENTITY, 'district', [
                'label' => 'Subdistrict',
                'input' => 'text',
                'type' => 'varchar',
                'source' => '',
                'required' => false,
                'position' => 333,
                'visible' => true,
                'system' => false,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'backend' => ''
            ]);
            
            $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'district')
            ->addData(['used_in_forms' => [
                    'adminhtml_customer_address',
                    'customer_address_edit',
                    'customer_register_address'
                ]
            ]);
            $attribute->save();

            $setup->getConnection()->addColumn(
                $setup->getTable('quote_address'),
                'district',
                [
                    'type' => 'text',
                    'length' => 255,
                    'comment' => 'Subdistrict',
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_address'),
                'district',
                [
                    'type' => 'text',
                    'length' => 255,
                    'comment' => 'Subdistrict',
                ]
            );
        }
    }
}
