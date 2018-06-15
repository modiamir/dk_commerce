<?php

namespace Digikala\Security;

use Digikala\Entity\User;
use Digikala\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class EntityUserProvider implements UserProviderInterface
{
    /**
     * @var \Digikala\Repository\UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @inheritdoc
     */
    public function loadUserByUsername($username)
    {
        $user = $this->userRepository->findOneBy(['username' => $username]);

        if ($user instanceof User) {
            return $user;
        }

        throw new UsernameNotFoundException(sprintf('User "%s" not found.', $username));
    }

    /**
     * @inheritdoc
     */
    public function refreshUser(UserInterface $user) {
        $id = $user->getId();
        $refreshedUser = $this->userRepository->find($id);
        if (null === $refreshedUser) {
            throw new UsernameNotFoundException(sprintf('User with id %s not found', json_encode($id)));
        }

        return $refreshedUser;
    }

    /**
     * @inheritdoc
     */
    public function supportsClass($class)
    {
        return $class == User::class;
    }
}