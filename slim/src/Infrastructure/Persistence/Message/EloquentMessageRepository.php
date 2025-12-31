<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Message;

use App\Domain\Message\Message;
use App\Domain\Message\MessageNotFoundException;
use App\Domain\Message\MessageRepository;

class EloquentMessageRepository implements MessageRepository
{
    public function findAll(): array
    {
        $eloquentMessages = EloquentMessage::all();
        $messages = [];

        foreach ($eloquentMessages as $eloquentMessage) {
            $messages[] = $this->toDomainMessage($eloquentMessage);
        }

        return $messages;
    }

    public function findMessageOfId(int $id): Message
    {
        $eloquentMessage = EloquentMessage::find($id);

        if ($eloquentMessage === null) {
            throw new MessageNotFoundException();
        }

        return $this->toDomainMessage($eloquentMessage);
    }

    public function findByUserId(int $userId): array
    {
        $eloquentMessages = EloquentMessage::where('user_id', $userId)->get();
        $messages = [];

        foreach ($eloquentMessages as $eloquentMessage) {
            $messages[] = $this->toDomainMessage($eloquentMessage);
        }

        return $messages;
    }

    public function create(Message $message): Message
    {
        $eloquentMessage = new EloquentMessage();
        $eloquentMessage->user_id = $message->getUserId();
        $eloquentMessage->content = $message->getContent();
        $eloquentMessage->save();

        return $this->toDomainMessage($eloquentMessage);
    }

    public function update(Message $message): Message
    {
        $eloquentMessage = EloquentMessage::find($message->getId());

        if ($eloquentMessage === null) {
            throw new MessageNotFoundException();
        }

        $eloquentMessage->user_id = $message->getUserId();
        $eloquentMessage->content = $message->getContent();
        $eloquentMessage->save();

        return $this->toDomainMessage($eloquentMessage);
    }

    public function delete(int $id): void
    {
        $eloquentMessage = EloquentMessage::find($id);

        if ($eloquentMessage === null) {
            throw new MessageNotFoundException();
        }

        $eloquentMessage->delete();
    }

    private function toDomainMessage(EloquentMessage $eloquentMessage): Message
    {
        return new Message(
            $eloquentMessage->id,
            $eloquentMessage->user_id,
            $eloquentMessage->content,
            $eloquentMessage->created_at ? $eloquentMessage->created_at->toDateTimeString() : null
        );
    }
}

