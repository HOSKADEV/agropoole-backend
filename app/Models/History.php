<?php

namespace App\Models;

use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class History extends Model
{
    use HasFactory,SoftDeletes, SoftCascadeTrait;

    protected $fillable = [
      'order_id',
      'user_id',
      'status'
      ];


      public function user() {
        return $this->belongsTo(User::class);
      }

      public function order(){
        return $this->belongsTo(Order::class);
      }
}
