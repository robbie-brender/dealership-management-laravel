<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KnowledgeBase extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'source_type',
        'source_url',
        'content',
        'file_path',
        'dealership_id',
        'user_id',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tags' => 'array',
    ];
    
    /**
     * Get the dealership that the knowledge base belongs to.
     */
    public function dealership(): BelongsTo
    {
        return $this->belongsTo(Dealership::class);
    }
    
    /**
     * Get the user that created the knowledge base.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the interactions that reference this knowledge base.
     */
    public function interactions(): HasMany
    {
        return $this->hasMany(Interaction::class);
    }
}
