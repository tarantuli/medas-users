<?php

declare(strict_types=1);

namespace Medas\Users\Events;

use Medas\Users\Entities\UserInterface;
use Medas\Users\RestControllers\Logins\Result;

class LoginEvent
{
    public Result $result = Result::UserNotFound;
    public UserInterface|null $user = null;

    public function __construct(
        public readonly string  $userName,
        private readonly string $password,
    )
    {
    }

    public function password(): string
    {
        return $this->password;
    }
}
