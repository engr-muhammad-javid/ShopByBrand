<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Api\Data;

interface BrandInterface
{
    const ID = 'id';
    const NAME = 'name';
    const URL_KEY = 'url_key';
    const IMAGE = 'image';
    const LOGO = 'logo';
    const SHORT_DESCRIPTION = 'short_description';
    const DESCRIPTION = 'description';
    const META_TITLE = 'meta_title';
    const META_DESCRIPTION = 'meta_description';
    const META_KEYWORDS = 'meta_keywords';
    const OPTION_ID = 'option_id';
    const IS_ACTIVE = 'is_active';
    const POSITION = 'position';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public function getId();
    public function setId($id);

    public function getName();
    public function setName($name);

    public function getUrlKey();
    public function setUrlKey($urlKey);

    public function getImage();
    public function setImage($image);

    public function getLogo();
    public function setLogo($logo);

    public function getShortDescription();
    public function setShortDescription($shortDescription);

    public function getDescription();
    public function setDescription($description);

    public function getMetaTitle();
    public function setMetaTitle($metaTitle);

    public function getMetaDescription();
    public function setMetaDescription($metaDescription);

    public function getMetaKeywords();
    public function setMetaKeywords($metaKeywords);

    public function getOptionId();
    public function setOptionId($optionId);

    public function getIsActive();
    public function setIsActive($isActive);

    public function getPosition();
    public function setPosition($position);

    public function getCreatedAt();
    public function setCreatedAt($createdAt);

    public function getUpdatedAt();
    public function setUpdatedAt($updatedAt);
}