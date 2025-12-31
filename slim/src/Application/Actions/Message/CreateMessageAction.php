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

        if (!isset($data['userId']) || !isset($data['content'])) {
            return $this->respondWithData(['error' => 'userId and content are required'], 400);
        }

        $message = new Message(
            null,
            (int) $data['userId'],
            $data['content']
        );

        $createdMessage = $this->messageRepository->create($message);

        $this->logger->info("Message created with id: {$createdMessage->getId()}");

        return $this->respondWithData($createdMessage, 201);
    }
}

