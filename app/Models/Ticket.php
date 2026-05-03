<?php

namespace App\Models;

use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ticket extends Model
{
    use HasFactory;

    // Lista Blanca
    protected $fillable = [
        'title',
        'status',
    ];

    // Cateado
    protected $casts = [
        'status' => TicketStatus::class,
    ];

    // Un ticket muchos mensajes
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    // Obtiene solo el mensaje raiz
    public function rootMessage(): HasOne
    {
        return $this->hasOne(Message::class)->where('is_root', true);
    }
}
