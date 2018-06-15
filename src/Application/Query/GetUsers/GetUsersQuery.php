<?php

namespace Digikala\Application\Query\GetUsers;

class GetUsersQuery
{
    /**
     * @var int
     */
    public $size;

    /**
     * @var int
     */
    public $offset;

    public function __construct($size = 10, $offset = 0)
    {
        $this->size = $size;
        $this->offset = $offset;
    }
}