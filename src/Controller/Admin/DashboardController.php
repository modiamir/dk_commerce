<?php

namespace Digikala\Controller\Admin;


use Digikala\Application\Query\GetUsers\GetUsersQuery;
use Elastica\Client;
use Elastica\Index;
use Elastica\Type\Mapping;
use League\Tactician\CommandBus;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends Controller
{
    public function index(CommandBus $queryBus, Mapping $productIndexProductTypeMapping)
    {
        $getUsersQuery = new GetUsersQuery(2, 0);

        $users = $queryBus->handle($getUsersQuery);

        return $this->render('admin/dashboard/index.html.twig', compact('users'));
    }
}