<?php

namespace Digikala\Application\Command\RegisterUser;

class RegisterUserCommand
{
    private $username;

    private $email;

    private $password;

    private $role;

    private $isEmailVerified;

    private $isActive;

    public function __construct(
        $username = null,
        $email = null,
        $password = null,
        $role = null,
        $isEmailVerified = false,
        $isActive = false
    ) {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->isEmailVerified = $isEmailVerified;
        $this->isActive = $isActive;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role){
        $this->role = $role;
    }

    /**
     * @return mixed
     */
    public function getIsEmailVerified()
    {
        return $this->isEmailVerified;
    }

    /**
     * @param mixed $isEmailVerified
     */
    public function setIsEmailVerified($isEmailVerified){
        $this->isEmailVerified = $isEmailVerified;
    }

    /**
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param mixed $isActive
     */
    public function setIsActive($isActive){
        $this->isActive = $isActive;
    }
}
