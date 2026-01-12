<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Zeta\ShopByBrand\Helper\Data as BrandHelper;

class ConfigSave implements ObserverInterface
{
    protected $brandHelper;

    public function __construct(BrandHelper $brandHelper)
    {
        $this->brandHelper = $brandHelper;
    }

    public function execute(Observer $observer)
    {
        return $this;
    }
}