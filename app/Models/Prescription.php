<?php

namespace App\Models;

use App\Traits\Organisationable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prescription extends Model
{
    use HasFactory, SoftDeletes, Organisationable;

    protected $fillable = [
        'patient_id',
        'prescriber_id',
        'medical_document_id',
        'organisation_id',
        'medication_name',
        'medication_code',
        'dosage',
        'form',
        'route',
        'frequency',
        'duration',
        'quantity',
        'refills',
        'instructions',
        'pharmacy',
        'pharmacy_phone',
        'prescribed_date',
        'valid_until',
        'filled_date',
        'discontinued_at',
        'discontinuation_reason',
        'status',
        'controlled_substance',
        'dea_schedule',
        'generic_allowed',
        'send_electronically',
        'notes',
    ];

    protected $casts = [
        'prescribed_date' => 'date',
        'valid_until' => 'date',
        'filled_date' => 'datetime',
        'discontinued_at' => 'datetime',
        'controlled_substance' => 'boolean',
        'generic_allowed' => 'boolean',
        'send_electronically' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function prescriber(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prescriber_id');
    }

    public function medicalDocument(): BelongsTo
    {
        return $this->belongsTo(MedicalDocument::class);
    }

    /**
     * Check if prescription is still active
     */
    public function isActive(): bool
    {
        if ($this->status === 'discontinued' || $this->status === 'cancelled' || $this->status === 'expired') {
            return false;
        }

        if ($this->valid_until && $this->valid_until->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Check if prescription needs refill
     */
    public function needsRefill(): bool
    {
        return $this->status === 'filled'
            && $this->refills > 0
            && $this->isActive();
    }

    /**
     * Check if prescription is expired
     */
    public function isExpired(): bool
    {
        if ($this->valid_until && $this->valid_until->isPast()) {
            return true;
        }

        return $this->status === 'expired';
    }

    /**
     * Mark prescription as filled
     */
    public function markAsFilled(): void
    {
        $this->update([
            'status' => 'filled',
            'filled_date' => now(),
        ]);
    }

    /**
     * Discontinue prescription
     */
    public function discontinue(string $reason = null): void
    {
        $this->update([
            'status' => 'discontinued',
            'discontinued_at' => now(),
            'discontinuation_reason' => $reason,
        ]);
    }

    /**
     * Scope for active prescriptions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now());
            });
    }

    /**
     * Scope for controlled substances
     */
    public function scopeControlledSubstances($query)
    {
        return $query->where('controlled_substance', true);
    }
}
