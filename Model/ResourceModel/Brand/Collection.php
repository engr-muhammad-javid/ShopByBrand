<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Model\ResourceModel\Brand;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Zeta\ShopByBrand\Model\Brand;
use Zeta\ShopByBrand\Model\ResourceModel\Brand as BrandResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(Brand::class, BrandResource::class);
    }

    public function addActiveFilter()
    {
        return $this->addFieldToFilter('is_active', 1);
    }

    public function addPositionOrder()
    {
        return $this->setOrder('position', 'ASC');
    }
}
