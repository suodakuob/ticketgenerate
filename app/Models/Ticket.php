<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\FootballMatch;
use App\Models\Payment;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'match_id',
        'seat_number',
        'price',
        'status',
        'ticket_number',
        'qr_code', // 🔴 Assurez-vous que ce champ est bien là
    ];
    

    protected $casts = [
        'price' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function match()
    {
        return $this->belongsTo(FootballMatch::class, 'match_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
