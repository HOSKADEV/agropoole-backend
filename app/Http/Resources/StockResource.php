<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
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
          "id"=> $this->id,
          'product' => new ProductResource($this->product),
          'quantity' => $this->quantity,
          'price' => $this->price,
          'min_quantity' => $this->min_quantity,
          'show_price' => $this->show_price,
          'status' => $this->status,
        ];
    }
}
