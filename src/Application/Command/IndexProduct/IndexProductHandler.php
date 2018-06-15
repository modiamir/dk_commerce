<?php

namespace Digikala\Application\Command\IndexProduct;


use Digikala\Application\CommandHandlerInterface;
use Digikala\Elastic\ProductTransformer;
use Digikala\Repository\ProductRepository;
use Elastica\Document;
use Elastica\Type;
use Symfony\Component\Intl\DateFormatter\DateFormat\Transformer;

class IndexProductHandler implements CommandHandlerInterface
{
    /**
     * @var \Elastica\Type
     */
    private $productIndexProductType;

    /**
     * @var \Digikala\Repository\ProductRepository
     */
    private $productRepository;

    /**
     * @var \Digikala\Elastic\ProductTransformer
     */
    private $productTransformer;

    public function __construct(
        Type $productIndexProductType,
        ProductRepository $productRepository,
        ProductTransformer $productTransformer
    ) {
        $this->productIndexProductType = $productIndexProductType;
        $this->productRepository = $productRepository;
        $this->productTransformer = $productTransformer;
    }

    public function __invoke(IndexProductCommand $command)
    {
        /** @var \Digikala\Entity\Product $product */
        $product = $this->productRepository->find($command->getProductId());

        $document = $this->productTransformer->transform($product);

        $this->productIndexProductType->addDocument($document);
    }
}