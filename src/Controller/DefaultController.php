<?php

namespace Digikala\Controller;

use Digikala\Application\Query\SearchProducts\SearchProductsQuery;
use Digikala\Elastic\ElasticProductRepository;
use League\Tactician\CommandBus;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DefaultController extends Controller
{
    /**
     * @throws \Exception
     */
    public function loginCheck()
    {
        throw new \Exception('');
    }

    public function index(CommandBus $queryBus, Request $request)
    {
        $query = new SearchProductsQuery(
            $request->query->get('search'),
            (float) $request->query->get('pricefrom'),
            (float) $request->query->get('priceto'),
            $request->query->get('color'),
            (int) $request->query->get('page', 1)
        );
        $products = $queryBus->handle($query);

        return $this->render('default/index.html.twig', compact('products'));
    }
}