<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Domain\User\User;
use Psr\Http\Message\ResponseInterface as Response;

class CreateUserAction extends UserAction
{
    protected function action(): Response
    {
        $data = $this->getFormData();

        if (!isset($data['username']) || !isset($data['firstName']) || !isset($data['lastName'])) {
            return $this->respondWithData(['error' => 'username, firstName and lastName are required'], 400);
        }

        $user = new User(
            null,
            $data['username'],
            $data['firstName'],
            $data['lastName']
        );

        $createdUser = $this->userRepository->create($user);

        $this->logger->info("User created with id: {$createdUser->getId()}");

        return $this->respondWithData($createdUser, 201);
    }
}

