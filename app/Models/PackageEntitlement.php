<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageEntitlement extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'feature',
        'type',
        'limit_value',
    ];

    protected $casts = [
        'limit_value' => 'integer',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
