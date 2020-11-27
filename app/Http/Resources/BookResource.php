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
          'id' => $this->id,
          'book_number' => $this->book_number,
          'name' => $this->book_name,
          'author' => $this->book_author,
          'img' => $this->book_img,
        ];
    }
}
