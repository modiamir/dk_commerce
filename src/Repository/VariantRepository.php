<?php

namespace Digikala\Repository;

use Digikala\Entity\Variant;

class VariantRepository extends AbstractServiceRepository
{
    public function getEntityClass() : string
    {
        return Variant::class;
    }
}
