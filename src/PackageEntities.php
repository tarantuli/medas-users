<?php

declare(strict_types=1);

namespace Medas\Users;

use Medas\Core\{Attributes\Service, Interfaces\PackageEntities as PackageEntitiesInterface};

#[Service]
readonly class PackageEntities implements PackageEntitiesInterface
{
    public function directories(): array
    {
        return [
            __DIR__ . '/Entities',
        ];
    }
}
