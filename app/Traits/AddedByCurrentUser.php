<?php

namespace App\Traits;

use App\Models\User;

trait AddedByCurrentUser
{
    public static function bootAddedByCurrentUser(): void
    {
        if (auth()->check()) {
            static::creating(function ($model) {
                $model->user_id = auth()->id();
            });
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
