<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'priority',
        'category',
        'status',
        'summary',
        'suggested_action',
        'is_escalated',
    ];

    protected $casts = [
        'is_escalated' => 'boolean',
    ];

    // Valid enum values
    public const PRIORITIES = ['low', 'medium', 'high', 'critical'];
    public const CATEGORIES = ['bug', 'feature_request', 'support', 'infrastructure', 'security'];
    public const STATUSES = ['open', 'in_progress', 'resolved', 'closed'];

    // Query Scopes for filtering
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeEscalated($query)
    {
        return $query->where('is_escalated', true);
    }

    // Helper methods
    public function isHighPriority(): bool
    {
        return in_array($this->priority, ['high', 'critical']);
    }

    public function isSecurityRelated(): bool
    {
        return $this->category === 'security';
    }
}
