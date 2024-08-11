<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserWithStockResource extends JsonResource
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
          'name' => $this->name,
          'email' => $this->email,
          'phone' => $this->phone(),
          'image' => empty($this->image) ? null : url($this->image),
          'role' => $this->role,
          'status' => $this->status,
          'enterprise_name' => $this->enterprise_name,
          'longitude' => $this->longitude,
          'latitude' => $this->latitude,
          'state' => new StateResource($this->city->state),
          'city' => new CityResource($this->city),
          'stocks' => new StockCollection($this->stocks()->inRandomOrder()->limit(5)->get())
        ];
    }
}
