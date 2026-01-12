<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Controller\Adminhtml\Brand;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Api\AttributeOptionManagementInterface;
use Magento\Eav\Api\Data\AttributeOptionInterfaceFactory;
use Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory;
use Zeta\ShopByBrand\Api\BrandRepositoryInterface;
use Zeta\ShopByBrand\Model\Config;

class InlineEdit extends Action
{
    const ADMIN_RESOURCE = 'Zeta_ShopByBrand::brand_save';

    protected $jsonFactory;
    protected $brandRepository;
    protected $config;
    protected $eavConfig;
    protected $attributeOptionManagement;
    protected $attributeOptionFactory;
    protected $attributeOptionLabelFactory;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        BrandRepositoryInterface $brandRepository,
        Config $config,
        EavConfig $eavConfig,
        AttributeOptionManagementInterface $attributeOptionManagement,
        AttributeOptionInterfaceFactory $attributeOptionFactory,
        AttributeOptionLabelInterfaceFactory $attributeOptionLabelFactory
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->brandRepository = $brandRepository;
        $this->config = $config;
        $this->eavConfig = $eavConfig;
        $this->attributeOptionManagement = $attributeOptionManagement;
        $this->attributeOptionFactory = $attributeOptionFactory;
        $this->attributeOptionLabelFactory = $attributeOptionLabelFactory;
    }

    public function execute()
    {
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        $attributeCode = $this->config->getBrandAttributeCode();

        foreach (array_keys($postItems) as $brandId) {
            try {
                $brand = $this->brandRepository->getById($brandId);
                $brand->setData(array_merge($brand->getData(), $postItems[$brandId]));

                // Update EAV attribute option if name changed
                if ($attributeCode && isset($postItems[$brandId]['name']) && $brand->getOptionId()) {
                    $this->updateAttributeOption((int)$brand->getOptionId(), $postItems[$brandId]['name'], $attributeCode);
                }

                $this->brandRepository->save($brand);
            } catch (\Exception $e) {
                $messages[] = '[Brand ID: ' . $brand->getId() . '] ' . __($e->getMessage());
                $error = true;
            }
        }

        return $resultJson->setData(['messages' => $messages, 'error' => $error]);
    }

    /**
     * Update existing attribute option label
     */
    protected function updateAttributeOption(int $optionId, string $label, string $attributeCode): void
    {
        $attribute = $this->eavConfig->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $attributeCode);

        $optionLabel = $this->attributeOptionLabelFactory->create();
        $optionLabel->setStoreId(0);
        $optionLabel->setLabel($label);

        $option = $this->attributeOptionFactory->create();
        $option->setValue((string)$optionId);
        $option->setLabel($label);
        $option->setStoreLabels([$optionLabel]);

        $this->attributeOptionManagement->update(
            \Magento\Catalog\Model\Product::ENTITY,
            $attribute->getAttributeId(),
            $optionId,
            $option
        );
    }
}
