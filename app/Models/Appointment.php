<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_id',
        'schedule_id',
        'amount',
        'price_id',
        'start_time',
        'end_time',
        'status',
        'notes',
        'payment_status',
        'stripe_payment_intent',
        'stripe_checkout_session'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'status' => 'string',
        'payment_status' => 'string'
    ];

    protected $attributes = [
        'payment_status' => 'pending'
    ];

    public function price(): BelongsTo
    {
        return $this->belongsTo(Price::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }
}
