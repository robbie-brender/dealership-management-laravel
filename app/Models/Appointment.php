<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'date',
        'start_time',
        'duration',
        'status',
        'notes',
        'location',
        'dealership_id',
        'customer_id',
        'user_id',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'duration' => 'integer',
    ];
    
    /**
     * Get the dealership that the appointment belongs to.
     */
    public function dealership(): BelongsTo
    {
        return $this->belongsTo(Dealership::class);
    }
    
    /**
     * Get the customer that the appointment belongs to.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    
    /**
     * Get the user that the appointment belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
