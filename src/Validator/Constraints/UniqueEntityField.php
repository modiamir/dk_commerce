<?php

namespace Digikala\Validator\Constraints;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

class UniqueEntityField extends UniqueEntity
{
    /**
     * The validator must be defined as a service with this name.
     *
     * @return string
     */
    public function validatedBy()
    {
        return UniqueEntityFieldValidator::class;
    }
}
