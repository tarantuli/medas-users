<?php

declare(strict_types=1);

namespace Medas\Users\RestControllers\Logins;

use Medas\Users\Entities\UserInterface;

class LoginEvent
{
    public Result $result;
    public UserInterface|null $user = null;

    public function __construct(
        public string $userName,
        public string $password,
    )
    {
    }
}
