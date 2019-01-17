<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\DB\FieldToConvert;
use Amasty\Rma\Model\ResourceModel\FieldDataConverterFactory;
use Amasty\Rma\Model\ResourceModel\QueryModifierFactory;

class RecurringData implements InstallDataInterface
{
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var FieldDataConverterFactory
     */
    private $fieldDataConverterFactory;

    /**
     * @var QueryModifierFactory
     */
    private $queryModifierFactory;

    /**
     * RecurringData constructor.
     *
     * @param ProductMetadataInterface  $productMetadata
     * @param FieldDataConverterFactory $fieldDataConverterFactory
     * @param QueryModifierFactory      $queryModifierFactory
     */
    public function __construct(
        ProductMetadataInterface $productMetadata,
        FieldDataConverterFactory $fieldDataConverterFactory,
        QueryModifierFactory $queryModifierFactory
    ) {
        $this->productMetadata = $productMetadata;
        $this->fieldDataConverterFactory = $fieldDataConverterFactory;
        $this->queryModifierFactory = $queryModifierFactory;
    }

    /**
     * Recurring data install
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($this->productMetadata->getVersion(), '2.2', '>=')) {
            $this->convertSerializedDataToJson($setup);
        }
    }

    /**
     * Convert serialized data to JSON
     *
     * @param ModuleDataSetupInterface $setup
     */
    public function convertSerializedDataToJson(ModuleDataSetupInterface $setup)
    {
        $fieldDataConverter = $this->fieldDataConverterFactory->create();
        $queryModifier = $this->queryModifierFactory->create();

        $fieldDataConverter->convert(
            [
                new FieldToConvert(
                    'Magento\Framework\DB\DataConverter\SerializedToJson',
                    $setup->getTable('core_config_data'),
                    'config_id',
                    'value',
                    $queryModifier->create(
                        'in',
                        [
                            'values' => [
                                'path' => [
                                    'amrma/properties/reasons'
                                ]
                            ]
                        ]
                    )
                ),
                new FieldToConvert(
                    'Magento\Framework\DB\DataConverter\SerializedToJson',
                    $setup->getTable('core_config_data'),
                    'config_id',
                    'value',
                    $queryModifier->create(
                        'in',
                        [
                            'values' => [
                                'path' => [
                                    'amrma/properties/conditions'
                                ]
                            ]
                        ]
                    )
                ),
                new FieldToConvert(
                    'Magento\Framework\DB\DataConverter\SerializedToJson',
                    $setup->getTable('core_config_data'),
                    'config_id',
                    'value',
                    $queryModifier->create(
                        'in',
                        [
                            'values' => [
                                'path' => [
                                    'amrma/properties/resolutions'
                                ]
                            ]
                        ]
                    )
                )
            ],
            $setup->getConnection()
        );
    }
}
