<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Block;

use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\Layer\FilterList;
use Magento\Catalog\Model\Layer\AvailabilityFlagInterface;

class Navigation extends \Magento\LayeredNavigation\Block\Navigation
{
    public function __construct(
        Template\Context $context,
        Resolver $layerResolver,
        FilterList $filterList,
        AvailabilityFlagInterface $visibilityFlag,
        array $data = []
    ) {
        parent::__construct($context, $layerResolver, $filterList, $visibilityFlag, $data);
    }

    /**
     * Get layer object
     */
    public function getLayer()
    {
        // This will automatically get the 'brand' layer from resolver
        // based on the layout configuration
        return parent::getLayer();
    }
}