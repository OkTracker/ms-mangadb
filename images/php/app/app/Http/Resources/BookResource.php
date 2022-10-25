<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'title' => $this->title,
            'isbn_10' => $this->isbn_10,
            'isbn_13' => $this->isbn_13,
            'series' => SeriesResource::make($this->series),
            'cover' => $this->cover ? $this->cover : "pending"
        ];
    }
}
