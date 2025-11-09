<?php

namespace App\Models;

use App\Traits\Organisationable;
use Filament\Models\Contracts\HasCurrentTenantLabel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model implements HasCurrentTenantLabel
{
    use SoftDeletes, Organisationable;

    protected $fillable = [
        'name',
        'address',
        'city',
        'branch_mark',
        'postal_code',
        'active',
        'price_list_id'
    ];

    public function getCurrentTenantLabel(): string
    {
        return 'Current branch';
    }

    public function priceList(): BelongsTo
    {
        return $this->belongsTo(PriceList::class, 'price_list_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'link_user_branches');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

}
