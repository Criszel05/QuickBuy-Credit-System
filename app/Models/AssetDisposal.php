<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetDisposal extends Model
{
    protected $fillable = [
        'asset_id',
        'recorded_by',
        'disposal_method',
        'reason',
        'retirement_date',
        'disposal_date',
        'disposal_value',
        'status',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'retirement_date' => 'date',
            'disposal_date' => 'date',
            'disposal_value' => 'decimal:2',
        ];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
