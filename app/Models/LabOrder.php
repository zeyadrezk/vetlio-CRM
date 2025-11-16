<?php

namespace App\Models;

use App\Traits\Organisationable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabOrder extends Model
{
    use HasFactory, SoftDeletes, Organisationable;

    protected $fillable = [
        'patient_id',
        'ordered_by',
        'medical_document_id',
        'organisation_id',
        'test_name',
        'test_code',
        'test_category',
        'priority',
        'ordered_at',
        'collected_at',
        'resulted_at',
        'status',
        'result_value',
        'result_unit',
        'reference_range',
        'abnormal_flag',
        'performing_lab',
        'specimen_type',
        'specimen_id',
        'reviewed_by',
        'reviewed_at',
        'interpretation',
        'clinical_notes',
        'lab_notes',
    ];

    protected $casts = [
        'ordered_at' => 'datetime',
        'collected_at' => 'datetime',
        'resulted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function orderedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ordered_by');
    }

    public function medicalDocument(): BelongsTo
    {
        return $this->belongsTo(MedicalDocument::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Check if results are available
     */
    public function hasResults(): bool
    {
        return $this->status === 'completed' && !empty($this->result_value);
    }

    /**
     * Check if results are abnormal
     */
    public function isAbnormal(): bool
    {
        return in_array($this->abnormal_flag, ['high', 'low', 'critical_high', 'critical_low', 'abnormal']);
    }

    /**
     * Check if results need review
     */
    public function needsReview(): bool
    {
        return $this->status === 'pending_review' || ($this->hasResults() && !$this->reviewed_at);
    }

    /**
     * Mark as reviewed
     */
    public function markAsReviewed(User $reviewer, string $interpretation = null): void
    {
        $this->update([
            'status' => 'reviewed',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'interpretation' => $interpretation,
        ]);
    }

    /**
     * Scope for pending results
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['ordered', 'specimen_collected', 'in_progress']);
    }

    /**
     * Scope for abnormal results
     */
    public function scopeAbnormal($query)
    {
        return $query->whereIn('abnormal_flag', ['high', 'low', 'critical_high', 'critical_low', 'abnormal']);
    }

    /**
     * Scope for unreviewed results
     */
    public function scopeUnreviewed($query)
    {
        return $query->where('status', 'completed')
            ->whereNull('reviewed_at');
    }

    /**
     * Scope for STAT priority
     */
    public function scopeStat($query)
    {
        return $query->where('priority', 'stat');
    }
}
