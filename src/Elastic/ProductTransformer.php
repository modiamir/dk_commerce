<?php

namespace Digikala\Elastic;


use Digikala\Entity\Product;
use Digikala\Entity\Variant;
use Elastica\Document;

class ProductTransformer implements DocumentToEntityTransformerInterface
{
    /**
     * @param Product $product
     *
     * @return \Elastica\Document
     */
    public function transform($product): Document
    {
        // The Id of the document
        $id = $product->getId();

        $variants = $product->getVariants()->map(function (Variant $variant) {
            return [
                'id' => $variant->getId(),
                'color' => $variant->getColor(),
                'price' => $variant->getPrice(),
            ];
        });
        // Create a document
        $productData = array(
            'id' => $id,
            'title' => $product->getTitle(),
            'description' => $product->getDescription(),
            'variants' => $variants->toArray(),
        );
        // First parameter is the id of document.
        $productDocument = new \Elastica\Document($id, $productData);

        return $productDocument;
    }
}