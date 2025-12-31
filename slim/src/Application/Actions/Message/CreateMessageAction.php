<?php

declare(strict_types=1);

namespace App\Application\Actions\Message;

use App\Domain\Message\Message;
use Psr\Http\Message\ResponseInterface as Response;

class CreateMessageAction extends MessageAction
{
    protected function action(): Response
    {
        $data = $this->getFormData();

        if (!isset($data['content']) || empty($data['content'])) {
            return $this->respondWithData(['error' => 'content is required'], 400);
        }

        $userId = (int) $this->request->getAttribute('userId');

        $message = new Message(
            null,
            $userId,
            $data['content']
        );

        $createdMessage = $this->messageRepository->create($message);

        $this->logger->info("Message created with id: {$createdMessage->getId()} by user: {$userId}");

        return $this->respondWithData($createdMessage, 201);
    }
}

