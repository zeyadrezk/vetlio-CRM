<?php

namespace App\Models;

use App\Traits\Organisationable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Diagnosis extends Model
{
    use HasFactory, SoftDeletes, Organisationable;

    protected $fillable = [
        'patient_id',
        'medical_document_id',
        'diagnosed_by',
        'organisation_id',
        'icd10_code',
        'icd10_description',
        'clinical_description',
        'type',
        'category',
        'diagnosed_date',
        'onset_date',
        'resolved_date',
        'status',
        'severity',
        'billable',
        'dx_order',
        'treatment_plan',
        'notes',
    ];

    protected $casts = [
        'diagnosed_date' => 'date',
        'onset_date' => 'date',
        'resolved_date' => 'date',
        'billable' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function medicalDocument(): BelongsTo
    {
        return $this->belongsTo(MedicalDocument::class);
    }

    public function diagnosedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diagnosed_by');
    }

    /**
     * Check if diagnosis is currently active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' || $this->status === 'chronic';
    }

    /**
     * Check if diagnosis is chronic
     */
    public function isChronic(): bool
    {
        return $this->status === 'chronic' || $this->category === 'chronic';
    }

    /**
     * Resolve the diagnosis
     */
    public function resolve(): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_date' => now(),
        ]);
    }

    /**
     * Get full diagnosis display (code + description)
     */
    public function getFullDescriptionAttribute(): string
    {
        return $this->icd10_code . ' - ' . $this->icd10_description;
    }

    /**
     * Scope for active diagnoses
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['active', 'chronic', 'in_remission', 'recurrent']);
    }

    /**
     * Scope for chronic conditions
     */
    public function scopeChronic($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'chronic')
                ->orWhere('category', 'chronic');
        });
    }

    /**
     * Scope for primary diagnosis
     */
    public function scopePrimary($query)
    {
        return $query->where('type', 'primary');
    }

    /**
     * Scope for billable diagnoses
     */
    public function scopeBillable($query)
    {
        return $query->where('billable', true);
    }

    /**
     * Scope ordered for claim (by dx_order)
     */
    public function scopeForClaim($query)
    {
        return $query->billable()
            ->whereNotNull('dx_order')
            ->orderBy('dx_order');
    }
}
