<?php

namespace Digikala\Application\Command\VerifyEmail;

use Digikala\Application\CommandHandlerInterface;
use Digikala\Entity\User;
use Digikala\Repository\UserRepository;

class VerifyEmailHandler implements CommandHandlerInterface
{
    /**
     * @var \Digikala\Repository\UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(VerifyEmailCommand $command)
    {
        /** @var \Digikala\Entity\User $user */
        $user = $this->userRepository->findOneBy([
            'emailVerificationCode' => $command->getCode(),
            'isEmailVerified' => false,
        ]);

        if (!$user instanceof User) {
            throw new \LogicException('Email verification code is invalid.');
        }

        $user->makeEmailVerified();

        $this->userRepository->save($user);
    }
}