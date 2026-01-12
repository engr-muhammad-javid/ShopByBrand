<?php
/**
 * FILE PATH: Block/Product/Brand.php
 * LOCATION: app/code/Zeta/ShopByBrand/Block/Product/Brand.php
 * PURPOSE: Block for displaying brand on product pages
 * FIXED: Constructor and parent class
 */
declare(strict_types=1);

namespace Zeta\ShopByBrand\Block\Product;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Framework\Exception\NoSuchEntityException;
use Zeta\ShopByBrand\Api\Data\BrandInterface;
use Zeta\ShopByBrand\Helper\Data as BrandHelper;
use Zeta\ShopByBrand\Model\Config;

class Brand extends AbstractProduct
{
    protected $brandHelper;
    protected $config;

    public function __construct(
        Context $context,
        BrandHelper $brandHelper,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->brandHelper = $brandHelper;
        $this->config = $config;
    }

    /**
     * Get brand for current product
     */
    public function getBrand(): ?BrandInterface
    {
        $product = $this->getProduct();
        
        if (!$product) {
            return null;
        }

        return $this->brandHelper->getBrandByProduct($product);
    }

    /**
     * Get brand URL
     */
    public function getBrandUrl(BrandInterface $brand): string
    {
        return $this->brandHelper->getBrandUrl($brand);
    }

    /**
     * Get brand logo URL
     */
    public function getBrandLogoUrl(BrandInterface $brand): string
    {
        return $this->brandHelper->getBrandLogoUrl($brand);
    }

    /**
     * Get brand image URL
     */
    public function getBrandImageUrl(BrandInterface $brand): string
    {
        return $this->brandHelper->getBrandImageUrl($brand);
    }

    /**
     * Check if module is enabled
     */
    public function isEnabled(): bool
    {
        return $this->config->isEnabled();
    }

    /**
     * Check if brand should be shown on product page
     */
    public function shouldShowOnProductPage(): bool
    {
        return $this->config->showBrandOnProductPage();
    }

    /**
     * Check if brand should be shown on listing page
     */
    public function shouldShowOnListingPage(): bool
    {
        return $this->config->showBrandOnListingPage();
    }

    /**
     * Get logo width for product page
     */
    public function getLogoWidth(): ?int
    {
        return $this->config->getProductPageLogoWidth();
    }

    /**
     * Get logo height for product page
     */
    public function getLogoHeight(): ?int
    {
        return $this->config->getProductPageLogoHeight();
    }

    /**
     * Get logo width for listing page
     */
    public function getListingLogoWidth(): ?int
    {
        return $this->config->getListingPageLogoWidth();
    }

    /**
     * Get logo height for listing page
     */
    public function getListingLogoHeight(): ?int
    {
        return $this->config->getListingPageLogoHeight();
    }
}