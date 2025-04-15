<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'section_id',
        'name',
        'capacity',
        'available_seats',
        'price',
        'section_type', // Standard, VIP, Premium
        'view_360_url',
        'is_active'
    ];

    /**
     * Get the match that owns the section.
     */
    public function match()
    {
        return $this->belongsTo(FootballMatch::class, 'match_id');
    }

    /**
     * Get the tickets for the section.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'section_id');
    }
}
