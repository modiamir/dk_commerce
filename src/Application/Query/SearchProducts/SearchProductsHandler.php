<?php

namespace Digikala\Application\Query\SearchProducts;

use Digikala\Application\QueryHandlerInterface;
use Digikala\Elastic\ElasticProductRepository;

class SearchProductsHandler implements QueryHandlerInterface
{
    /**
     * @var \Digikala\Elastic\ElasticProductRepository
     */
    private $elasticProductRepository;

    public function __construct(ElasticProductRepository $elasticProductRepository)
    {
        $this->elasticProductRepository = $elasticProductRepository;
    }

    public function __invoke(SearchProductsQuery $query)
    {
        $criteria = [];

        if ($query->getSearch()) {
            $criteria['search'] = $query->getSearch();
        }

        if ($query->getColor()) {
            $criteria['color'] = $query->getColor();
        }

        if ($query->getPriceFrom() || $query->getPriceTo()) {
            $criteria['price'] = [];
            if ($query->getPriceFrom()) {
                $criteria['price']['from'] = $query->getPriceFrom();
            }

            if ($query->getPriceTo()) {
                $criteria['price']['to'] = $query->getPriceTo();
            }
        }

        return $this->elasticProductRepository->findBy(
            $criteria,
            null,
            ElasticProductRepository::PAGE_SIZE,
            ($query->getPage() - 1) * ElasticProductRepository::PAGE_SIZE
        );

    }
}