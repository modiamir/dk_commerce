<?php

namespace Digikala\Elastic;

use Digikala\Entity\Product;
use Doctrine\Common\Persistence\ObjectRepository;
use Elastica\Query;
use Elastica\Search;
use Elastica\Type;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ElasticProductRepository implements ObjectRepository
{
    const AVAILABLE_CRITERIA = [
        'id',
        'search',
        'color',
        'price',
    ];

    const PAGE_SIZE = 3;

    /**
     * @var \Elastica\Type
     */
    private $productIndexProductType;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(
        Type $productIndexProductType,
        EventDispatcherInterface $dispatcher
    ) {
        $this->productIndexProductType = $productIndexProductType;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Finds an object by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     *
     * @return object|null The object.
     */
    public function find($id)
    {
        $result = $this->search(['id' => $id], null, 1, 0);

        return $result;
    }

    /**
     * Finds all objects in the repository.
     *
     * @return array The objects.
     */
    public function findAll()
    {
        $result = $this->search();

        return $result;
    }

    /**
     * Finds objects by a set of criteria.
     *
     * Optionally sorting and limiting details can be passed. An implementation may throw
     * an UnexpectedValueException if certain values of the sorting or limiting details are
     * not supported.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return array The objects.
     *
     * @throws \UnexpectedValueException
     */
    public function findBy(array $criteria, array $orderBy = NULL, $limit = NULL, $offset = NULL)
    {
        return $this->search($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Finds a single object by a set of criteria.
     *
     * @param array $criteria The criteria.
     *
     * @return object|null The object.
     */
    public function findOneBy(array $criteria)
    {
        return $this->search($criteria, null, 1, 0);
    }

    /**
     * Returns the class name of the object managed by the repository.
     *
     * @return string
     */
    public function getClassName()
    {
        return Product::class;
    }

    private function createQuery(array $criteria = [], array $orderBy = NULL, $limit = NULL, $offset = NULL)
    {
        $query = new Query();

        $queryContext = [];
        $filterContext = [];

        foreach ($criteria as $key => $value) {
            if (empty($value)) {
                continue;
            }

            if (!in_array($key, self::AVAILABLE_CRITERIA)) {
                throw new \Exception('Invalid Search Criteria');
            }

            switch ($key) {
                case 'search':
                    $searchQuery = (new Query\BoolQuery())->addShould([
                        new Query\Match('title', $value),
                        new Query\Match('description', $value),
                    ]);
                    $queryContext[] = $searchQuery;
                    break;
                case 'price':
                    if (is_array($value)) {
                        $range = [];
                        if (isset($value['from'])) {
                            $range['gte'] = $value['from'];
                        }
                        if (isset($value['to'])) {
                            $range['lte'] = $value['to'];
                        }

                        if (count($range) > 0) {
                            $priceQuery = new Query\Range('variants.price', $range);

                            $filterContext[] = (new Query\Nested())
                                ->setPath('variants')
                                ->setQuery($priceQuery);
                        }
                    }
                    break;
                case 'color':
                    $colorQuery = new Query\Match('variants.color', $value);
                    $filterContext[] = (new Query\Nested())
                        ->setPath('variants')
                        ->setQuery($colorQuery);
            }

        }

        if (count($queryContext) > 0 || count($filterContext) > 0) {
            $boolQuery = new Query\BoolQuery();

            if (count($queryContext) > 0) {
                $boolQuery->addMust($queryContext);
            }

            if (count($filterContext) > 0) {
                $boolQuery->addFilter((new Query\BoolQuery())->addMust($filterContext));
            }

            $query->setQuery($boolQuery);
        }

        $query->setSize($limit);
        $query->setFrom($offset);

        return $query;
    }

    private function search(array $criteria = [], array $orderBy = NULL, $limit = NULL, $offset = NULL)
    {
        $beforeSearchEvent = new GetSearchResultEvent(
            $this->getClassName(),
            $criteria,
            $orderBy,
            $limit,
            $offset
        );

        $this->dispatcher->dispatch(GetSearchResultEvent::ELASTIC_SEARCH_BEFORE, $beforeSearchEvent);

        if ($beforeSearchEvent->getResult()) {
            return $beforeSearchEvent->getResult();
        }

        $query = $this->createQuery($criteria, $orderBy, $limit, $offset);

        $result = $this->productIndexProductType->search($query);

        $afterSearchEvent = new GetSearchResultEvent(
            $this->getClassName(),
            $criteria,
            $orderBy,
            $limit,
            $offset,
            $result
        );

        $this->dispatcher->dispatch(GetSearchResultEvent::ELASTIC_SEARCH_AFTER, $afterSearchEvent);

        return $result;
    }
}