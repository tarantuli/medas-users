<?php

declare(strict_types=1);

namespace Medas\Users;

use Medas\ApiKeys\ApiKeysPackage;
use Medas\Core\AsSingleton;
use Medas\EntityManager\EntityManagerPackage;
use Medas\RestRequestHandler\RestRequestHandlerPackage;
use Medas\Routing\RoutingPackage;
use Medas\ServiceManager\BasePackage;

class UsersPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return [
            ApiKeysPackage::instance(),
            EntityManagerPackage::instance(),
            RestRequestHandlerPackage::instance(),
            RoutingPackage::instance(),
        ];
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }
}
