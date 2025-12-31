<?php

declare(strict_types=1);

namespace App\Domain\Message;

interface MessageRepository
{
    /**
     * @return Message[]
     */
    public function findAll(): array;

    /**
     * @param int $id
     * @return Message
     * @throws MessageNotFoundException
     */
    public function findMessageOfId(int $id): Message;

    /**
     * @param int $userId
     * @return Message[]
     */
    public function findByUserId(int $userId): array;

    /**
     * @param Message $message
     * @return Message
     */
    public function create(Message $message): Message;

    /**
     * @param Message $message
     * @return Message
     * @throws MessageNotFoundException
     */
    public function update(Message $message): Message;

    /**
     * @param int $id
     * @throws MessageNotFoundException
     */
    public function delete(int $id): void;
}

