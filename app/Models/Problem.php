<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;

class Problem extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'parent_problem_id',
        'name',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function parentProblem()
    {
        return $this->belongsTo(Problem::class, 'parent_problem_id');
    }

    public function problems()
    {
        return $this->hasMany(Problem::class, 'parent_problem_id');
    }
}
