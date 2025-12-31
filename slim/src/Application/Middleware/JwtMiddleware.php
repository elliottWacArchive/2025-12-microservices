<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use App\Application\Services\JwtService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpUnauthorizedException;

class JwtMiddleware implements MiddlewareInterface
{
    private JwtService $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $authorizationHeader = $request->getHeaderLine('Authorization');
        $token = $this->jwtService->extractTokenFromHeader($authorizationHeader);

        if ($token === null) {
            throw new HttpUnauthorizedException($request, 'Token missing');
        }

        $decoded = $this->jwtService->validateToken($token);

        if ($decoded === null) {
            throw new HttpUnauthorizedException($request, 'Invalid or expired token');
        }

        $request = $request->withAttribute('userId', $decoded['userId']);
        $request = $request->withAttribute('username', $decoded['username']);
        $request = $request->withAttribute('token', $decoded);

        return $handler->handle($request);
    }
}

