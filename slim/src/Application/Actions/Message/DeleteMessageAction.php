<?php

declare(strict_types=1);

namespace App\Application\Actions\Message;

use Psr\Http\Message\ResponseInterface as Response;

class DeleteMessageAction extends MessageAction
{
    protected function action(): Response
    {
        $messageId = (int) $this->resolveArg('id');

        $userId = (int) $this->request->getAttribute('userId');

        $message = $this->messageRepository->findMessageOfId($messageId);

        if ($message->getUserId() !== $userId) {
            return $this->respondWithData(['error' => 'You can only delete your own messages'], 403);
        }

        $this->messageRepository->delete($messageId);

        $this->logger->info("Message of id `{$messageId}` was deleted by user: {$userId}");

        return $this->respondWithData(['message' => 'Message deleted successfully'], 200);
    }
}

