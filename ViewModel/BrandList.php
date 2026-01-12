<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\ViewModel;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Zeta\ShopByBrand\Helper\Data as BrandHelper;
use Zeta\ShopByBrand\Model\ResourceModel\Brand\CollectionFactory;
use Zeta\ShopByBrand\Model\Config;

class BrandList implements ArgumentInterface
{
    protected $collectionFactory;
    protected $brandHelper;
    protected $coreRegistry;
    protected $config;

    public function __construct(
        CollectionFactory $collectionFactory,
        BrandHelper $brandHelper,
        Registry $coreRegistry,
        Config $config
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->brandHelper = $brandHelper;
        $this->coreRegistry = $coreRegistry;
        $this->config = $config;
    }

    public function getCurrentBrand()
    {
        return $this->coreRegistry->registry('current_brand');
    }

    public function getBrandLogoUrl($brand)
    {
        return $this->brandHelper->getBrandLogoUrl($brand);
    }

    public function getBrandImageUrl($brand)
    {
        return $this->brandHelper->getBrandImageUrl($brand);
    }

    public function getBrandUrl($brand)
    {
        return $this->brandHelper->getBrandUrl($brand);
    }

    public function getBrandAttributeCode(): string
    {
        return $this->config->getBrandAttributeCode() ?: '';
    }

    public function isEnabled(): bool
    {
        return $this->config->isEnabled();
    }
    
}