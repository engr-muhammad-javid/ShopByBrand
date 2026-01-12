<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class Image extends Column
{
    protected $storeManager;
    protected $scopeConfig;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    public function prepareDataSource(array $dataSource)
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        $fieldName = $this->getData('name');
        $mediaBaseUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);

        // Get default banner from config
        $defaultBanner = $this->scopeConfig->getValue(
            'zeta_shopbybrand/default_banner/image',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        foreach ($dataSource['data']['items'] as &$item) {
            $imageName = $item[$fieldName] ?? null;

            if ($imageName) {
                // Use actual brand image
                $url = $mediaBaseUrl . 'zeta/brand/image/' . $imageName;
                $alt = $item['name'] ?? $imageName;
            } else {
                // Use default banner from config
                if ($defaultBanner) {
                    $url = $mediaBaseUrl . 'zeta/brand/default/' . $defaultBanner;
                } else {
                    $url = $mediaBaseUrl . 'catalog/product/placeholder/image.jpg';
                }
                $imageName = 'Default Banner';
                $alt = 'Default';
            }

            $item[$fieldName . '_src'] = $url;
            $item[$fieldName . '_alt'] = $alt;
            $item[$fieldName . '_orig_src'] = $url;
        }

        return $dataSource;
    }
}