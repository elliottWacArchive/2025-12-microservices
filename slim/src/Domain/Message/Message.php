<?php

declare(strict_types=1);

namespace App\Domain\Message;

use JsonSerializable;

class Message implements JsonSerializable
{
    private ?int $id;

    private int $userId;

    private string $content;

    private ?string $discussionId;

    private ?string $createdAt;

    public function __construct(?int $id, int $userId, string $content, ?string $discussionId = null, ?string $createdAt = null)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->content = $content;
        $this->discussionId = $discussionId;
        $this->createdAt = $createdAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getDiscussionId(): ?string
    {
        return $this->discussionId;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'content' => $this->content,
            'discussionId' => $this->discussionId,
            'createdAt' => $this->createdAt,
        ];
    }
}

