<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitOfficialInfo extends Model
{
    protected $fillable = [
        'unit_id',
        'directorate','village',
        'basin_number','basin_name',
        'plot_number','apartment_number',
        'areas',
    ];

    protected $casts = [
        'areas' => 'array',
    ];

    public function unit()
    {
        return $this->belongsTo(\App\Models\Unit::class);
    }
}
