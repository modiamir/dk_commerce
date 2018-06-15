<?php

namespace Digikala\Repository;

use Digikala\Entity\Product;

class ProductRepository extends AbstractServiceRepository
{
    public function getEntityClass() : string
    {
        return Product::class;
    }
}
