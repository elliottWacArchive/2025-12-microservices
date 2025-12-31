<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use App\Infrastructure\Persistence\User\EloquentUser;
use Illuminate\Database\Capsule\Manager as Capsule;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });

    $app->get('/eloquent-test', function (Request $request, Response $response) use ($app) {
        $container = $app->getContainer();
        $capsule = $container->get(Capsule::class);
        
        $count = Capsule::table('users')->count();
    
        $first = EloquentUser::first();
    
        $data = [
            'count' => $count,
            'first' => $first ? [
                'id' => $first->id,
                'username' => $first->username,
                'first_name' => $first->first_name,
                'last_name' => $first->last_name,
            ] : null,
        ];
    
        $response->getBody()->write(json_encode($data));
    
        return $response
            ->withHeader('Content-Type', 'application/json');
    });
};
