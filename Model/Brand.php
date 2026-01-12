<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Model;

use Magento\Framework\Model\AbstractModel;
use Zeta\ShopByBrand\Api\Data\BrandInterface;

class Brand extends AbstractModel implements BrandInterface
{
    protected function _construct()
    {
        $this->_init(\Zeta\ShopByBrand\Model\ResourceModel\Brand::class);
    }

    public function getId() { return $this->getData(self::ID); }
    public function setId($id) { return $this->setData(self::ID, $id); }

    public function getName() { return $this->getData(self::NAME); }
    public function setName($name) { return $this->setData(self::NAME, $name); }

    public function getUrlKey() { return $this->getData(self::URL_KEY); }
    public function setUrlKey($urlKey) { return $this->setData(self::URL_KEY, $urlKey); }

    public function getImage() { return $this->getData(self::IMAGE); }
    public function setImage($image) { return $this->setData(self::IMAGE, $image); }

    public function getLogo() { return $this->getData(self::LOGO); }
    public function setLogo($logo) { return $this->setData(self::LOGO, $logo); }

    public function getShortDescription() { return $this->getData(self::SHORT_DESCRIPTION); }
    public function setShortDescription($shortDescription) { return $this->setData(self::SHORT_DESCRIPTION, $shortDescription); }

    public function getDescription() { return $this->getData(self::DESCRIPTION); }
    public function setDescription($description) { return $this->setData(self::DESCRIPTION, $description); }

    public function getMetaTitle() { return $this->getData(self::META_TITLE); }
    public function setMetaTitle($metaTitle) { return $this->setData(self::META_TITLE, $metaTitle); }

    public function getMetaDescription() { return $this->getData(self::META_DESCRIPTION); }
    public function setMetaDescription($metaDescription) { return $this->setData(self::META_DESCRIPTION, $metaDescription); }

    public function getMetaKeywords() { return $this->getData(self::META_KEYWORDS); }
    public function setMetaKeywords($metaKeywords) { return $this->setData(self::META_KEYWORDS, $metaKeywords); }

    public function getOptionId() { return $this->getData(self::OPTION_ID); }
    public function setOptionId($optionId) { return $this->setData(self::OPTION_ID, $optionId); }

    public function getIsActive() { return $this->getData(self::IS_ACTIVE); }
    public function setIsActive($isActive) { return $this->setData(self::IS_ACTIVE, $isActive); }

    public function getPosition() { return $this->getData(self::POSITION); }
    public function setPosition($position) { return $this->setData(self::POSITION, $position); }

    public function getCreatedAt() { return $this->getData(self::CREATED_AT); }
    public function setCreatedAt($createdAt) { return $this->setData(self::CREATED_AT, $createdAt); }

    public function getUpdatedAt() { return $this->getData(self::UPDATED_AT); }
    public function setUpdatedAt($updatedAt) { return $this->setData(self::UPDATED_AT, $updatedAt); }
}