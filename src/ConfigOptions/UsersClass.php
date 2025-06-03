<?php

declare(strict_types=1);

namespace Medas\Users\ConfigOptions;

use Medas\Core\Attributes\Service;
use Medas\RestRequestHandler\ConfigOptions\UsersClass as RestRequestHandlerOption;
use Medas\Users\User;

#[Service]
readonly class UsersClass extends RestRequestHandlerOption
{
    public function hasDefault(): bool
    {
        return true;
    }

    public function default(): string
    {
        return User::class;
    }
}
