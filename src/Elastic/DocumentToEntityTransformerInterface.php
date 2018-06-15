<?php

namespace Digikala\Elastic;

use Elastica\Document;

interface DocumentToEntityTransformerInterface
{
    public function transform($object): Document;
}