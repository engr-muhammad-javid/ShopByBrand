<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Controller\Adminhtml\Brand;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    const ADMIN_RESOURCE = 'Zeta_ShopByBrand::brand';

    protected $resultPageFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Zeta_ShopByBrand::brands');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Brands'));
        
        return $resultPage;
    }
}