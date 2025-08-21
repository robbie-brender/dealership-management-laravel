<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'call_id',
        'status',
        'direction',
        'caller_number',
        'recipient_number',
        'duration',
        'assistant_id',
        'transcript',
        'recording_url',
        'metadata',
        'call_started_at',
        'call_ended_at',
        'dealership_id',
        'department',
        'vapi_summary',
        'vapi_success_evaluation',
        'vapi_analysis',
        'vapi_recording_url',
        'vapi_stereo_recording_url',
        'vapi_cost',
        'vapi_duration_seconds',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'metadata' => 'array',
        'call_started_at' => 'datetime',
        'call_ended_at' => 'datetime',
        'vapi_summary' => 'array',
        'vapi_success_evaluation' => 'boolean',
        'vapi_analysis' => 'array',
        'vapi_cost' => 'float',
        'vapi_duration_seconds' => 'integer',
    ];

    /**
     * Get the dealership that owns the call log.
     */
    public function dealership()
    {
        return $this->belongsTo(Dealership::class);
    }
}
