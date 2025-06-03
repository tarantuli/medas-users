<?php

declare(strict_types=1);

use Medas\Users\UsersPackage;
use Medas\ServiceManager\{ServiceConfig, ServiceManager};

chdir(__DIR__);

new ServiceManager(function (): ServiceConfig {
    $config = new ServiceConfig();

    $config->addPackages([
        UsersPackage::instance(),
    ]);

    return $config;
});
