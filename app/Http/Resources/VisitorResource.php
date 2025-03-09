<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use Carbon\Carbon;
use Storage;

class VisitorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=> $this->id,
            "name"=> $this->name,
            "visitor_card_id"=> $this->visitor_card_id,
            "factory_name"=> $this->factory_name,
            "phone"=> $this->phone,
            "email"=> $this->email,
            "reason"=> $this->reason,
            "address"=> $this->address,
            "remarks"=> $this->remarks,
            "checkout"=> $this->checkout,
            "in_time"=> ( new Carbon($this->in_time))->format("H:i:s"),
            "out_time"=> ( new Carbon($this->out_time))->format("H:i:s"),
            "image"=> $this->image ? Storage::url($this->image) : $this->image,
            "employee" => $this->employee ? new EmployeeResource($this->employee_id) : null,

        ];
    }
}
