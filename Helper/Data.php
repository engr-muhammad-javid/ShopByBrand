<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Helper;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Zeta\ShopByBrand\Api\BrandRepositoryInterface;
use Zeta\ShopByBrand\Model\BrandFactory;
use Zeta\ShopByBrand\Model\Config;
use Zeta\ShopByBrand\Model\ResourceModel\Brand as BrandResource;

class Data extends AbstractHelper
{
    protected $config;
    protected $brandFactory;
    protected $brandResource;
    protected $brandRepository;
    protected $eavConfig;
    protected $storeManager;

    public function __construct(
        Context $context,
        Config $config,
        BrandFactory $brandFactory,
        BrandResource $brandResource,
        BrandRepositoryInterface $brandRepository,
        EavConfig $eavConfig,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->brandFactory = $brandFactory;
        $this->brandResource = $brandResource;
        $this->brandRepository = $brandRepository;
        $this->eavConfig = $eavConfig;
        $this->storeManager = $storeManager;
    }

    public function syncBrandsFromAttribute($attributeCode)
    {
        try {
            $attribute = $this->eavConfig->getAttribute(Product::ENTITY, $attributeCode);
            $options = $attribute->getSource()->getAllOptions();

            foreach ($options as $option) {
                if (empty($option['value'])) {
                    continue;
                }

                try {
                    $brand = $this->brandRepository->getByOptionId($option['value']);
                    $brand->setName($option['label']);
                } catch (NoSuchEntityException $e) {
                    $brand = $this->brandFactory->create();
                    $brand->setOptionId($option['value']);
                    $brand->setName($option['label']);
                    $brand->setUrlKey($this->brandResource->generateUrlKey($option['label']));
                    $brand->setIsActive(1);
                    $brand->setPosition(0);
                }

                $this->brandRepository->save($brand);
            }
        } catch (\Exception $e) {
            $this->_logger->error('Error syncing brands: ' . $e->getMessage());
        }
    }

    public function getBrandByProduct(Product $product)
    {
        $attributeCode = $this->config->getBrandAttributeCode();
        if (!$attributeCode || !$product->getData($attributeCode)) {
            return null;
        }

        try {
            return $this->brandRepository->getByOptionId($product->getData($attributeCode));
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    public function getBrandLogoUrl($brand)
    {
        if ($brand->getLogo()) {
            return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
                . 'zeta/brand/logo/' . $brand->getLogo();
        }

        $defaultLogo = $this->config->getDefaultLogo();
        if ($defaultLogo) {
            return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
                . 'zeta/brand/default/' . $defaultLogo;
        }

        return '';
    }

    public function getBrandImageUrl($brand)
    {
        if ($brand->getImage()) {
            return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
                . 'zeta/brand/image/' . $brand->getImage();
        }
        return '';
    }

    public function getBrandUrl($brand)
    {
        return $this->_urlBuilder->getUrl('brand/' . $brand->getUrlKey());
    }

    public function getAllBrandsUrl()
    {
        return $this->_urlBuilder->getUrl('brands');
    }
}