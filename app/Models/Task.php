<?php

namespace App\Models;

use App\Contracts\TaskRelated;
use App\Enums\TaskStatus;
use App\Traits\AddedByCurrentUser;
use App\Traits\Organisationable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Tags\HasTags;

class Task extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory, SoftDeletes, Organisationable, InteractsWithMedia, HasTags, AddedByCurrentUser;

    protected $fillable = [
        'title',
        'description',
        'priority_id',
        'status_id',
        'completed_at',
        'start_at',
        'deadline_at',
        'related_id',
        'related_type',
        'content',
        'completed'
    ];

    protected $casts = [
        'deadline_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        parent::booted();

        static::creating(function (Task $task) {
            $task->status_id = TaskStatus::Created->value;
        });

        static::updating(function (Task $task) {
            $task->completed_at = $task->status_id == TaskStatus::Completed->value ? now() : null;
        });
    }

    public function isCompleted(): bool
    {
        return $this->status_id == TaskStatus::Completed->value;
    }

    public function assignedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'link_task_users', 'task_id', 'user_id');;
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }
}
