<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\BarangTitipan;
use App\Models\Pembeli;
use App\Models\Keranjang;
use App\Models\Pembayaran;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $pembeli = Auth::user();
        $keranjang = $pembeli->keranjang->barang ?? [];

        $totalHarga = $keranjang->sum('harga_jual');

        return view('checkout.index', compact('pembeli', 'keranjang', 'totalHarga'));
    }

    public function submitCheckout(Request $request)
    {
        $pembeliId = Auth::guard('pembeli')->id();
        $jenisPengiriman = $request->input('jenis_pengiriman');
        $subtotal = $request->input('subtotal');
        $totalBayar = $request->input('total_pembayaran');
        $poinDitukar = $request->input('poin_tukar') ?? 0;
        $idAlamat = $request->input('id_alamat'); // ambil id_alamat dari request

        DB::beginTransaction();

        try {
            // Hitung poin dasar dan bonus
            $poinDasar = floor($totalBayar / 10000);
            $bonus = $totalBayar > 500000 ? floor($poinDasar * 0.2) : 0;
            $poinDidapat = $poinDasar + $bonus;

            // 1. Simpan transaksi utama dengan poin_didapat, poin_digunakan, dan id_alamat
            $transaksi = Transaksi::create([
                'id_pembeli'        => $pembeliId,
                'tanggal_transaksi' => Carbon::now(),
                'total_pembayaran'  => $totalBayar,
                'status_transaksi'  => 'Menunggu Pembayaran',
                'jenis_pengiriman'  => $jenisPengiriman,
                'nomor_transaksi'   => '',
                'poin_didapat'      => $poinDidapat,
                'poin_digunakan'    => $poinDitukar,
                'id_alamat'         => $idAlamat,
            ]);

            // 2. Generate No Nota: yy.mm.xxx
            $totalTransaksi = Transaksi::count() + 1;
            $transaksi->nomor_transaksi = now()->format('y.m.') . str_pad($totalTransaksi, 3, '0', STR_PAD_LEFT);
            $transaksi->save();

            // 3. Ambil isi keranjang pembeli
            $keranjangIds = Keranjang::where('id_pembeli', $pembeliId)->pluck('id_keranjang');

            $barangList = DB::table('detail_keranjang')
                ->join('barang_titipan', 'detail_keranjang.id_barang', '=', 'barang_titipan.id_barang')
                ->whereIn('detail_keranjang.id_keranjang', $keranjangIds)
                ->select('barang_titipan.id_barang', 'barang_titipan.harga_jual')
                ->get();

            foreach ($barangList as $barang) {
                // 4. Simpan detail transaksi
                DetailTransaksi::create([
                    'id_transaksi' => $transaksi->id_transaksi,
                    'id_barang'    => $barang->id_barang,
                    'sub_total'    => $barang->harga_jual
                ]);

                // 5. Ubah status barang jadi Terjual
                BarangTitipan::where('id_barang', $barang->id_barang)->update([
                    'status_barang' => 'Terjual'
                ]);
            }

            // 6. Kurangi poin jika dipakai
            if ($poinDitukar > 0) {
                $pembeli = Pembeli::find($pembeliId);

                if ($pembeli->poin >= $poinDitukar) {
                    $pembeli->poin -= $poinDitukar;
                    $pembeli->save();
                } else {
                    return back()->with('error', 'Poin tidak cukup!');
                }
            }

            // 7. Hapus keranjang
            DB::table('detail_keranjang')->whereIn('id_keranjang', $keranjangIds)->delete();

            // 8. Simpan data pembayaran ke tabel pembayaran
            // id_pegawai diisi null/0, status_verifikasi 0 (belum diverifikasi)
            Pembayaran::create([
                'id_pembeli' => $pembeliId,
                'id_pegawai' => null, // atau 0 sesuai tipe data
                'id_transaksi' => $transaksi->id_transaksi,
                'bukti_transfer' => null, // belum ada bukti transfer saat checkout
                'status_verifikasi' => 0,
            ]);

            DB::commit();

            return redirect()->route('pembayaran.show', ['id_transaksi' => $transaksi->id_transaksi])
                            ->with('success', 'Checkout berhasil. Silakan lakukan pembayaran.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Checkout gagal: ' . $e->getMessage());
        }
    }

    public function showCheckout()
    {
        $idPembeli = Auth::guard('pembeli')->id();
        $pembeli = Auth::guard('pembeli')->user();

        // Ambil semua id_keranjang milik pembeli
        $keranjangIds = Keranjang::where('id_pembeli', $idPembeli)->pluck('id_keranjang');

        // Ambil semua barang dalam keranjang beserta foto utama (urutan 1)
        $barangs = BarangTitipan::join('detail_keranjang', 'barang_titipan.id_barang', '=', 'detail_keranjang.id_barang')
            ->leftJoin('foto_barang', function($join) {
                $join->on('barang_titipan.id_barang', '=', 'foto_barang.id_barang')
                    ->where('foto_barang.urutan', '=', 1);
            })
            ->whereIn('detail_keranjang.id_keranjang', $keranjangIds)
            ->select(
                'barang_titipan.id_barang',
                'barang_titipan.nama_barang',
                'barang_titipan.deskripsi',
                'barang_titipan.harga_jual',
                'foto_barang.nama_file as foto_utama'
            )
            ->get();

        // Ambil semua alamat milik pembeli
        $alamatList = $pembeli->alamat_pembelis ?? collect();

        $subtotal = $barangs->sum('harga_jual');

        return view('checkout', [
            'items' => $barangs,
            'poin' => $pembeli->poin,
            'alamatList' => $alamatList,
            'subtotal' => $subtotal,
        ]);
    }
}