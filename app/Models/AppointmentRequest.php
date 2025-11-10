<?php

namespace App\Models;

use App\Traits\Organisationable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppointmentRequest extends Model
{
    /** @use HasFactory<\Database\Factories\AppointmentRequestFactory> */
    use HasFactory, SoftDeletes, Organisationable;

    protected $fillable = [
        'client_id',
        'service_id',
        'branch_id',
        'service_provider_id',
        'patient_id',
        'date',
        'from',
        'to',
        'note',
        'reason_for_coming',
        'approval_status_id',
        'approval_at',
        'approval_note',
        'approval_by',
        'organisation_id',
    ];

    protected $casts = [
        'date' => 'date',
        'from' => 'timestamp',
        'to' => 'timestamp',
        'approval_at' => 'timestamp',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function serviceProvider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'service_provider_id');;
    }


}
