<?php

declare(strict_types=1);

use App\Application\Actions\Auth\LoginAction;
use App\Application\Actions\Auth\RegisterAction;
use App\Application\Actions\Message\CreateMessageAction;
use App\Application\Actions\Message\DeleteMessageAction;
use App\Application\Actions\Message\ListMessagesAction;
use App\Application\Actions\Message\UpdateMessageAction;
use App\Application\Actions\Message\ViewMessageAction;
use App\Application\Actions\User\CreateUserAction;
use App\Application\Actions\User\DeleteUserAction;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\UpdateUserAction;
use App\Application\Actions\User\ViewUserAction;
use App\Application\Middleware\JwtMiddleware;
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

    // Routes d'auth (publiques)
    $app->post('/auth/register', RegisterAction::class);  // post /auth/register : inscription
    $app->post('/auth/login', LoginAction::class);        // post /auth/login : connexion (renvoie token)

    // Routes protégées
    $app->group('', function (Group $group) {
        // Routes Users (protégées)
        $group->group('/users', function (Group $usersGroup) {
            $usersGroup->get('', ListUsersAction::class);           // get /users : liste tous les users
            $usersGroup->get('/{id}', ViewUserAction::class);       // get /users/{id} : voir un user
            $usersGroup->post('', CreateUserAction::class);         // post /users : créer un user
            $usersGroup->put('/{id}', UpdateUserAction::class);     // put /users/{id} : update un user
            $usersGroup->delete('/{id}', DeleteUserAction::class);  // delete /users/{id} : delete un user
        });

        // Routes Messages (protégées)
        $group->group('/messages', function (Group $messagesGroup) {
            $messagesGroup->get('', ListMessagesAction::class);             // get /messages : liste tous les messages
            $messagesGroup->get('/{id}', ViewMessageAction::class);         // get /messages/{id} : voir un message
            $messagesGroup->post('', CreateMessageAction::class);           // post /messages : créer un message
            $messagesGroup->put('/{id}', UpdateMessageAction::class);       // put /messages/{id} : update un message
            $messagesGroup->delete('/{id}', DeleteMessageAction::class);    // delete /messages/{id} : delete un message
        });
    })->add(JwtMiddleware::class);

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
