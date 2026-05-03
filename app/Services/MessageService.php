<?php

namespace App\Services;

use App\Exceptions\CannotDeleteRootMessageException;
use App\Exceptions\TicketClosedException;
use App\Interfaces\MessageRepositoryInterface;
use App\Models\Message;

class MessageService
{

    public function __construct(private MessageRepositoryInterface $messageRepository)
    {

    }

    public function updateMessage(Message $message, array $data): Message
    {
        if ($message->ticket->status->isClosed()) {
            throw new TicketClosedException();
        }

        return $this->messageRepository->update($message, $data);
    }

    public function deleteMessage(Message $message): bool
    {
        if($message->is_root) {
            throw new CannotDeleteRootMessageException();
        }

        return $this->messageRepository->delete($message);
    }
}
