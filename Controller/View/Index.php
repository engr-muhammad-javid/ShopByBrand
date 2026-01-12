<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Controller\View;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Zeta\ShopByBrand\Api\BrandRepositoryInterface;
use Zeta\ShopByBrand\Model\Config;
use Magento\Framework\Exception\LocalizedException;

class Index extends Action
{
    protected PageFactory $resultPageFactory;
    protected BrandRepositoryInterface $brandRepository;
    protected Registry $coreRegistry;
    protected Config $config;
    protected LayerResolver $layerResolver;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        BrandRepositoryInterface $brandRepository,
        Registry $coreRegistry,
        Config $config,
        LayerResolver $layerResolver
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->brandRepository = $brandRepository;
        $this->coreRegistry = $coreRegistry;
        $this->config = $config;
        $this->layerResolver = $layerResolver;
    }

    public function execute()
    {
        if (!$this->config->isEnabled()) {
            return $this->resultRedirectFactory->create()->setPath('noroute');
        }

        $urlKey = (string)$this->getRequest()->getParam('url_key');
        if (!$urlKey) {
            return $this->resultRedirectFactory->create()->setPath('noroute');
        }

        try {
            $brand = $this->brandRepository->getByUrlKey($urlKey);

            if (!$brand->getIsActive()) {
                throw new LocalizedException(__('Brand is disabled'));
            }


            // Register the current brand for Layer & Block
            $this->coreRegistry->register('current_brand', $brand);

            // Initialize Brand Layer
            $this->layerResolver->create('brand');

            

            $resultPage = $this->resultPageFactory->create();

            // SEO
            $resultPage->getConfig()->getTitle()->set(
                $brand->getMetaTitle() ?: $brand->getName()
            );

            if ($brand->getMetaDescription()) {
                $resultPage->getConfig()->setDescription($brand->getMetaDescription());
            }

            if ($brand->getMetaKeywords()) {
                $resultPage->getConfig()->setKeywords($brand->getMetaKeywords());
            }

            return $resultPage;

        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $this->resultRedirectFactory->create()->setPath('noroute');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong.'));
            return $this->resultRedirectFactory->create()->setPath('noroute');
        }
    }
}
