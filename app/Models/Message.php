<?php

namespace App\Models;

use App\Enums\AuthorType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    // Lista blanca
    protected $fillable = [
        'ticket_id',
        'content',
        'author',
        'author_type',
        'is_root',
    ];

    // Casteado
    protected $casts = [
        'author_type' => AuthorType::class,
        'is_root'     => 'boolean',
    ];

    // Un mensaje pertenece a un ticket
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }
}
