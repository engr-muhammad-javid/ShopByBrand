<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Zeta\ShopByBrand\Api\BrandRepositoryInterface;
use Zeta\ShopByBrand\Model\BrandFactory;
use Zeta\ShopByBrand\Model\Config;
use Zeta\ShopByBrand\Model\ResourceModel\Brand as BrandResource;

class AttributeOptionSave implements ObserverInterface
{
    protected $config;
    protected $brandFactory;
    protected $brandResource;
    protected $brandRepository;

    public function __construct(
        Config $config,
        BrandFactory $brandFactory,
        BrandResource $brandResource,
        BrandRepositoryInterface $brandRepository
    ) {
        $this->config = $config;
        $this->brandFactory = $brandFactory;
        $this->brandResource = $brandResource;
        $this->brandRepository = $brandRepository;
    }

    public function execute(Observer $observer)
    {
        $attributeCode = $this->config->getBrandAttributeCode();
        if (!$attributeCode) {
            return $this;
        }

        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute->getAttributeCode() !== $attributeCode) {
            return $this;
        }

        $option = $observer->getEvent()->getOption();
        if (!$option || !isset($option['value'])) {
            return $this;
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
        return $this;
    }
}