<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Model\Layer;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Zeta\ShopByBrand\Model\Config;
use Magento\Catalog\Model\Layer\FilterableAttributeListInterface;

class FilterableAttributeList implements FilterableAttributeListInterface
{
    protected $collectionFactory;
    protected $storeManager;
    protected $config;

    public function __construct(
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        Config $config
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        $this->config = $config;
    }

    /**
     * Get list of filterable attributes - exclude brand attribute
     */
    public function getList()
    {
        $collection = $this->collectionFactory->create();
        $collection->setItemObjectClass(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::class)
                   ->addStoreLabel($this->storeManager->getStore()->getId())
                   ->setOrder('position', 'ASC');

        $collection = $this->_prepareAttributeCollection($collection);

        // Exclude brand attribute from filters
        $brandAttributeCode = $this->config->getBrandAttributeCode();
        if ($brandAttributeCode) {
            $collection->addFieldToFilter('attribute_code', ['neq' => $brandAttributeCode]);
        }

        $collection->load();

        return $collection;
    }

    protected function _prepareAttributeCollection($collection)
    {
        $collection->addIsFilterableFilter();
        return $collection;
    }
}
