<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => true, // Should be set to false in production
                'logError'            => false,
                'logErrorDetails'     => false,
                'determineRouteBeforeAppMiddleware' => false,
                'db' => [
                    'driver' => 'mysql',
                    'host' => isset($_ENV['docker']) ? 'mysql' : 'localhost',
                    'database' => 'slim',
                    'username' => isset($_ENV['docker']) ? 'root' : 'root',
                    'password' => isset($_ENV['docker']) ? 'root' : '',
                    'charset' => 'utf8',
                    'collation' => 'utf8_unicode_ci',
                    'prefix' => '',
                ],
                'logger' => [
                    'name' => 'slim-app',
                    'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                    'level' => Logger::DEBUG,
                ],
                'jwt' => [
                    'secret' => $_ENV['JWT_SECRET'] ?? 'phrase-top-secrete-lolilol-mais-il-faut-mettre-un-vrai-truc-en-prod',
                    'algorithm' => 'HS256',
                    'expiration' => 3600, // 1 heure
                ],
            ]);
        }
    ]);
};
