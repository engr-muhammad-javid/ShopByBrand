<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Zeta\ShopByBrand\Api\Data\BrandInterface;

interface BrandRepositoryInterface
{
    /**
     * @param BrandInterface $brand
     * @return BrandInterface
     */
    public function save(BrandInterface $brand);

    /**
     * @param int $brandId
     * @return BrandInterface
     */
    public function getById($brandId);

    /**
     * @param string $urlKey
     * @return BrandInterface
     */
    public function getByUrlKey($urlKey);

    /**
     * @param int $optionId
     * @return BrandInterface
     */
    public function getByOptionId($optionId);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Zeta\ShopByBrand\Api\Data\BrandSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param BrandInterface $brand
     * @return bool
     */
    public function delete(BrandInterface $brand);

    /**
     * @param int $brandId
     * @return bool
     */
    public function deleteById($brandId);
}