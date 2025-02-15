<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Indicator extends Model
{
   protected $table = 'indicators';
   protected $fillable = ['name', 'equipment_id', 'unit'];

   public function equipment()
   {
      return $this->belongsTo(Equipment::class);
   }
}
