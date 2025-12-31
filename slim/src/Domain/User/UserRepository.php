<?php

declare(strict_types=1);

namespace App\Domain\User;

interface UserRepository
{
    /**
     * @return User[]
     */
    public function findAll(): array;

    /**
     * @param int $id
     * @return User
     * @throws UserNotFoundException
     */
    public function findUserOfId(int $id): User;

    /**
     * @param string $username
     * @return User
     * @throws UserNotFoundException
     */
    public function findByUsername(string $username): User;

    /**
     * @param User $user
     * @return User
     */
    public function create(User $user): User;

    /**
     * @param User $user
     * @return User
     * @throws UserNotFoundException
     */
    public function update(User $user): User;

    /**
     * @param int $id
     * @throws UserNotFoundException
     */
    public function delete(int $id): void;
}
