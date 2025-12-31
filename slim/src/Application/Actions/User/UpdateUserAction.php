<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Domain\User\User;
use Psr\Http\Message\ResponseInterface as Response;

class UpdateUserAction extends UserAction
{
    protected function action(): Response
    {
        $userId = (int) $this->resolveArg('id');
        $data = $this->getFormData();

        $existingUser = $this->userRepository->findUserOfId($userId);

        $username = $data['username'] ?? $existingUser->getUsername();
        $firstName = $data['firstName'] ?? $existingUser->getFirstName();
        $lastName = $data['lastName'] ?? $existingUser->getLastName();

        $user = new User(
            $userId,
            $username,
            $firstName,
            $lastName
        );

        $updatedUser = $this->userRepository->update($user);

        $this->logger->info("User of id `${userId}` was updated.");

        return $this->respondWithData($updatedUser);
    }
}

