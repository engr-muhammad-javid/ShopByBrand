<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Ui\DataProvider\Brand;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Zeta\ShopByBrand\Model\ResourceModel\Brand\CollectionFactory;

class ListingDataProvider extends AbstractDataProvider
{
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }

    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }
        
        return [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => array_values($this->getCollection()->toArray()['items'])
        ];
    }
}