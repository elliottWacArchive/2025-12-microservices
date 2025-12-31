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

        $existingMessage = $this->messageRepository->findMessageOfId($messageId);

        $userId = isset($data['userId']) ? (int) $data['userId'] : $existingMessage->getUserId();
        $content = $data['content'] ?? $existingMessage->getContent();

        $message = new Message(
            $messageId,
            $userId,
            $content
        );

        $updatedMessage = $this->messageRepository->update($message);

        $this->logger->info("Message of id `${messageId}` was updated.");

        return $this->respondWithData($updatedMessage);
    }
}

