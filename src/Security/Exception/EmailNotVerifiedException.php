<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Digikala\Security\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

/**
 * EmailNotVerifiedException is thrown when the user email is not verified.
 */
class EmailNotVerifiedException extends AccountStatusException
{
    /**
     * {@inheritdoc}
     */
    public function getMessageKey()
    {
        return 'Email is not verified.';
    }
}
