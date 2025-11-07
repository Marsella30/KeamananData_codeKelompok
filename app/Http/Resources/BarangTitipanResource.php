<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BarangTitipanResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id_barang'             => $this->id_barang,
            'id_penitip'            => $this->id_penitip,
            'id_pegawai'            => $this->id_pegawai,
            'id_qc_pegawai'         => $this->id_qc_pegawai,
            'id_hunter'             => $this->id_hunter,
            'id_kategori'           => $this->id_kategori,
            'tanggal_masuk'         => optional($this->tanggal_masuk)->toDateTimeString(),
            'tanggal_akhir'         => optional($this->tanggal_akhir)->toDateTimeString(),
            'tanggal_keluar'        => optional($this->tanggal_keluar)->toDateTimeString(),
            'status_perpanjangan'   => (bool) $this->status_perpanjangan,
            'nama_barang'           => $this->nama_barang,
            'harga_jual'            => (float) $this->harga_jual,
            'deskripsi'             => $this->deskripsi,
            'foto_barang'           => $this->foto_barang,
            'status_barang'         => $this->status_barang,
            'garansi'               => (bool) $this->garansi,
            'tanggal_garansi'       => optional($this->tanggal_garansi)->toDateTimeString(),
            'barang_hunter'         => (bool) $this->barang_hunter,
            'berat'                 => (float) $this->berat,
            'id_nota'               => $this->id_nota,

            // relasi—jika ingin disertakan
            'kategori'              => new KategoriResource($this->whenLoaded('kategori')),
            // 'penitip'             => new PenitipResource($this->whenLoaded('penitip')),
            // 'foto_lengkap'        => FotoBarangResource::collection($this->whenLoaded('fotoBarang')),
        ];
    }
}
