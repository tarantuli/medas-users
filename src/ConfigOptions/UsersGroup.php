<?php

declare(strict_types=1);

namespace Medas\Users\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup};

#[Service]
readonly class UsersGroup implements ConfigGroup
{
    public function parent(): ConfigGroup|null
    {
        return null;
    }

    public function name(): string
    {
        return 'users';
    }
}
