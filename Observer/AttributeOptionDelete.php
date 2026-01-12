<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Zeta\ShopByBrand\Api\BrandRepositoryInterface;
use Zeta\ShopByBrand\Model\Config;

class AttributeOptionDelete implements ObserverInterface
{
    protected $config;
    protected $brandRepository;

    public function __construct(
        Config $config,
        BrandRepositoryInterface $brandRepository
    ) {
        $this->config = $config;
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

        $optionId = $observer->getEvent()->getOptionId();
        if (!$optionId) {
            return $this;
        }

        try {
            $brand = $this->brandRepository->getByOptionId($optionId);
            $this->brandRepository->delete($brand);
        } catch (NoSuchEntityException $e) {
            // Brand doesn't exist
        }

        return $this;
    }
}