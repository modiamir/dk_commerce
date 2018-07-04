<?php

namespace Digikala\Security;

use Digikala\Entity\User;
use Digikala\Security\Exception\EmailNotVerifiedException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    /**
     * @inheritdoc
     */
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }

        if (!$user->getIsEmailVerified()) {
            $ex = new EmailNotVerifiedException('Email is not verified.');
            $ex->setUser($user);
            throw $ex;
        }
    }

    /**
     * @inheritdoc
     */
    public function checkPostAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }

        if (!$user->getIsActive()) {
            $ex = new DisabledException('User account is disabled.');
            $ex->setUser($user);
            throw $ex;
        }
    }
}