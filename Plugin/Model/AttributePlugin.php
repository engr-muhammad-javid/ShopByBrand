<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Plugin\Model;

use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\Exception\NoSuchEntityException;
use Zeta\ShopByBrand\Api\BrandRepositoryInterface;
use Zeta\ShopByBrand\Model\BrandFactory;
use Zeta\ShopByBrand\Model\Config;
use Zeta\ShopByBrand\Model\ResourceModel\Brand as BrandResource;
use Zeta\ShopByBrand\Model\ResourceModel\Brand\CollectionFactory as BrandCollectionFactory;

class AttributePlugin
{
    public function __construct(
        private readonly Config $config,
        private readonly BrandFactory $brandFactory,
        private readonly BrandResource $brandResource,
        private readonly BrandCollectionFactory $brandCollectionFactory,
        private readonly BrandRepositoryInterface $brandRepository
    ) {}

    public function afterSave(Attribute $subject, $result)
    {
        $attributeCode = $this->config->getBrandAttributeCode();
        if (!$attributeCode || $subject->getAttributeCode() !== $attributeCode) {
            return $result;
        }

        /** -----------------------------
         *  CURRENT ATTRIBUTE OPTIONS
         * ----------------------------- */
        $currentOptions = [];
        foreach ($subject->getSource()->getAllOptions(false) as $option) {
            if (!empty($option['value'])) {
                $currentOptions[(int)$option['value']] = trim((string)$option['label']);
            }
        }

        /** -----------------------------
         *  EXISTING BRANDS
         * ----------------------------- */
        $existingBrands = [];
        $brandCollection = $this->brandCollectionFactory->create();
        foreach ($brandCollection as $brand) {
            $existingBrands[(int)$brand->getOptionId()] = $brand;
        }

        /** -----------------------------
         *  CREATE / UPDATE
         * ----------------------------- */
        foreach ($currentOptions as $optionId => $label) {
            if (isset($existingBrands[$optionId])) {
                $brand = $existingBrands[$optionId];
                if ($brand->getName() !== $label) {
                    $brand->setName($label);
                    $this->brandRepository->save($brand);
                }
            } else {
                $brand = $this->brandFactory->create();
                $brand->setOptionId($optionId);
                $brand->setName($label);
                $brand->setUrlKey(
                    $this->brandResource->generateUrlKey($label)
                );
                $brand->setIsActive(1);
                $brand->setPosition(0);
                $this->brandRepository->save($brand);
            }
        }

        /** -----------------------------
         *  DELETE REMOVED OPTIONS
         * ----------------------------- */
        foreach ($existingBrands as $optionId => $brand) {
            if (!isset($currentOptions[$optionId])) {
                $this->brandRepository->delete($brand);
            }
        }

        return $result;
    }
}
