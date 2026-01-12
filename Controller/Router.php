<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Controller;

use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Zeta\ShopByBrand\Api\BrandRepositoryInterface;
use Zeta\ShopByBrand\Model\Config;

class Router implements RouterInterface
{
    protected $actionFactory;
    protected $brandRepository;
    protected $config;
    protected $response;

    public function __construct(
        ActionFactory $actionFactory,
        BrandRepositoryInterface $brandRepository,
        Config $config,
        ResponseInterface $response
    ) {
        $this->actionFactory = $actionFactory;
        $this->brandRepository = $brandRepository;
        $this->config = $config;
        $this->response = $response;
    }

    public function match(RequestInterface $request): ?ActionInterface
    {
        if (!$this->config->isEnabled()) {
            return null;
        }

        $identifier = trim($request->getPathInfo(), '/');

        if (preg_match('#^brand/([^/]+)/?$#', $identifier, $matches)) {
            $urlKey = $matches[1];

            try {
                $brand = $this->brandRepository->getByUrlKey($urlKey);
            
                if (!(bool)$brand->getIsActive()) {
                    return null;
                }

                $request->setModuleName('brand')
                    ->setControllerName('view')
                    ->setActionName('index')
                    ->setParam('url_key', $urlKey);

                return $this->actionFactory->create(
                    \Magento\Framework\App\Action\Forward::class
                );
            } catch (NoSuchEntityException $e) {
                return null;
            }
        }

        return null;
    }
}