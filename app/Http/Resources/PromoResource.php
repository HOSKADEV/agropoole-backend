<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PromoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable|null
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'target_quantity' => $this->target_quantity,
            'new_price' => $this->new_price,
        ];
    }
}
