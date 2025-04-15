<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ticket;
use App\Models\Section;

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
        'stadium_map_data', // JSON data for stadium sections
    ];

    protected $casts = [
        'match_date' => 'datetime',
        'match_time' => 'datetime',
        'ticket_price' => 'decimal:2',
        'stadium_map_data' => 'array',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'match_id');
    }

    /**
     * Get the sections for the match.
     */
    public function sections()
    {
        return $this->hasMany(Section::class, 'match_id');
    }
}
