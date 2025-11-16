<?php

namespace App\Models;

use App\Traits\Organisationable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VitalSigns extends Model
{
    use HasFactory, Organisationable;

    protected $fillable = [
        'patient_id',
        'medical_document_id',
        'measured_by',
        'organisation_id',
        'measured_at',
        'blood_pressure_systolic',
        'blood_pressure_diastolic',
        'heart_rate',
        'respiratory_rate',
        'temperature',
        'temperature_route',
        'oxygen_saturation',
        'height',
        'weight',
        'bmi',
        'head_circumference',
        'pain_level',
        'pain_location',
        'blood_glucose',
        'peak_flow',
        'patient_position',
        'notes',
        'flagged_abnormal',
    ];

    protected $casts = [
        'measured_at' => 'datetime',
        'temperature' => 'decimal:2',
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
        'bmi' => 'decimal:2',
        'head_circumference' => 'decimal:2',
        'flagged_abnormal' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($vitalSigns) {
            // Auto-calculate BMI if height and weight are present
            if ($vitalSigns->height && $vitalSigns->weight && !$vitalSigns->bmi) {
                $vitalSigns->bmi = $vitalSigns->calculateBMI();
            }

            // Auto-flag abnormal values
            $vitalSigns->flagged_abnormal = $vitalSigns->hasAbnormalValues();
        });
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function medicalDocument(): BelongsTo
    {
        return $this->belongsTo(MedicalDocument::class);
    }

    public function measuredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'measured_by');
    }

    /**
     * Calculate BMI from height (cm) and weight (kg)
     */
    public function calculateBMI(): ?float
    {
        if (!$this->height || !$this->weight) {
            return null;
        }

        // BMI = weight (kg) / (height (m))^2
        $heightInMeters = $this->height / 100;
        return round($this->weight / ($heightInMeters ** 2), 2);
    }

    /**
     * Get blood pressure as string (systolic/diastolic)
     */
    public function getBloodPressureAttribute(): ?string
    {
        if (!$this->blood_pressure_systolic || !$this->blood_pressure_diastolic) {
            return null;
        }

        return $this->blood_pressure_systolic . '/' . $this->blood_pressure_diastolic;
    }

    /**
     * Check if any vital signs are abnormal
     */
    public function hasAbnormalValues(): bool
    {
        // Blood pressure (hypertension if >= 140/90)
        if ($this->blood_pressure_systolic && $this->blood_pressure_systolic >= 140) {
            return true;
        }
        if ($this->blood_pressure_diastolic && $this->blood_pressure_diastolic >= 90) {
            return true;
        }

        // Heart rate (abnormal if < 60 or > 100 for adults)
        if ($this->heart_rate && ($this->heart_rate < 60 || $this->heart_rate > 100)) {
            return true;
        }

        // Respiratory rate (abnormal if < 12 or > 20 for adults)
        if ($this->respiratory_rate && ($this->respiratory_rate < 12 || $this->respiratory_rate > 20)) {
            return true;
        }

        // Temperature (fever if > 37.5°C or 99.5°F)
        if ($this->temperature && $this->temperature > 37.5) {
            return true;
        }

        // Oxygen saturation (abnormal if < 95%)
        if ($this->oxygen_saturation && $this->oxygen_saturation < 95) {
            return true;
        }

        // BMI (underweight if < 18.5, overweight if > 25)
        $bmi = $this->bmi ?? $this->calculateBMI();
        if ($bmi && ($bmi < 18.5 || $bmi > 25)) {
            return true;
        }

        return false;
    }

    /**
     * Get BMI category
     */
    public function getBMICategoryAttribute(): ?string
    {
        $bmi = $this->bmi ?? $this->calculateBMI();

        if (!$bmi) {
            return null;
        }

        if ($bmi < 18.5) {
            return 'Underweight';
        } elseif ($bmi < 25) {
            return 'Normal weight';
        } elseif ($bmi < 30) {
            return 'Overweight';
        } else {
            return 'Obese';
        }
    }
}
