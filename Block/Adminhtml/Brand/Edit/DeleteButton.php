<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Block\Adminhtml\Brand\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData()
    {
        $data = [];
        $brandId = $this->getBrandId();
        
        if ($brandId) {
            $data = [
                'label' => __('Delete Brand'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to delete this brand?'
                ) . '\', \'' . $this->getDeleteUrl() . '\', {"data": {}})',
                'sort_order' => 20,
            ];
        }
        
        return $data;
    }

    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['id' => $this->getBrandId()]);
    }
}