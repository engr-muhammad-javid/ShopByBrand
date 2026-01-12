<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Model\Brand;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Zeta\ShopByBrand\Model\ResourceModel\Brand\CollectionFactory;

class DataProvider extends AbstractDataProvider
{
    protected $collection;
    protected $dataPersistor;
    protected $storeManager;
    protected $loadedData;
    protected $request;

    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        StoreManagerInterface $storeManager,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->storeManager = $storeManager;
        $this->request = $request;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data for FORM
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $this->loadedData = [];

        // Get the brand ID from request
        $brandId = $this->request->getParam('id');

        if ($brandId) {
            // EDIT MODE - Load specific brand
            $brand = $this->collection
                ->addFieldToFilter('id', $brandId)
                ->getFirstItem();
            
            if ($brand->getId()) {
                $brandData = $brand->getData();
                
                // Prepare image field for form
                if (!empty($brandData['image'])) {
                    $imagePath = $brandData['image'];
                    unset($brandData['image']);
                    $brandData['image'][0] = [
                        'name' => $imagePath,
                        'url' => $this->getMediaUrl('zeta/brand/image/' . $imagePath)
                    ];
                }
                
                // Prepare logo field for form
                if (!empty($brandData['logo'])) {
                    $logoPath = $brandData['logo'];
                    unset($brandData['logo']);
                    $brandData['logo'][0] = [
                        'name' => $logoPath,
                        'url' => $this->getMediaUrl('zeta/brand/logo/' . $logoPath)
                    ];
                }
                
                // CRITICAL: Key by brand ID for form to work
                $this->loadedData[$brand->getId()] = $brandData;
            }
        }

        // Check if there's data in session (after validation failure)
        $data = $this->dataPersistor->get('zeta_brand');
        if (!empty($data)) {
            $brand = $this->collection->getNewEmptyItem();
            $brand->setData($data);
            $this->loadedData[$brand->getId()] = $brand->getData();
            $this->dataPersistor->clear('zeta_brand');
        }

        return $this->loadedData;
    }

    /**
     * Get media URL
     */
    private function getMediaUrl(string $path): string
    {
        try {
            return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $path;
        } catch (\Exception $e) {
            return '';
        }
    }
}