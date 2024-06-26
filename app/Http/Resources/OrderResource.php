<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
      return [
        'id' => $this->id,
        //'cart_id' => $this->cart_id,
        'phone' => $this->phone(),
        'longitude' => $this->longitude,
        'latitude' => $this->latitude,
        'created_at' => date_format($this->created_at,'Y-m-d H:i:s'),
        'updated_at' => date_format($this->updated_at,'Y-m-d H:i:s'),
        //'invoice' => is_null($this->invoice) ? null :new InvoiceResource($this->invoice),
        'status' => $this->histories()->latest()->first()->status,
        'history' => new HistoryCollection($this->histories),
        'buyer' => new UserResource($this->buyer),
        'seller' => new UserResource($this->seller),
        'driver' => new UserResource($this->delivery?->driver),
        'cart' => new CartResource($this->cart)
      ];
    }
}
