<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Dealership extends Model
{
    use BelongsToTenant;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'address',
        'city',
        'state',
        'zip_code',
        'phone',
        'email',
        'website',
        'tenant_id',
    ];
    
    /**
     * Get the users for the dealership.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
    
    /**
     * Get the customers for the dealership.
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
    
    /**
     * Get the appointments for the dealership.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
    
    /**
     * Get the interactions for the dealership.
     */
    public function interactions(): HasMany
    {
        return $this->hasMany(Interaction::class);
    }
    
    /**
     * Get the knowledge bases for the dealership.
     */
    public function knowledgeBases(): HasMany
    {
        return $this->hasMany(KnowledgeBase::class);
    }
}
