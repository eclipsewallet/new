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


namespace Mirasvit\Indexer\Service\Validator;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Indexer\Api\Service\ValidatorServiceInterface;
use Magento\Catalog\Model\Indexer\Product\Flat\State as FlatState;

class ProductFlat implements ValidatorServiceInterface
{
    /**
     * @var FlatState
     */
    private $flatState;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    public function __construct(
        FlatState $flatState,
        ResourceConnection $resource,
        StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository
    ) {
        $this->flatState = $flatState;
        $this->resource = $resource;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        if ($this->flatState->isFlatEnabled() == false) {
            return false;
        }

        $connection = $this->resource->getConnection();

        foreach ($this->storeManager->getStores() as $store) {
            $bind = [
                'website_id' => $store->getWebsiteId(),
            ];

            $flatTable = sprintf('%s_%s', $this->resource->getTableName('catalog_product_flat'), $store->getId());

            $select = $connection->select()
                ->from(
                    ['e' => $this->resource->getTableName('catalog_product_entity')],
                    ['entity_id', 'updated_at', 'type_id']
                )->join(
                    ['website' => $this->resource->getTableName('catalog_product_website')],
                    'e.entity_id = website.product_id AND website.website_id = :website_id',
                    []
                )->joinLeft(
                    ['flat' => $flatTable],
                    'e.entity_id = flat.entity_id',
                    ['updated_at']
                )->where('flat.updated_at <> e.updated_at OR flat.updated_at IS NULL')
                ->limit(100);

            $result = $connection->fetchAll($select, $bind);
            foreach ($result as $row) {
                $id = $row['entity_id'];

                try {
                    $product = $this->productRepository->getById($id);

                    if ($product->getStatus() == 1 && ($product->getVisibility() == Visibility::VISIBILITY_IN_SEARCH
                            || $product->getVisibility() == Visibility::VISIBILITY_BOTH
                            || $product->getVisibility() == Visibility::VISIBILITY_IN_CATALOG
                        )
                    ) {
                        echo $product->getSku() . PHP_EOL;
                        $product->save();
                    }
                } catch (\Exception $e) {
                }
            }
        }

        return true;
    }
}