<?php

namespace App\Models;

use App\Traits\Organisationable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InsurancePlan extends Model
{
    use HasFactory, SoftDeletes, Organisationable;

    protected $fillable = [
        'patient_id',
        'organisation_id',
        'provider_name',
        'policy_number',
        'group_number',
        'subscriber_name',
        'subscriber_relationship',
        'subscriber_dob',
        'effective_date',
        'expiration_date',
        'copay_amount',
        'deductible',
        'deductible_met',
        'out_of_pocket_max',
        'out_of_pocket_met',
        'priority',
        'plan_type',
        'verification_status',
        'last_verified_at',
        'notes',
        'active',
    ];

    protected $casts = [
        'subscriber_dob' => 'date',
        'effective_date' => 'date',
        'expiration_date' => 'date',
        'last_verified_at' => 'datetime',
        'copay_amount' => 'decimal:2',
        'deductible' => 'decimal:2',
        'deductible_met' => 'decimal:2',
        'out_of_pocket_max' => 'decimal:2',
        'out_of_pocket_met' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Check if insurance is currently active and valid
     */
    public function isActive(): bool
    {
        if (!$this->active) {
            return false;
        }

        $now = now();

        if ($this->effective_date && $this->effective_date->isFuture()) {
            return false;
        }

        if ($this->expiration_date && $this->expiration_date->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Check if verification is needed
     */
    public function needsVerification(): bool
    {
        if ($this->verification_status === 'not_verified') {
            return true;
        }

        if ($this->verification_status === 'expired') {
            return true;
        }

        // If last verified more than 30 days ago
        if ($this->last_verified_at && $this->last_verified_at->diffInDays(now()) > 30) {
            return true;
        }

        return false;
    }

    /**
     * Get remaining deductible
     */
    public function getRemainingDeductibleAttribute(): ?float
    {
        if (!$this->deductible) {
            return null;
        }

        return max(0, $this->deductible - $this->deductible_met);
    }

    /**
     * Get remaining out-of-pocket maximum
     */
    public function getRemainingOutOfPocketAttribute(): ?float
    {
        if (!$this->out_of_pocket_max) {
            return null;
        }

        return max(0, $this->out_of_pocket_max - $this->out_of_pocket_met);
    }

    /**
     * Scope for primary insurance
     */
    public function scopePrimary($query)
    {
        return $query->where('priority', 'primary');
    }

    /**
     * Scope for active insurance plans
     */
    public function scopeActive($query)
    {
        return $query->where('active', true)
            ->where(function ($q) {
                $q->whereNull('expiration_date')
                    ->orWhere('expiration_date', '>=', now());
            });
    }
}
