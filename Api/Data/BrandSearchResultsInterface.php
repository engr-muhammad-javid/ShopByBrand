<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface BrandSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \Zeta\ShopByBrand\Api\Data\BrandInterface[]
     */
    public function getItems();

    /**
     * @param \Zeta\ShopByBrand\Api\Data\BrandInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}