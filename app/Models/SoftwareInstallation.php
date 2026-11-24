<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SoftwareInstallation extends Model
{
    protected $fillable = [
        'software_license_id',
        'asset_id',
        'installation_date',
        'uninstallation_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'installation_date' => 'date',
            'uninstallation_date' => 'date',
        ];
    }

    public function softwareLicense(): BelongsTo
    {
        return $this->belongsTo(SoftwareLicense::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
