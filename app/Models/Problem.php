<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;

class Problem extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'equipment_id',
        'parent_problem_id',
        'name',
        'further_testing',
        'corrective_action',
        'possible_cause',
        'action_taken',
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
