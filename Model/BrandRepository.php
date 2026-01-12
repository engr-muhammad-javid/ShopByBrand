<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Zeta\ShopByBrand\Api\BrandRepositoryInterface;
use Zeta\ShopByBrand\Api\Data\BrandInterface;
use Zeta\ShopByBrand\Model\BrandFactory;
use Zeta\ShopByBrand\Model\ResourceModel\Brand as BrandResource;
use Zeta\ShopByBrand\Model\ResourceModel\Brand\CollectionFactory;

class BrandRepository implements BrandRepositoryInterface
{
    protected $brandFactory;
    protected $brandResource;
    protected $collectionFactory;
    protected $searchResultsFactory;
    protected $collectionProcessor;

    public function __construct(
        BrandFactory $brandFactory,
        BrandResource $brandResource,
        CollectionFactory $collectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->brandFactory = $brandFactory;
        $this->brandResource = $brandResource;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    public function save(BrandInterface $brand)
    {
        try {
            $this->brandResource->save($brand);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $brand;
    }

    public function getById($brandId)
    {
        $brand = $this->brandFactory->create();
        $this->brandResource->load($brand, $brandId);
        if (!$brand->getId()) {
            throw new NoSuchEntityException(__('Brand with id "%1" does not exist.', $brandId));
        }
        return $brand;
    }

    public function getByUrlKey($urlKey)
    {
        $brand = $this->brandFactory->create();
        $this->brandResource->load($brand, $urlKey, 'url_key');
        if (!$brand->getId()) {
            throw new NoSuchEntityException(__('Brand with URL key "%1" does not exist.', $urlKey));
        }
        return $brand;
    }

    public function getByOptionId($optionId)
    {
        $brand = $this->brandFactory->create();
        $this->brandResource->load($brand, $optionId, 'option_id');
        if (!$brand->getId()) {
            throw new NoSuchEntityException(__('Brand with option ID "%1" does not exist.', $optionId));
        }
        return $brand;
    }

    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    public function delete(BrandInterface $brand)
    {
        try {
            $this->brandResource->delete($brand);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    public function deleteById($brandId)
    {
        return $this->delete($this->getById($brandId));
    }
}
