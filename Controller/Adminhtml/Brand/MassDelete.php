<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Controller\Adminhtml\Brand;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\Product;
use Magento\Eav\Api\AttributeOptionManagementInterface;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Zeta\ShopByBrand\Model\Config;
use Zeta\ShopByBrand\Model\ResourceModel\Brand\CollectionFactory;

class MassDelete extends Action
{
    const ADMIN_RESOURCE = 'Zeta_ShopByBrand::brand_delete';
    protected $filter;
    protected $collectionFactory;
    protected $config;
    protected $eavConfig;
    protected $attributeOptionManagement;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        Config $config,
        EavConfig $eavConfig,
        AttributeOptionManagementInterface $attributeOptionManagement
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->config = $config;
        $this->eavConfig = $eavConfig;
        $this->attributeOptionManagement = $attributeOptionManagement;
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();

        $attributeCode = $this->config->getBrandAttributeCode();
        $attribute = null;
        if ($attributeCode) {
            $attribute = $this->eavConfig->getAttribute(Product::ENTITY, $attributeCode);
        }

        foreach ($collection as $brand) {
            try {
                $optionId = $brand->getOptionId();
                $brand->delete();

                if ($attribute && $optionId) {
                    $this->attributeOptionManagement->delete(Product::ENTITY, $attribute->getAttributeId(), $optionId);
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $collectionSize));

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
