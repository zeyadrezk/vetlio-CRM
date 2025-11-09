<?php

namespace App\Models;

use App\Traits\Organisationable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Document extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\DocumentFactory> */
    use HasFactory, SoftDeletes, Organisationable, InteractsWithMedia;

    protected $fillable = [
        'title',
        'related_type',
        'related_id',
        'description',
        'creator_id',
        'creator_type',
        'visible_in_portal',
        'organisation_id',
    ];

    protected static function booted()
    {
        parent::booted();

        static::creating(function (Document $document) {
            $document->creator_id = auth()->id();
            $document->creator_type = auth()->user()::class;
        });
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): MorphTo
    {
        return $this->morphTo();
    }
}
