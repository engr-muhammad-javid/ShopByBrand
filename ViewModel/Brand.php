<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\ViewModel;

use Magento\Catalog\Model\Product;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Zeta\ShopByBrand\Helper\Data as BrandHelper;
use Zeta\ShopByBrand\Model\Config;

class Brand implements ArgumentInterface
{
    protected $config;
    protected $brandHelper;

    public function __construct(Config $config, BrandHelper $brandHelper)
    {
        $this->config = $config;
        $this->brandHelper = $brandHelper;
    }

    public function isEnabled()
    {
        return $this->config->isEnabled();
    }

    public function isShowBrandOnListing()
    {
        return $this->config->isShowBrandOnListing();
    }

    public function isShowBrandOnProduct()
    {
        return $this->config->isShowBrandOnProduct();
    }

    public function getBrandByProduct(Product $product)
    {
        return $this->brandHelper->getBrandByProduct($product);
    }

    public function getBrandLogoUrl($brand)
    {
        return $this->brandHelper->getBrandLogoUrl($brand);
    }

    public function getBrandUrl($brand)
    {
        return $this->brandHelper->getBrandUrl($brand);
    }

    public function getListingLogoWidth()
    {
        return $this->config->getListingLogoWidth();
    }

    public function getListingLogoHeight()
    {
        return $this->config->getListingLogoHeight();
    }

    public function getProductLogoWidth()
    {
        return $this->config->getProductLogoWidth();
    }

    public function getProductLogoHeight()
    {
        return $this->config->getProductLogoHeight();
    }
}