<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inspection extends Model
{
    use HasFactory;

    protected $table = 'inspections';

    protected $fillable = [
        'equipment_id',
        'indicator_id',
        'problem_id',
        'baseline',
        'real',
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

    public function problem()
    {
        return $this->belongsTo(Problem::class);
    }
}
