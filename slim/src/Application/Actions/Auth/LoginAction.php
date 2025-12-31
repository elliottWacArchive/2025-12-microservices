<?php

declare(strict_types=1);

namespace App\Application\Actions\Auth;

use App\Application\Actions\Action;
use App\Application\Services\JwtService;
use App\Domain\User\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class LoginAction extends Action
{
    private UserRepository $userRepository;
    private JwtService $jwtService;

    public function __construct(LoggerInterface $logger, UserRepository $userRepository, JwtService $jwtService)
    {
        parent::__construct($logger);
        $this->userRepository = $userRepository;
        $this->jwtService = $jwtService;
    }

    protected function action(): Response
    {
        $data = $this->getFormData();

        if (!isset($data['username']) || !isset($data['password'])) {
            return $this->respondWithData([
                'error' => 'username and password are required'
            ], 400);
        }

        try {
            $user = $this->userRepository->findByUsername($data['username']);
        } catch (\App\Domain\User\UserNotFoundException $e) {
            return $this->respondWithData([
                'error' => 'Invalid credentials'
            ], 401);
        }

        if ($user->getPassword() === null || !password_verify($data['password'], $user->getPassword())) {
            return $this->respondWithData([
                'error' => 'Invalid credentials'
            ], 401);
        }

        $token = $this->jwtService->generateToken(
            $user->getId(),
            $user->getUsername()
        );

        $this->logger->info("User logged in with id: {$user->getId()}");

        return $this->respondWithData([
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
            ]
        ]);
    }
}

