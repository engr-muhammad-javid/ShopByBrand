<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class Logo extends Column
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

        // Get default logo from config
        $defaultLogo = $this->scopeConfig->getValue(
            'zeta_shopbybrand/default_logo/image',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        foreach ($dataSource['data']['items'] as &$item) {
            $logoName = $item[$fieldName] ?? null;

            if ($logoName) {
                // Use actual brand logo
                $url = $mediaBaseUrl . 'zeta/brand/logo/' . $logoName;
                $alt = $item['name'] ?? $logoName;
            } else {
                // Use default logo from config
                if ($defaultLogo) {
                    $url = $mediaBaseUrl . 'zeta/brand/default/' . $defaultLogo;
                } else {
                    $url = $mediaBaseUrl . 'catalog/product/placeholder/image.jpg';
                }
                $logoName = 'Default Logo';
                $alt = 'Default';
            }

            $item[$fieldName . '_src'] = $url;
            $item[$fieldName . '_alt'] = $alt;
            $item[$fieldName . '_orig_src'] = $url;
        }

        return $dataSource;
    }
}