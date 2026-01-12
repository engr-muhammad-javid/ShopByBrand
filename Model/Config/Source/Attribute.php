<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Model\Config\Source;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory;
use Magento\Framework\Option\ArrayInterface;

class Attribute implements ArrayInterface
{
    protected $attributeCollectionFactory;

    public function __construct(CollectionFactory $attributeCollectionFactory)
    {
        $this->attributeCollectionFactory = $attributeCollectionFactory;
    }

    public function toOptionArray()
    {
        $options = [['value' => '', 'label' => __('-- Please Select --')]];

        $collection = $this->attributeCollectionFactory->create();
        $collection->addFieldToFilter('entity_type_id', 4)
            ->addFieldToFilter('frontend_input', 'select')
            ->setOrder('frontend_label', 'ASC');

        foreach ($collection as $attribute) {
            $options[] = [
                'value' => $attribute->getAttributeCode(),
                'label' => $attribute->getFrontendLabel()
            ];
        }

        return $options;
    }
}