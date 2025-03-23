<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ticket;

class FootballMatch extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $fillable = [
        'name',
        'home_team',
        'away_team',
        'match_date',
        'stadium',
        'stadium_image',
        'ticket_price',
        'ticket_type',
        'available_tickets',
        'description',
        'match_time',
        'match_status',
    ];

    protected $casts = [
        'match_date' => 'datetime',
        'match_time' => 'datetime',
        'ticket_price' => 'decimal:2',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'match_id');
    }
}
