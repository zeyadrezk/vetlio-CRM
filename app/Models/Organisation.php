<?php

namespace App\Models;

use Glorand\Model\Settings\Traits\HasSettingsField;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organisation extends Model
{
    /** @use HasFactory<\Database\Factories\OrganisationFactory> */
    use HasFactory, SoftDeletes, HasSettingsField;

    protected $fillable = [
        'name',
        'subdomain',
        'logo',
        'address',
        'city',
        'zip_code',
        'phone',
        'email',
        'oib',
        'country_id',
        'language_id',
        'currency_id',
        'in_vat_system',
        'active',
        'full_address',
        'certificate_path',
        'certificate_password',
        'certificate_valid_to',
        'certificate_details',
        'fiscalization_enabled',
        'fiscalization_demo',
        'sequence_mark',
    ];

    protected function casts(): array
    {
        return [
            'certificate_password' => 'encrypted',
            'certificate_valid_to' => 'date',
            'certificate_details' => 'array',
        ];
    }

    public function fullAddress(): Attribute
    {
        return Attribute::make(function () {
            return implode(', ', [$this->address, $this->city, $this->zip_code]);
        });
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'organisation_id');
    }
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
