<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Model;

use Magento\Catalog\Model\Layer as MagentoLayer;
use Magento\Framework\Registry;
use Zeta\ShopByBrand\Model\Config;

class Layer extends MagentoLayer
{
    protected $registry;
    protected $config;

    public function __construct(
        \Magento\Catalog\Model\Layer\ContextInterface $context,
        \Magento\Catalog\Model\Layer\StateFactory $layerStateFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product $catalogProduct,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Registry $registry,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        Config $config
    ) {
        $this->registry = $registry;
        $this->config = $config;
        
        parent::__construct(
            $context,
            $layerStateFactory,
            $attributeCollectionFactory,
            $catalogProduct,
            $storeManager,
            $registry,
            $categoryRepository
        );
    }

    public function getCurrentBrand()
    {
        return $this->registry->registry('current_brand');
    }

    public function getCurrentCategory()
    {
        $category = $this->getData('current_category');
        if ($category === null) {
            try {
                $category = $this->categoryRepository->get(
                    $this->getCurrentStore()->getRootCategoryId()
                );
                $this->setData('current_category', $category);
            } catch (\Exception $e) {
                return null;
            }
        }
        return $category;
    }

    public function getProductCollection()
    {
        exit('herere');
        
        if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
            $collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
        } else {
            $collection = $this->collectionProvider->getCollection($this->getCurrentCategory());
            $this->prepareProductCollection($collection);
            $this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
        }
        return $collection;
    }

    public function prepareProductCollection($collection)
    {
        $brand = $this->getCurrentBrand();
        
        if ($brand && $brand->getOptionId()) {
            $attributeCode = $this->config->getBrandAttributeCode();
            if ($attributeCode) {
                $collection->addAttributeToFilter($attributeCode, $brand->getOptionId());
            }
        }

        $collection->addAttributeToSelect('*')
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addUrlRewrite()
            ->addStoreFilter();

        return $collection;
    }
}