<?php

namespace Digikala\Repository;

use Digikala\Entity\User;

class UserRepository extends AbstractServiceRepository
{
    public function getEntityClass() : string
    {
        return User::class;
    }

    public function page($offset, $size)
    {
        /** @var \Doctrine\ORM\QueryBuilder $queryBuilder */
        $queryBuilder = $this->createQueryBuilder('user');

        $queryBuilder
            ->setFirstResult($offset)
            ->setMaxResults($size);

        return $queryBuilder->getQuery()->getResult();
    }

    public function save(User $user)
    {
        if (!$user->getId()) {
            $this->getEntityManager()->persist($user);
        }

        $this->getEntityManager()->flush();
    }
}
