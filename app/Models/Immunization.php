<?php

namespace App\Models;

use App\Traits\Organisationable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Immunization extends Model
{
    use HasFactory, SoftDeletes, Organisationable;

    protected $fillable = [
        'patient_id',
        'administered_by',
        'medical_document_id',
        'organisation_id',
        'vaccine_name',
        'vaccine_code',
        'vaccine_product',
        'manufacturer',
        'lot_number',
        'expiration_date',
        'administered_date',
        'dose_amount',
        'dose_number',
        'series_total',
        'administration_site',
        'route',
        'vis_version',
        'vis_provided_date',
        'next_due_date',
        'next_dose_instructions',
        'consent_obtained',
        'consent_signed_by',
        'refused',
        'refusal_reason',
        'adverse_reaction_reported',
        'adverse_reaction_description',
        'reported_to_registry',
        'funding_source',
        'notes',
    ];

    protected $casts = [
        'expiration_date' => 'date',
        'administered_date' => 'date',
        'vis_provided_date' => 'date',
        'next_due_date' => 'date',
        'consent_obtained' => 'boolean',
        'refused' => 'boolean',
        'adverse_reaction_reported' => 'boolean',
        'reported_to_registry' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function administeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'administered_by');
    }

    public function medicalDocument(): BelongsTo
    {
        return $this->belongsTo(MedicalDocument::class);
    }

    public function consentSignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'consent_signed_by');
    }

    /**
     * Check if this is part of a series
     */
    public function isPartOfSeries(): bool
    {
        return $this->series_total && $this->series_total > 1;
    }

    /**
     * Check if series is complete
     */
    public function isSeriesComplete(): bool
    {
        if (!$this->isPartOfSeries()) {
            return true;
        }

        return $this->dose_number >= $this->series_total;
    }

    /**
     * Check if next dose is due
     */
    public function isNextDoseDue(): bool
    {
        if (!$this->next_due_date) {
            return false;
        }

        return $this->next_due_date->isPast();
    }

    /**
     * Check if vaccine is overdue
     */
    public function isOverdue(): bool
    {
        if (!$this->next_due_date) {
            return false;
        }

        // Consider overdue if more than 30 days past due date
        return $this->next_due_date->diffInDays(now(), false) > 30;
    }

    /**
     * Get days until next dose
     */
    public function getDaysUntilNextDoseAttribute(): ?int
    {
        if (!$this->next_due_date) {
            return null;
        }

        return (int) now()->diffInDays($this->next_due_date, false);
    }

    /**
     * Scope for due vaccines
     */
    public function scopeDue($query)
    {
        return $query->whereNotNull('next_due_date')
            ->where('next_due_date', '<=', now());
    }

    /**
     * Scope for upcoming vaccines (due within next 30 days)
     */
    public function scopeUpcoming($query)
    {
        return $query->whereNotNull('next_due_date')
            ->where('next_due_date', '>', now())
            ->where('next_due_date', '<=', now()->addDays(30));
    }

    /**
     * Scope for refused vaccines
     */
    public function scopeRefused($query)
    {
        return $query->where('refused', true);
    }

    /**
     * Scope for vaccines with adverse reactions
     */
    public function scopeWithAdverseReactions($query)
    {
        return $query->where('adverse_reaction_reported', true);
    }
}
