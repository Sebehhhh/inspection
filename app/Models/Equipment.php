<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    public $incrementing = false;
    protected $table = 'equipments';
    protected $primaryKey = 'id';
    protected $fillable = ['id','name', 'description'];
}
