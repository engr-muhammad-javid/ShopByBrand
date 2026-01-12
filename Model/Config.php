<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    const XML_PATH_ENABLED = 'zeta_shopbybrand/general/enabled';
    const XML_PATH_BRAND_ATTRIBUTE = 'zeta_shopbybrand/general/brand_attribute';
    const XML_PATH_LISTING_SHOW_BRAND = 'zeta_shopbybrand/listing_page/show_brand';
    const XML_PATH_LISTING_LOGO_WIDTH = 'zeta_shopbybrand/listing_page/logo_width';
    const XML_PATH_LISTING_LOGO_HEIGHT = 'zeta_shopbybrand/listing_page/logo_height';
    const XML_PATH_PRODUCT_SHOW_BRAND = 'zeta_shopbybrand/product_page/show_brand';
    const XML_PATH_PRODUCT_LOGO_WIDTH = 'zeta_shopbybrand/product_page/logo_width';
    const XML_PATH_PRODUCT_LOGO_HEIGHT = 'zeta_shopbybrand/product_page/logo_height';
    const XML_PATH_DEFAULT_LOGO = 'zeta_shopbybrand/default_logo/image';

    protected $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function isEnabled($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getBrandAttributeCode($storeId = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_BRAND_ATTRIBUTE, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function isShowBrandOnListing($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_LISTING_SHOW_BRAND, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getListingPageLogoWidth($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_LISTING_LOGO_WIDTH, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getListingPageLogoHeight($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_LISTING_LOGO_HEIGHT, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function isShowBrandOnProduct($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_PRODUCT_SHOW_BRAND, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getProductPageLogoWidth($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_PRODUCT_LOGO_WIDTH, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getProductPageLogoHeight($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_PRODUCT_LOGO_HEIGHT, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getDefaultLogo($storeId = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_DEFAULT_LOGO, ScopeInterface::SCOPE_STORE, $storeId);
    }


}