<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Controller\Adminhtml\Brand;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\Product;
use Magento\Eav\Api\AttributeOptionManagementInterface;
use Magento\Eav\Model\Config as EavConfig;
use Zeta\ShopByBrand\Api\BrandRepositoryInterface;
use Zeta\ShopByBrand\Model\Config;

class Delete extends Action
{
    const ADMIN_RESOURCE = 'Zeta_ShopByBrand::brand_delete';
    protected $brandRepository;
    protected $config;
    protected $eavConfig;
    protected $attributeOptionManagement;

    public function __construct(
        Context $context,
        BrandRepositoryInterface $brandRepository,
        Config $config,
        EavConfig $eavConfig,
        AttributeOptionManagementInterface $attributeOptionManagement
    ) {
        parent::__construct($context);
        $this->brandRepository = $brandRepository;
        $this->config = $config;
        $this->eavConfig = $eavConfig;
        $this->attributeOptionManagement = $attributeOptionManagement;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');

        if (!$id) {
            $this->messageManager->addErrorMessage(__('We can\'t find a brand to delete.'));
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $brand = $this->brandRepository->getById($id);
            $optionId = $brand->getOptionId();

            $this->brandRepository->delete($brand);

            $attributeCode = $this->config->getBrandAttributeCode();
            if ($attributeCode && $optionId) {
                $attribute = $this->eavConfig->getAttribute(Product::ENTITY, $attributeCode);
                $this->attributeOptionManagement->delete(Product::ENTITY, $attribute->getAttributeId(), $optionId);
            }

            $this->messageManager->addSuccessMessage(__('Brand has been deleted.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('*/*/');
    }
}