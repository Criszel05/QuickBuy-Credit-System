<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SoftwareLicense extends Model
{
    protected $fillable = [
        'software_name',
        'vendor',
        'license_key',
        'license_type',
        'number_of_licenses',
        'purchase_cost',
        'purchase_date',
        'expiration_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'purchase_cost' => 'decimal:2',
            'purchase_date' => 'date',
            'expiration_date' => 'date',
        ];
    }

    public function installations(): HasMany
    {
        return $this->hasMany(SoftwareInstallation::class);
    }
}
