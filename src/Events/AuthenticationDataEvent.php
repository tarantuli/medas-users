<?php

declare(strict_types=1);

namespace Medas\Users\Events;

use Medas\Users\RestControllers\Logins\AuthenticationData;

class AuthenticationDataEvent
{
    public function __construct(
        public AuthenticationData $authenticationData,
    )
    {
    }
}
