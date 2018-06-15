<?php

namespace Digikala\Console;

use Digikala\Elastic\ProductTransformer;
use Digikala\Entity\Product;
use Digikala\Repository\ProductRepository;
use Elastica\Type;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ElasticPopulateCommand extends ContainerAwareCommand
{
    const BATCH_SIZE = 1;

    /**
     * @var \Digikala\Repository\ProductRepository
     */
    private $productRepository;

    /**
     * @var \Elastica\Type
     */
    private $productIndexProductType;

    /**
     * @var \Digikala\Elastic\ProductTransformer
     */
    private $productTransformer;

    /**
     * @var \Elastica\Type\Mapping
     */
    private $productIndexProductTypeMapping;

    public function __construct(
        ProductRepository $productRepository,
        Type $productIndexProductType,
        Type\Mapping $productIndexProductTypeMapping,
        ProductTransformer $productTransformer
    ) {
        parent::__construct();
        $this->productRepository = $productRepository;
        $this->productIndexProductType = $productIndexProductType;
        $this->productTransformer = $productTransformer;
        $this->productIndexProductTypeMapping = $productIndexProductTypeMapping;
    }

    protected function configure()
    {
        $this
            ->setName('digikala:elastic:populate')
            ->setDescription('Populate products to elastic.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $count = $this->productRepository->count([]);
        $maxPage = ceil($count / self::BATCH_SIZE);

        $this->productIndexProductType->getIndex()->create($this->getContainer()->getParameter('elastic_index_product_config'), true);
        $this->productIndexProductTypeMapping->send();

        for ($i = 0; $i < $maxPage; $i ++) {
            /** @var Product[] $products */
            $products = $this->productRepository->findBy([], null, self::BATCH_SIZE, $i * self::BATCH_SIZE);
            $documents = [];
            foreach ($products as $product) {
                $document = $this->productTransformer->transform($product);
                $documents[] = $document;
            }
            $this->productIndexProductType->addDocuments($documents);
        }
    }
}