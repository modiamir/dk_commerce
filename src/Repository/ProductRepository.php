<?php

namespace Digikala\Repository;

use Digikala\Entity\Product;

class ProductRepository extends AbstractServiceRepository
{
    public function getEntityClass() : string
    {
        return Product::class;
    }

    public function findAndRefresh($id)
    {
        $product = $this->find($id);

        if ($product instanceof Product) {
            $this->_em->refresh($product);
        }

        return $product;
    }
}
