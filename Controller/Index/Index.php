<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Zeta\ShopByBrand\Model\Config;

class Index extends Action
{
    protected $resultPageFactory;
    protected $config;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Config $config
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->config = $config;
    }

    public function execute()
    {
        if (!$this->config->isEnabled()) {
            return $this->resultRedirectFactory->create()->setPath('noroute');
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Brands'));
        return $resultPage;
    }
}