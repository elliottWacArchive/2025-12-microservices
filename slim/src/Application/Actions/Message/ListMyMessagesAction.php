<?php

declare(strict_types=1);

namespace App\Application\Actions\Message;

use Psr\Http\Message\ResponseInterface as Response;

class ListMyMessagesAction extends MessageAction
{
    protected function action(): Response
    {
        $userId = (int) $this->request->getAttribute('userId');

        $messages = $this->messageRepository->findByUserId($userId);

        $this->logger->info("Messages list was viewed by user: {$userId}");

        return $this->respondWithData($messages);
    }
}

