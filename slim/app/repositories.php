<?php

declare(strict_types=1);

use App\Domain\Message\MessageRepository;
use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\Message\EloquentMessageRepository;
use App\Infrastructure\Persistence\User\EloquentUserRepository;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;
use DI\ContainerBuilder;
use Illuminate\Database\Capsule\Manager as Capsule;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {

    $containerBuilder->addDefinitions([
        UserRepository::class => function (ContainerInterface $c) {
            $capsule = $c->get(Capsule::class);
            
            return new EloquentUserRepository();
        },
        MessageRepository::class => function (ContainerInterface $c) {
            $capsule = $c->get(Capsule::class);
            
            return new EloquentMessageRepository();
        },
    ]);
};
