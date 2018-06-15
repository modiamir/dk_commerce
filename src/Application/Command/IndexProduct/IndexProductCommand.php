<?php

namespace Digikala\Application\Command\IndexProduct;


use Elastica\Document;

class IndexProductCommand
{
    private $productId;

    public function __construct(int $productId)
    {
        $this->productId = $productId;
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }
}