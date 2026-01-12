<?php
/**
 * FILE PATH: Block/Brand/ListBrand.php
 * LOCATION: app/code/Zeta/ShopByBrand/Block/Brand/ListBrand.php
 * PURPOSE: Block for brand listing page (optional - ViewModels are preferred)
 */
declare(strict_types=1);

namespace Zeta\ShopByBrand\Block\Brand;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Zeta\ShopByBrand\Api\BrandRepositoryInterface;
use Zeta\ShopByBrand\Helper\Data as BrandHelper;
use Zeta\ShopByBrand\Model\Config;

class ListBrand extends Template
{
    protected $brandRepository;
    protected $brandHelper;
    protected $config;
    protected $searchCriteriaBuilder;
    protected $sortOrderBuilder;
    private $brands = null;

    public function __construct(
        Context $context,
        BrandRepositoryInterface $brandRepository,
        BrandHelper $brandHelper,
        Config $config,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->brandRepository = $brandRepository;
        $this->brandHelper = $brandHelper;
        $this->config = $config;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * Get all active brands
     */
    public function getBrands(): array
    {
        if ($this->brands === null) {
            $sortOrder = $this->sortOrderBuilder
                ->setField('position')
                ->setDirection('ASC')
                ->create();

            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('is_active', 1)
                ->addSortOrder($sortOrder)
                ->create();

            $searchResults = $this->brandRepository->getList($searchCriteria);
            $this->brands = $searchResults->getItems();
        }

        return $this->brands;
    }

    /**
     * Get brand URL
     */
    public function getBrandUrl($brand): string
    {
        return $this->brandHelper->getBrandUrl($brand);
    }

    /**
     * Get brand logo URL
     */
    public function getBrandLogoUrl($brand): string
    {
        return $this->brandHelper->getBrandLogoUrl($brand);
    }

    /**
     * Check if module is enabled
     */
    public function isEnabled(): bool
    {
        return $this->config->isEnabled();
    }
}