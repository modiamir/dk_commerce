<?php

namespace Digikala\Application\Query\SearchProducts;

class SearchProductsQuery
{
    /**
     * @var string
     */
    private $search;

    /**
     * @var float
     */
    private $priceFrom;

    /**
     * @var float
     */
    private $priceTo;

    /**
     * @var int
     */
    private $page;

    /**
     * @var string
     */
    private $color;

    public function __construct(
        string $search = null,
        float $priceFrom = null,
        float $priceTo = null,
        string $color = null,
        int $page = 1
    ) {
        $this->search = $search;
        $this->priceFrom = $priceFrom;
        $this->priceTo = $priceTo;
        $this->color = $color;
        $this->page = $page;
    }

    /**
     * @return string
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @return float
     */
    public function getPriceFrom()
    {
        return $this->priceFrom;
    }

    /**
     * @return float
     */
    public function getPriceTo()
    {
        return $this->priceTo;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }
}
