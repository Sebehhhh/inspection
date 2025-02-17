<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    protected $fillable = ['indicator_id', 'problem_id'];

    public function indicator()
    {
        return $this->belongsTo(Indicator::class);
    }

    public function problem()
    {
        return $this->belongsTo(Problem::class);
    }
}
