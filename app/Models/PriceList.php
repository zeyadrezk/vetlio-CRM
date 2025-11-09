<?php

namespace App\Models;

use App\Traits\Organisationable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PriceList extends Model
{
    /** @use HasFactory<\Database\Factories\PriceListFactory> */
    use HasFactory, Organisationable, SoftDeletes;

    protected $fillable = [
        'name',
        'active',
    ];

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'link_price_list_branches', 'price_list_id', 'branch_id');
    }

    public function prices(): HasMany
    {
        return $this->hasMany(Price::class, 'price_list_id');
    }
}
