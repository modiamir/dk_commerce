<?php

namespace Digikala\Security;


use Symfony\Component\Security\Core\Authentication\Provider\UserAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthenticationProvider extends UserAuthenticationProvider
{

    /**
     * Retrieves the user from an implementation-specific location.
     *
     * @param string $username The username to retrieve
     * @param UsernamePasswordToken $token The Token
     *
     * @return UserInterface The user
     *
     * @throws AuthenticationException if the credentials could not be validated
     */
    protected function retrieveUser($username, UsernamePasswordToken $token) {
        // TODO: Implement retrieveUser() method.
    }

    /**
     * Does additional checks on the user and token (like validating the
     * credentials).
     *
     * @throws AuthenticationException if the credentials could not be validated
     */
    protected function checkAuthentication(UserInterface $user, UsernamePasswordToken $token) {
        // TODO: Implement checkAuthentication() method.
    }
}