<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceRecord extends Model
{
    protected $fillable = [
        'maintenance_number',
        'asset_id',
        'reported_by',
        'problem_description',
        'maintenance_type',
        'status',
        'technician',
        'reported_date',
        'completed_date',
        'repair_cost',
        'repair_notes',
    ];

    protected function casts(): array
    {
        return [
            'reported_date' => 'date',
            'completed_date' => 'date',
            'repair_cost' => 'decimal:2',
        ];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
