<?php

namespace App\Enums;

enum TicketStatus: string
{
    case Open          = 'open';
    case OperatorReply = 'operator_reply';
    case CustomerReply = 'customer_reply';
    case Closed        = 'closed';

    public function isClosed(): bool
    {
        return $this === self::Closed;
    }

    // Para determinar quien modifico o respondio el ticket
    public static function fromAuthorType(AuthorType $authorType): self
    {
        return match ($authorType) {
            AuthorType::Operator => self::OperatorReply,
            AuthorType::Customer => self::CustomerReply,
        };
    }
}
