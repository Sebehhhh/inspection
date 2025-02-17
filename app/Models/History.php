<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $table = 'inspection_histories';
    protected $fillable = [
        'equipment_id',
        'indicator_id',
        'actual_value',
        'status',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function indicator()
    {
        return $this->belongsTo(Indicator::class);
    }
}
