<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
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
          'cart_id' => $this->cart_id,
          'phone' => $this->phone(),
          'longitude' => $this->longitude,
          'latitude' => $this->latitude,
          'status' => $this->status,
          'created_at' => $this->created_at,
          'updated_at' => $this->updated_at,
          'invoice' => is_null($this->invoice) ? null :new InvoiceResource($this->invoice),
          'items' => new PaginatedItemCollection($this->cart->items()->paginate(10))
        ];

    }
}
