<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Controller\Adminhtml\Brand;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Zeta\ShopByBrand\Api\BrandRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends Action
{
    const ADMIN_RESOURCE = 'Zeta_ShopByBrand::brand_save';
    
    protected $resultPageFactory;
    protected $brandRepository;
    protected $coreRegistry;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        BrandRepositoryInterface $brandRepository,
        Registry $coreRegistry
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->brandRepository = $brandRepository;
        $this->coreRegistry = $coreRegistry;
    }

    public function execute()
    {
        $id = (int) $this->getRequest()->getParam('id');
        $brand = null;

        if ($id) {
            try {
                $brand = $this->brandRepository->getById($id);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This brand no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        // Register brand for use in blocks if needed
        $this->coreRegistry->register('zeta_brand', $brand);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Zeta_ShopByBrand::brands');
        
        $title = $brand && $brand->getId() 
            ? __('Edit Brand: %1', $brand->getName()) 
            : __('New Brand');
            
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}