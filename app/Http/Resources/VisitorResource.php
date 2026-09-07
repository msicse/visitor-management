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
            "organization"=> $this->organization,
            "phone"=> $this->phone,
            "email"=> $this->email,
            "reason"=> $this->reason,
            "address"=> $this->address,
            "remarks"=> $this->remarks,
            "checkout"=> $this->checkout,
            "in_time"=> $this->in_time ? Carbon::parse($this->in_time)->format("H:i:s") : null,
            "out_time"=> $this->out_time ? Carbon::parse($this->out_time)->format("H:i:s") : null,
            "image"=> $this->image ? Storage::url($this->image) : $this->image,
            "employee" => $this->employee ? new EmployeeResource($this->employee) : null,

        ];
    }
}
