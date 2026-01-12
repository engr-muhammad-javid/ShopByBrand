<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Model\Config\Backend;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Zeta\ShopByBrand\Helper\Data as BrandHelper;

class BrandAttribute extends Value
{
    protected $brandHelper;

    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        BrandHelper $brandHelper,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->brandHelper = $brandHelper;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    public function afterSave()
    {
        if ($this->isValueChanged() && $this->getValue()) {
            $this->brandHelper->syncBrandsFromAttribute($this->getValue());
        }
        return parent::afterSave();
    }
}