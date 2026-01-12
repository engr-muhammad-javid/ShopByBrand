<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Model\Layer;

use Magento\Catalog\Model\Layer\ItemCollectionProviderInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\Category;

class CollectionProvider implements ItemCollectionProviderInterface
{
    protected ProductCollectionFactory $productCollectionFactory;

    public function __construct(
        ProductCollectionFactory $productCollectionFactory
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
    }

    /**
     * {@inheritdoc}
     * Return product collection for a given category
     */
    public function getCollection($category): ProductCollection
    {
        if (!$category instanceof Category) {
            throw new \InvalidArgumentException('Category must be instance of Magento\Catalog\Model\Category');
        }

        $collection = $this->productCollectionFactory->create();
        $collection->addCategoryFilter($category);
        $collection->addAttributeToSelect('*');
        $collection->addMinimalPrice()
                   ->addFinalPrice()
                   ->addTaxPercents()
                   ->addUrlRewrite();

        return $collection;
    }
}
