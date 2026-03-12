<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitOwner extends Model
{
    protected $fillable = ['unit_id', 'name', 'phone', 'email', 'notes'];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
