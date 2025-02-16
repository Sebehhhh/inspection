<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solution extends Model
{
    use HasFactory;

    protected $fillable = [
        'problem_id',
        'further_testing',
        'corrective_actions',
    ];

    /**
     * Get the problem that owns the solution.
     */
    public function problem()
    {
        return $this->belongsTo(Problem::class);
    }
}
