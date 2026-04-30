<?php

declare(strict_types=1);

namespace Medas\Users\RestControllers\Logins;

use Medas\Core\Interfaces\AuthenticationData as AuthenticationDataInterface;

class AuthenticationData implements AuthenticationDataInterface
{
    public array $additionalData = [];

    public function __construct(
        private readonly mixed $userId,
    )
    {
    }

    public function getUserId(): mixed
    {
        return $this->userId;
    }
}
