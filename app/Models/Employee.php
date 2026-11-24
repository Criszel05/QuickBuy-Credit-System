<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'employee_code',
        'name',
        'position',
        'phone',
        'email',
        'address',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function assetAssignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function transfersFrom(): HasMany
    {
        return $this->hasMany(
            AssetTransfer::class,
            'from_employee_id'
        );
    }

    public function transfersTo(): HasMany
    {
        return $this->hasMany(
            AssetTransfer::class,
            'to_employee_id'
        );
    }
}
