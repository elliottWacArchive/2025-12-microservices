<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\User;
use App\Domain\User\UserNotFoundException;
use App\Domain\User\UserRepository;

class EloquentUserRepository implements UserRepository
{
    public function findAll(): array
    {
        $eloquentUsers = EloquentUser::all();
        $users = [];

        foreach ($eloquentUsers as $eloquentUser) {
            $users[] = $this->toDomainUser($eloquentUser);
        }

        return $users;
    }
    public function findUserOfId(int $id): User
    {
        $eloquentUser = EloquentUser::find($id);

        if ($eloquentUser === null) {
            throw new UserNotFoundException();
        }

        return $this->toDomainUser($eloquentUser);
    }
    private function toDomainUser(EloquentUser $eloquentUser): User
    {
        return new User(
            $eloquentUser->id,
            $eloquentUser->username,
            $eloquentUser->first_name,
            $eloquentUser->last_name
        );
    }
}

