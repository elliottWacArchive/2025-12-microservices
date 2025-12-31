<?php

declare(strict_types=1);

namespace App\Application\Actions\Auth;

use App\Application\Actions\Action;
use App\Domain\User\User;
use App\Domain\User\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class RegisterAction extends Action
{
    private UserRepository $userRepository;

    public function __construct(LoggerInterface $logger, UserRepository $userRepository)
    {
        parent::__construct($logger);
        $this->userRepository = $userRepository;
    }

    protected function action(): Response
    {
        $data = $this->getFormData();

        if (!isset($data['username']) || !isset($data['password']) || !isset($data['firstName']) || !isset($data['lastName'])) {
            return $this->respondWithData([
                'error' => 'username, password, firstName and lastName are required'
            ], 400);
        }

        try {
            $this->userRepository->findByUsername($data['username']);
            return $this->respondWithData([
                'error' => 'Username already exists'
            ], 409);
        } catch (\App\Domain\User\UserNotFoundException $e) {
        }

        $user = new User(
            null,
            $data['username'],
            $data['firstName'],
            $data['lastName'],
            $data['password']
        );

        $createdUser = $this->userRepository->create($user);

        $this->logger->info("User registered with id: {$createdUser->getId()}");

        return $this->respondWithData([
            'message' => 'User registered successfully',
            'user' => [
                'id' => $createdUser->getId(),
                'username' => $createdUser->getUsername(),
                'firstName' => $createdUser->getFirstName(),
                'lastName' => $createdUser->getLastName(),
            ]
        ], 201);
    }
}

