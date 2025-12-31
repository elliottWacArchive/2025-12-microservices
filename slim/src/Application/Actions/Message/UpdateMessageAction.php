<?php

declare(strict_types=1);

namespace App\Application\Actions\Message;

use App\Domain\Message\Message;
use Psr\Http\Message\ResponseInterface as Response;

class UpdateMessageAction extends MessageAction
{
    protected function action(): Response
    {
        $messageId = (int) $this->resolveArg('id');
        $data = $this->getFormData();

        $userId = (int) $this->request->getAttribute('userId');

        $existingMessage = $this->messageRepository->findMessageOfId($messageId);

        if ($existingMessage->getUserId() !== $userId) {
            return $this->respondWithData(['error' => 'You can only update your own messages'], 403);
        }

        $content = $data['content'] ?? $existingMessage->getContent();

        if (empty($content)) {
            return $this->respondWithData(['error' => 'content cannot be empty'], 400);
        }

        $message = new Message(
            $messageId,
            $userId,
            $content
        );

        $updatedMessage = $this->messageRepository->update($message);

        $this->logger->info("Message of id `${messageId}` was updated by user: {$userId}");

        return $this->respondWithData($updatedMessage);
    }
}

