<?php

namespace App\Http\Resources;

use App\Models\Invoice;
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
      $invoice = $this->invoice;

      if(empty($invoice)){
        $invoice = Invoice::create(['order_id' => $this->id]);
        $invoice->total();
      }


      return [
        'id' => $this->id,
        //'cart_id' => $this->cart_id,
        'phone' => $this->phone(),
        'longitude' => $this->longitude,
        'latitude' => $this->latitude,
        'with_delivery' => $this->with_delivery,
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
        'purchase_amount' => $invoice->purchase_amount,
        'tax_amount' => $invoice->tax_amount,
        'total_amount' => $invoice->total_amount,
        //'invoice' => is_null($this->invoice) ? null :new InvoiceResource($this->invoice),
        'status' => $this->histories()->latest()->first()?->status ?? $this->status,
        'history' => new HistoryCollection($this->histories),
        'buyer' => new UserResource($this->buyer),
        'seller' => new UserResource($this->seller),
        'driver' => in_array($this->status, ['shipped','ongoing','delivered','received']) ? new UserResource($this->delivery?->driver) : null,
        'cart' => new CartResource($this->cart)
      ];
    }
}
