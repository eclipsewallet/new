<?php


namespace Marvelic\Postcode\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;

class InstallData implements InstallDataInterface
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
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerSetup->addAttribute('customer_address', 'district', [
            'label' => 'Subdistrict',
            'input' => 'text',
            'type' => 'varchar',
            'source' => '',
            'required' => false,
            'position' => 333,
            'visible' => true,
            'system' => true,
            'is_used_in_grid' => false,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => false,
            'is_searchable_in_grid' => false,
            'backend' => ''
        ]);


        $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'district')
            ->addData(['used_in_forms' => [
                'customer_address_edit',
                'customer_register_address',
                'adminhtml_customer_address'
            ]]);
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
