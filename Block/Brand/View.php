<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Block\Brand;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Layer\Resolver;
use Zeta\ShopByBrand\Api\BrandRepositoryInterface;
use Zeta\ShopByBrand\Helper\Data as BrandHelper;
use Zeta\ShopByBrand\Model\Config;
use Zeta\ShopByBrand\Api\Data\BrandInterface;

class View extends AbstractProduct
{
    protected BrandRepositoryInterface $brandRepository;
    protected BrandHelper $brandHelper;
    protected Config $config;
    protected Resolver $layerResolver;
    private ?BrandInterface $brand = null;

    public function __construct(
        Context $context,
        BrandRepositoryInterface $brandRepository,
        BrandHelper $brandHelper,
        Config $config,
        Resolver $layerResolver,
        array $data = []
    ) {
        $this->brandRepository = $brandRepository;
        $this->brandHelper = $brandHelper;
        $this->config = $config;
        $this->layerResolver = $layerResolver;
        parent::__construct($context, $data);
    }

    public function getBrand(): ?BrandInterface
    {
        if ($this->brand === null) {
            $urlKey = $this->getRequest()->getParam('url_key');
            if ($urlKey) {
                try {
                    $this->brand = $this->brandRepository->getByUrlKey($urlKey);
                } catch (\Exception $e) {
                    return null;
                }
            }
        }
        return $this->brand;
    }

    public function getLoadedProductCollection()
    {
        return $this->_getProductCollection();
    }

    protected function _getProductCollection()
    {
        if ($this->_productCollection === null) {
            $layer = $this->layerResolver->get();
            $this->_productCollection = $layer->getProductCollection();
        }
        return $this->_productCollection;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $toolbar = $this->getToolbarBlock();
        if ($toolbar instanceof \Magento\Catalog\Block\Product\ProductList\Toolbar) {
            $toolbar->setCollection($this->_getProductCollection());
        }
        $this->_getProductCollection()->load();
        return $this;
    }
}
