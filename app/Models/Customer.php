<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'notes',
        'dealership_id',
    ];
    
    /**
     * Get the dealership that the customer belongs to.
     */
    public function dealership(): BelongsTo
    {
        return $this->belongsTo(Dealership::class);
    }
    
    /**
     * Get the appointments for the customer.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
    
    /**
     * Get the interactions for the customer.
     */
    public function interactions(): HasMany
    {
        return $this->hasMany(Interaction::class);
    }
    
    /**
     * Get the customer's full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
