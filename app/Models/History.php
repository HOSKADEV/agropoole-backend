<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class History extends Model
{
    use HasFactory,SoftDeletes, SoftCascadeTrait;

    protected $fillable = [
      'order_id',
      'user_id',
      'status'
      ];


      public function user() {
        return $this->belongsTo(User::class)->withTrashed();
      }

      public function order(){
        return $this->belongsTo(Order::class);
      }

      public function created_at(){
      $timestamp = Carbon::parse($this->created_at);
        return ucfirst($timestamp->dayName) . ', '. $timestamp->format('Y-m-d H:i');
      }

      public function message(){
        $user = $this->user->enterprise();

        return match ($this->status) {
          'pending' => "{$user} a créé la demande.",
          'accepted' => "{$user} a accepté la commande.",
          'canceled' => "{$user} a annulé la commande.",
          'confirmed' => "{$user} a confirmé la commande.",
          'shipped' => "{$user} a expédié la commande.",
          'ongoing' => "{$user} a pris en charge la livraison.",
          'delivered' => "{$user} a livré la commande.",
          'received' => "{$user} a reçue la commande.",
        };
      }
}
