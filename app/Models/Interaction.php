<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interaction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'start_time',
        'duration',
        'notes',
        'summary',
        'dealership_id',
        'customer_id',
        'user_id',
        'knowledge_base_id',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_time' => 'datetime',
        'duration' => 'integer',
    ];
    
    /**
     * Get the dealership that the interaction belongs to.
     */
    public function dealership(): BelongsTo
    {
        return $this->belongsTo(Dealership::class);
    }
    
    /**
     * Get the customer that the interaction belongs to.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    
    /**
     * Get the user that the interaction belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the knowledge base that the interaction references.
     */
    public function knowledgeBase(): BelongsTo
    {
        return $this->belongsTo(KnowledgeBase::class);
    }
}
