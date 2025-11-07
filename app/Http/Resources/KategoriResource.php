<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KategoriResource extends JsonResource
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
            'id_kategori'   => $this->id_kategori,
            'nama_kategori' => $this->nama_kategori,
            // jika Anda menyimpan path gambar kategori di kolom lain, tambahkan di sini,
            // misalnya 'image' => $this->image_path,
        ];
    }
}