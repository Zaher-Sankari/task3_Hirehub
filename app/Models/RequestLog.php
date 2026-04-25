<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestLog extends Model
{
    use HasFactory;

    protected $table = 'request_logs';

    protected $fillable = [
        'user_id',
        'method',
        'endpoint',
        'ip',
        'response_time_ms',
        'status_code',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'response_time_ms' => 'decimal:2',
        'status_code' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Who made the request (if authenticated)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Get slow requests (response time > threshold in ms)
     */
    public function scopeSlow($query, int $threshold = 1000)
    {
        return $query->where('response_time_ms', '>', $threshold);
    }

    /**
     * Scope: Filter by endpoint
     */
    public function scopeForEndpoint($query, string $endpoint)
    {
        return $query->where('endpoint', 'LIKE', "%{$endpoint}%");
    }

    /**
     * Scope: Filter by status code
     */
    public function scopeWithStatus($query, int $statusCode)
    {
        return $query->where('status_code', $statusCode);
    }

    /**
     * Scope: Get failed requests (4xx and 5xx)
     */
    public function scopeFailed($query)
    {
        return $query->where(function ($q) {
            $q->where('status_code', '>=', 400)
              ->where('status_code', '<', 600);
        });
    }

    /**
     * Accessor: Format response time for display
     */
    public function getFormattedResponseTimeAttribute(): string
    {
        if ($this->response_time_ms >= 1000) {
            return round($this->response_time_ms / 1000, 2) . ' s';
        }
        return $this->response_time_ms . ' ms';
    }

    /**
     * Accessor: Get HTTP method with color indicator (for debugging)
     */
    public function getMethodBadgeAttribute(): string
    {
        return match ($this->method) {
            'GET' => 'GET',
            'POST' => 'POST',
            'PUT' => 'PUT',
            'DELETE' => 'DELETE',
            default => $this->method,
        };
    }
}