<?php

declare(strict_types=1);

namespace Medas\Users;

use Medas\Core\{AsSingleton, BasePackage};
use Medas\EntityManager\EntityManagerPackage;
use Medas\RestRequestHandler\RestRequestHandlerPackage;
use Medas\Routing\RoutingPackage;

class UsersPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return [
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
