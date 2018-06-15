<?php

namespace Digikala\Application\Command\VerifyEmail;


class VerifyEmailCommand
{
    private $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }
}