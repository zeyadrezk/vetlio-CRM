<?php

namespace App\Traits;

use App\Models\Organisation;
use App\Models\User;
use App\Scopes\OrganisationScope;
use Illuminate\Support\Facades\Schema;

trait Organisationable
{
    public static function bootOrganisationable(): void
    {
        if (auth('web')->check()) {
            static::addGlobalScope(new OrganisationScope());

            static::creating(function ($model) {
                $model->organisation_id = auth()->user()->organisation_id;
            });
        }
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }
}
