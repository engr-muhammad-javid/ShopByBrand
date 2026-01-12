<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Controller\Adminhtml\Brand;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\ImageUploader;
use Magento\Framework\Controller\ResultFactory;

class UploadLogo extends Action
{
    const ADMIN_RESOURCE = 'Zeta_ShopByBrand::brand_save';
    protected $imageUploader;

    public function __construct(Context $context, ImageUploader $imageUploader)
    {
        parent::__construct($context);
        $this->imageUploader = $imageUploader;
    }

    public function execute()
    {
        $imageId = $this->_request->getParam('param_name', 'logo');

        try {
            $result = $this->imageUploader->saveFileToTmpDir($imageId);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}