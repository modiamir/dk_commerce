<?php

namespace Digikala\Application\Query\GetUsers;

use Digikala\Application\QueryHandlerInterface;
use Digikala\Repository\UserRepository;

class GetUsersHandler implements QueryHandlerInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(GetUsersQuery $query)
    {
        return $this->userRepository->page($query->offset, $query->size);
    }
}