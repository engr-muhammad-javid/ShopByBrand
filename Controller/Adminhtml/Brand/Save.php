<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Controller\Adminhtml\Brand;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\Product;
use Magento\Eav\Api\AttributeOptionManagementInterface;
use Magento\Eav\Api\Data\AttributeOptionInterfaceFactory;
use Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Zeta\ShopByBrand\Api\BrandRepositoryInterface;
use Zeta\ShopByBrand\Model\BrandFactory;
use Zeta\ShopByBrand\Model\Config;
use Zeta\ShopByBrand\Model\ResourceModel\Brand as BrandResource;

class Save extends Action
{
    const ADMIN_RESOURCE = 'Zeta_ShopByBrand::brand_save';
    
    protected $dataPersistor;
    protected $brandFactory;
    protected $brandRepository;
    protected $brandResource;
    protected $config;
    protected $eavConfig;
    protected $attributeOptionManagement;
    protected $attributeOptionFactory;
    protected $attributeOptionLabelFactory;

    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        BrandFactory $brandFactory,
        BrandRepositoryInterface $brandRepository,
        BrandResource $brandResource,
        Config $config,
        EavConfig $eavConfig,
        AttributeOptionManagementInterface $attributeOptionManagement,
        AttributeOptionInterfaceFactory $attributeOptionFactory,
        AttributeOptionLabelInterfaceFactory $attributeOptionLabelFactory
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->brandFactory = $brandFactory;
        $this->brandRepository = $brandRepository;
        $this->brandResource = $brandResource;
        $this->config = $config;
        $this->eavConfig = $eavConfig;
        $this->attributeOptionManagement = $attributeOptionManagement;
        $this->attributeOptionFactory = $attributeOptionFactory;
        $this->attributeOptionLabelFactory = $attributeOptionLabelFactory;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }

        $id = (int)$this->getRequest()->getParam('id');

        if ($id > 0) {
            // UPDATE
            $brand = $this->brandRepository->getById($id);
        } else {
            // CREATE
            $brand = $this->brandFactory->create();
            $brand->setId(null);      // 🔥 CRITICAL
            unset($data['id']);       // 🔥 CRITICAL
        }


        try {
            // Load or create brand
            if ($id) {
                $brand = $this->brandRepository->getById($id);
            } else {
                $brand = $this->brandFactory->create();
            }

            // Process image uploads
            if (isset($data['image'][0]['name'])) {
                $data['image'] = $data['image'][0]['name'];
            } else {
                unset($data['image']);
            }

            if (isset($data['logo'][0]['name'])) {
                $data['logo'] = $data['logo'][0]['name'];
            } else {
                unset($data['logo']);
            }

            // Handle URL key
            if (empty($data['url_key'])) {
                $data['url_key'] = $this->brandResource->generateUrlKey($data['name'], $id);
            } else {
                if ($this->brandResource->isUrlKeyExists($data['url_key'], $id)) {
                    throw new LocalizedException(__('URL key already exists.'));
                }
            }

            // Ensure proper data types
            $data['position'] = isset($data['position']) ? (int)$data['position'] : 0;
            $data['is_active'] = isset($data['is_active']) ? (int)$data['is_active'] : 1;

            // Handle attribute option
            $attributeCode = $this->config->getBrandAttributeCode();
            if ($attributeCode) {
                if (!$id || !$brand->getOptionId()) {
                    // CREATE NEW OPTION
                    $optionId = $this->createAttributeOption($attributeCode, $data['name']);
                    $data['option_id'] = (int)$optionId;
                } else {
                    // UPDATE EXISTING OPTION
                    $this->updateAttributeOption(
                        $attributeCode, 
                        (int)$brand->getOptionId(), // CAST TO INT
                        $data['name']
                    );
                    $data['option_id'] = (int)$brand->getOptionId();
                }
            }

            // Set all data to brand
            $brand->setData($data);
            
            // Save brand - this is the critical part
            $this->brandRepository->save($brand);// Use resource model directly instead of repository
            
            $this->messageManager->addSuccessMessage(__('Brand has been saved.'));
            $this->dataPersistor->clear('zeta_brand');

            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $brand->getId()]);
            }

            return $resultRedirect->setPath('*/*/');
            
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the brand: ' . $e->getMessage()));
        }

        $this->dataPersistor->set('zeta_brand', $data);
        return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
    }

    /**
     * Create new attribute option
     */
    protected function createAttributeOption($attributeCode, $label): int
    {
        $attribute = $this->eavConfig->getAttribute(Product::ENTITY, $attributeCode);

        $optionLabel = $this->attributeOptionLabelFactory->create();
        $optionLabel->setStoreId(0);
        $optionLabel->setLabel($label);

        $option = $this->attributeOptionFactory->create();
        $option->setLabel($label);
        $option->setStoreLabels([$optionLabel]);
        $option->setSortOrder(0);
        $option->setIsDefault(false);

        $this->attributeOptionManagement->add(
            Product::ENTITY, 
            $attribute->getAttributeId(), 
            $option
        );

        // Reload attribute to get fresh options
        $attribute = $this->eavConfig->getAttribute(Product::ENTITY, $attributeCode);
        $this->eavConfig->clear(); // Clear EAV cache
        $attribute = $this->eavConfig->getAttribute(Product::ENTITY, $attributeCode);
        
        $options = $attribute->getSource()->getAllOptions();

        foreach ($options as $option) {
            if (trim((string)$option['label']) === trim($label)) {
                return (int)$option['value'];
            }
        }

        throw new LocalizedException(__('Failed to create attribute option.'));
    }

    /**
     * Update existing attribute option
     */
    protected function updateAttributeOption($attributeCode, int $optionId, $label): void
    {
        $attribute = $this->eavConfig->getAttribute(Product::ENTITY, $attributeCode);

        $optionLabel = $this->attributeOptionLabelFactory->create();
        $optionLabel->setStoreId(0);
        $optionLabel->setLabel($label);

        $option = $this->attributeOptionFactory->create();
        $option->setValue((string)$optionId); // Keep as string for setValue
        $option->setLabel($label);
        $option->setStoreLabels([$optionLabel]);

        // CRITICAL FIX: Cast $optionId to int for update method
        $this->attributeOptionManagement->update(
            Product::ENTITY, 
            $attribute->getAttributeId(), 
            $optionId, // Already int from parameter
            $option
        );
    }
}