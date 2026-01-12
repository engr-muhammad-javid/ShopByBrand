<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Block\Adminhtml\Brand\Edit;

use Magento\Backend\Block\Widget\Context;
use Zeta\ShopByBrand\Api\BrandRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

abstract class GenericButton
{
    protected $context;
    protected $brandRepository;

    public function __construct(
        Context $context,
        BrandRepositoryInterface $brandRepository
    ) {
        $this->context = $context;
        $this->brandRepository = $brandRepository;
    }

    public function getBrandId()
    {
        try {
            return (int) $this->context->getRequest()->getParam('id');
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}