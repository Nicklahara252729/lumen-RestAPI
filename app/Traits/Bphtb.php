<?php

namespace App\Traits;

/**
 * import component
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use NumberFormatter;
use Ramsey\Uuid\Uuid;

/**
 * import traits
 */

use App\Traits\Generator;

trait Bphtb
{
    use Generator;

    /**
     * NTPD
     */
    public function ntpd($nop, $id, $noSts)
    {
        $custCode = $this->year . $this->nomorUrutBrivaBphtb($nop, $id);
        $getBriva = DB::connection('third_mysql')->table('briva_report')->select(DB::raw('CONCAT(id,brivaNo) AS ntpd'))->where('custCode', $custCode)->first();
        $getStsHistory = DB::connection('second_mysql')->table('STS_History')->select('Status_Bayar', 'Kode_Pengesahan')->where(['No_Pokok_WP' => $nop, 'No_STS' => $noSts])->first();
        $statusBayar = is_null($getStsHistory) && is_null($getBriva) ? 0 : (!is_null($getBriva) ? 1 : ($getStsHistory->Status_Bayar == 1 ? 1 : 0));
        $ntpd = $statusBayar == 0 ? 0 : (!is_null($getBriva) ? $getBriva->ntpd : $getStsHistory->Kode_Pengesahan);
        return ['ntpd' => $ntpd, 'status_bayar' => $statusBayar];
    }

    /**
     * status verifikasi
     */
    public function statusVerifikasi($statusVerifikasi)
    {
        if ($statusVerifikasi == '0') :
            $return = ['value' => $statusVerifikasi, 'keterangan' => 'Permohonan baru'];
        elseif ($statusVerifikasi == '1') :
            $return = ['value' => $statusVerifikasi, 'keterangan' => 'Verifikasi'];
        elseif ($statusVerifikasi == '2') :
            $return = ['value' => $statusVerifikasi, 'keterangan' => 'Validasi'];
        elseif ($statusVerifikasi == '3') :
            $return = ['value' => $statusVerifikasi, 'keterangan' => 'Penetapan'];
        elseif ($statusVerifikasi == '4') :
            $return = ['value' => $statusVerifikasi, 'keterangan' => 'Dibatalkan'];
        elseif ($statusVerifikasi == '5') :
            $return = ['value' => $statusVerifikasi, 'keterangan' => 'Terkirim ke BPN'];
        elseif ($statusVerifikasi == '0.1' || $statusVerifikasi == '1.1' || $statusVerifikasi == '2.1') :
            $return = ['value' => $statusVerifikasi, 'keterangan' => 'Ditolak'];
        elseif ($statusVerifikasi == '0.2' || $statusVerifikasi == '1.2' || $statusVerifikasi == '2.2') :
            $return = ['value' => $statusVerifikasi, 'keterangan' => 'Perbaikan'];
        endif;
        return $return;
    }

    /**
     * query get all data
     */
    public function queryAllData($condition, $pageSize, $deleted)
    {
        $combineCondition = authAttribute()['role'] == 'notaris' ? $condition . ' AND created_by = "' . authAttribute()['id'] . '"' : $condition;
        $data = $this->pelayananBphtb->select(
            "pelayanan_bphtb.id",
            "uuid_pelayanan_bphtb",
            "no_registrasi",
            "nop",
            "pelayanan_bphtb.created_at",
            "nilai_transaksi",
            "status_verifikasi",
            "pelayanan_bphtb.updated_at",
            "nama_wp_2",
            "npopkp",
            "npoptkp",
            "nilai_bphtb",
            "pengurangan",
            "nop",
            "no_sts"
        )
            ->selectRaw('(SELECT name FROM users WHERE uuid_user = pelayanan_bphtb.created_by) AS pendaftar')
            ->selectRaw('(SELECT name FROM users WHERE uuid_user = pelayanan_bphtb.updated_by) AS pengubah');
        if (isset($deleted)) :
            $data = $data->selectRaw('deleted_at')
                ->selectRaw('(SELECT name FROM users WHERE uuid_user = pelayanan_bphtb.deleted_by) AS penghapus')
                ->whereNotNull('deleted_at');
        else :
            $data = $data->whereNull('deleted_at');
        endif;
        $data = $data->whereRaw($combineCondition)
            ->orderBy('pelayanan_bphtb.id', 'desc')
            ->get();

        $output = [];
        foreach ($data as $key => $value) :

            /**
             * verifikasi status
             */
            $statusVerifikasi = $this->statusVerifikasi($value->status_verifikasi);

            /**
             * get dat objek pajak
             */
            $getDatObjekPajak = $this->datObjekPajak->select('JALAN_OP')
                ->whereRaw('CONCAT(KD_PROPINSI, KD_DATI2, KD_KECAMATAN, KD_KELURAHAN, KD_BLOK, NO_URUT, KD_JNS_OP) = "' . $value->nop . '"')
                ->first();

            /**
             * convert tanggal
             */
            $tanggalPendaftaran = Carbon::parse($value->created_at)->locale('id');
            $tanggalPendaftaran->settings(['formatFunction' => 'translatedFormat']);
            $tanggalPerubahan = Carbon::parse($value->updated_at)->locale('id');
            $tanggalPerubahan->settings(['formatFunction' => 'translatedFormat']);

            /**
             * get riwayat penolakan
             */
            $getRiwayatPenolakan = $this->riwayatDitolakBphtb->select('keterangan')
                ->where('no_registrasi', $value->no_registrasi)
                ->orderBy('id', 'desc')
                ->first();
            $pesanPenolakan = is_null($getRiwayatPenolakan) ? null : $getRiwayatPenolakan->keterangan;

            /**
             * get status bayar dan ntpd
             */
            $ntpd = $this->ntpd($value->nop, $value->id, $value->no_sts);

            /**
             * set output
             */
            $set = [
                "uuid_pelayanan" => $value->uuid_pelayanan_bphtb,
                "nop" => $value->nop,
                "no_registrasi" => $value->no_registrasi,
                "nama_pembeli" => $value->nama_wp_2,
                "pendaftar" => $value->pendaftar,
                'tanggal_pendaftaran' => $tanggalPendaftaran->format('l, j F Y ; h:i:s a'),
                "nilai_transaksi" => $value->nilai_transaksi,
                "alamat" => is_null($getDatObjekPajak) ? null : $getDatObjekPajak->JALAN_OP,
                "status_verifikasi" => $statusVerifikasi,
                "updated_at" => $tanggalPerubahan->format('l, j F Y ; h:i:s a'),
                "pesan_ditolak" => $pesanPenolakan,
                "npopkp" => $value->npopkp,
                "npoptkp" => $value->npoptkp,
                "nilai_bphtb" => $value->nilai_bphtb,
                "pengurangan" => is_null($value->pengurangan) ? 0 : (int)$value->pengurangan,
                "status_bayar" => $ntpd['status_bayar'] == 0 ? 'belum' : 'sudah',
                "ntpd" => $ntpd['ntpd'],
                "updated_by" => $value->pengubah
            ];

            if (isset($deleted)) :
                /**
                 * convert tanggal
                 */
                $tanggalHapus = Carbon::parse($value->deleted_at)->locale('id');
                $tanggalHapus->settings(['formatFunction' => 'translatedFormat']);

                $set["deleted_by"] = $value->penghapus;
                $set["deleted_at"] = $tanggalHapus->format('l, j F Y ; h:i:s a');
            endif;

            array_push($output, $set);
        endforeach;
        $collectionObject = collect($output);
        $pageSize = is_null($pageSize) ? 30 : $pageSize;
        $dataPaginate = $this->paginateHelpers->paginate($collectionObject, $pageSize);

        return $dataPaginate;
    }

    /**
     * query get single data
     */
    public function queryGetSingleData($param)
    {
        /**
         * process
         */
        $getData = $this->pelayananBphtb->select(
            'pelayanan_bphtb.id',
            'uuid_pelayanan_bphtb',
            'no_registrasi',
            'nop',
            'no_hp_wp_1',
            'nilai_transaksi',
            'nik',
            'no_hp_wp_2',
            'nama_wp_2',
            'alamat_wp_2',
            DB::raw('CONCAT("' . url($this->storage) . '/", ktp) AS ktp'),
            DB::raw('CONCAT("' . url($this->storage) . '/", foto_op) AS foto_op'),
            DB::raw('CONCAT("' . url($this->storage) . '/", sertifikat_tanah) AS sertifikat_tanah'),
            DB::raw('CONCAT("' . url($this->storage) . '/", fc_sppt_thn_berjalan) AS fc_sppt_thn_berjalan'),
            DB::raw('CONCAT("' . url($this->storage) . '/", fc_sk_jual_beli) AS fc_sk_jual_beli'),
            DB::raw('CONCAT("' . url($this->storage) . '/", perjanjian_kredit) AS perjanjian_kredit'),
            DB::raw('CONCAT("' . url($this->storage) . '/", surat_pernyataan) AS surat_pernyataan'),
            DB::raw('CONCAT("' . url($this->storage) . '/", fc_surat_kematian) AS fc_surat_kematian'),
            DB::raw('CONCAT("' . url($this->storage) . '/", fc_sk_ahli_waris) AS fc_sk_ahli_waris'),
            DB::raw('CONCAT("' . url($this->storage) . '/", sp_ganti_rugi) AS sp_ganti_rugi'),
            DB::raw('CONCAT("' . url($this->storage) . '/", sk_bpn) AS sk_bpn'),
            DB::raw('CONCAT("' . url($this->storage) . '/", fc_sk_hibah_desa) AS fc_sk_hibah_desa'),
            DB::raw('CONCAT("' . url($this->storage) . '/", risalah_lelang) AS risalah_lelang'),
            'created_by',
            'pelayanan_bphtb.uuid_jenis_perolehan',
            'status_verifikasi',
            'pelayanan_bphtb.id_provinsi',
            'pelayanan_bphtb.id_kabupaten',
            'pelayanan_bphtb.id_kecamatan',
            'pelayanan_bphtb.id_kelurahan',
            DB::raw('(SELECT nama_provinsi FROM provinsi WHERE id_provinsi = pelayanan_bphtb.id_provinsi) AS nama_provinsi'),
            DB::raw('(SELECT nama_kabupaten FROM kabupaten WHERE id_kabupaten = pelayanan_bphtb.id_kabupaten) AS nama_kabupaten'),
            DB::raw('(SELECT nama_kecamatan FROM kecamatan WHERE id_kecamatan = pelayanan_bphtb.id_kecamatan) AS nama_kecamatan'),
            DB::raw('(SELECT nama_kelurahan FROM kelurahan WHERE id_kelurahan = pelayanan_bphtb.id_kelurahan AND id_kecamatan = pelayanan_bphtb.id_kecamatan) AS nama_kelurahan'),
            'pelayanan_bphtb.dph_id_provinsi',
            'pelayanan_bphtb.dph_id_kabupaten',
            'pelayanan_bphtb.dph_id_kecamatan',
            'pelayanan_bphtb.dph_id_kelurahan',
            DB::raw('(SELECT nama_provinsi FROM provinsi WHERE id_provinsi = pelayanan_bphtb.dph_id_provinsi) AS dph_nama_provinsi'),
            DB::raw('(SELECT nama_kabupaten FROM kabupaten WHERE id_kabupaten = pelayanan_bphtb.dph_id_kabupaten) AS dph_nama_kabupaten'),
            DB::raw('(SELECT NM_KECAMATAN FROM ref_kecamatan WHERE KD_KECAMATAN = MID(pelayanan_bphtb.nop, 5, 3)) AS dph_nama_kecamatan'),
            DB::raw('(SELECT NM_KELURAHAN FROM ref_kelurahan WHERE KD_KECAMATAN = MID(pelayanan_bphtb.nop, 5, 3) AND KD_KELURAHAN = MID(pelayanan_bphtb.nop, 8, 3)) AS dph_nama_kelurahan'),
            'dph_nama',
            'dph_nomor',
            'dph_npwp',
            'dph_alamat',
            'dph_nik',
            DB::raw('(SELECT name FROM users WHERE uuid_user = pelayanan_bphtb.created_by) AS notaris'),
            DB::raw('(SELECT jenis_perolehan FROM jenis_perolehan WHERE uuid_jenis_perolehan = pelayanan_bphtb.uuid_jenis_perolehan) AS jenis_perolehan'),
            DB::raw('(SELECT id FROM jenis_perolehan WHERE uuid_jenis_perolehan = pelayanan_bphtb.uuid_jenis_perolehan) AS id_jenis_perolehan'),
            'no_sertifikat',
            DB::raw("DATE_FORMAT(pelayanan_bphtb.created_at, '%d %M %Y') AS tgl_dibuat"),
            'npop',
            'njop_tanah',
            'njop_bangunan',
            'luas_njop_tanah',
            'luas_njop_bangunan',
            'luas_tanah',
            'luas_bangunan',
            'npopkp',
            'npoptkp',
            'no_sts',
            'pengurangan',
            'nilai_bphtb_pengurangan',
            'ket_peraturan'
        )
            ->where('uuid_pelayanan_bphtb', $param)
            ->orWhere('no_registrasi', $param)
            ->first();

        /**
         * region 
         */
        $getData['provinsi'] = ['id' => $getData->id_provinsi, 'nama' => $getData->nama_provinsi];
        $getData['kabupaten'] = ['id' => $getData->id_kabupaten, 'nama' => $getData->nama_kabupaten];
        $getData['kecamatan'] = ['id' => $getData->id_kecamatan, 'nama' => $getData->nama_kecamatan];
        $getData['kelurahan'] = ['id' => $getData->id_kelurahan, 'nama' => $getData->nama_kelurahan];

        /**
         * region dph
         */
        $getData['dph_provinsi'] = ['id' => $getData->dph_id_provinsi, 'nama' => $getData->dph_nama_provinsi];
        $getData['dph_kabupaten'] = ['id' => $getData->dph_id_kabupaten, 'nama' => $getData->dph_nama_kabupaten];
        $getData['dph_kecamatan'] = ['id' => $getData->dph_id_kecamatan, 'nama' => $getData->dph_nama_kecamatan];
        $getData['dph_kelurahan'] = ['id' => $getData->dph_id_kelurahan, 'nama' => $getData->dph_nama_kelurahan];

        /**
         * status verifikasi
         */
        $getData['status_verifikasi'] = $this->statusVerifikasi($getData->status_verifikasi);

        /**
         * hitung piutang
         */
        $dataPiutang = $this->sppt->select(DB::raw('SUM(PBB_YG_HARUS_DIBAYAR_SPPT) as piutang'))
            ->whereRaw('CONCAT(KD_PROPINSI, KD_DATI2, KD_KECAMATAN, KD_KELURAHAN, KD_BLOK, NO_URUT, KD_JNS_OP) = "' . $getData->nop . '"')
            ->where('STATUS_PEMBAYARAN_SPPT', 0)
            ->first();
        $getData['piutang'] = is_null($dataPiutang) ? 0 : (int)$dataPiutang->piutang;

        /**
         * tanggal bayar
         */
        $custCode = $this->year . $this->nomorUrutBrivaBphtb($getData->nop, $getData->id);
        $getBriva = DB::connection('third_mysql')->table('briva_report')->select(DB::raw("DATE_FORMAT(paymentDate, '%d %M %Y') AS tglbayar"))->where('custCode', $custCode)->first();
        $getStsHistory = $this->secondDb->table('STS_History')
            ->select(DB::raw("DATE_FORMAT(Tgl_Bayar, '%d %M %Y') AS tglbayar"), 'Status_Bayar')
            ->where(['No_Pokok_Wp' => $getData->nop, 'No_STS' => $getData->no_sts])
            ->first();
        $getData['tgl_bayar'] = is_null($getStsHistory) && is_null($getBriva) ? null : (!is_null($getBriva) ? $getBriva->tglbayar : $getStsHistory->tglbayar);
        $getData['status_bayar'] = is_null($getStsHistory) && is_null($getBriva) ? 'belum' : (!is_null($getBriva) ? 'sudah' : ($getStsHistory->Status_Bayar == 0 ? 'belum' : 'sudah'));

        /**
         * filter response body
         */
        $getData = collect($getData)->except([
            'id_provinsi',
            'id_kabupaten',
            'id_kecamatan',
            'id_kelurahan',
            'dph_id_provinsi',
            'dph_id_kabupaten',
            'dph_id_kecamatan',
            'dph_id_kelurahan'
        ])->toArray();

        return $getData;
    }

    /**
     * data for detail & print
     */
    public function queryDetail($uuidPelayananBphtb)
    {
        /**
         * get pelayanan bphtb
         */
        $getPelayananBphtb = $this->queryGetSingleData($uuidPelayananBphtb);
        $id = $getPelayananBphtb['id'];
        $nilaiTransaksi = $getPelayananBphtb['nilai_transaksi'];
        $nop = $getPelayananBphtb['nop'];
        $ktp = $getPelayananBphtb['ktp'];
        $fotoOp = $getPelayananBphtb['foto_op'];
        $sertifikatTanah = $getPelayananBphtb['sertifikat_tanah'];
        $fc_sppt_thn_berjalan = $getPelayananBphtb['fc_sppt_thn_berjalan'];
        $perjanjian_kredit = $getPelayananBphtb['perjanjian_kredit'];
        $surat_pernyataan = $getPelayananBphtb['surat_pernyataan'];
        $fc_surat_kematian = $getPelayananBphtb['fc_surat_kematian'];
        $fc_sk_ahli_waris = $getPelayananBphtb['fc_sk_ahli_waris'];
        $sp_ganti_rugi = $getPelayananBphtb['sp_ganti_rugi'];
        $sk_bpn = $getPelayananBphtb['sk_bpn'];
        $fc_sk_hibah_desa = $getPelayananBphtb['fc_sk_hibah_desa'];
        $status_verifikasi = $getPelayananBphtb['status_verifikasi'];
        $risalah_lelang = $getPelayananBphtb['risalah_lelang'];
        $nik = $getPelayananBphtb['nik'];
        $pengurangan = $getPelayananBphtb['pengurangan'];
        $nilaiBphtbPengurangan = $getPelayananBphtb['nilai_bphtb_pengurangan'];
        $ketPeraturan = $getPelayananBphtb['ket_peraturan'];
        
        /**
         * get objek pajak
         */
        $getObjekPajak = $this->datObjekPajak->select(
            'dat_objek_pajak.*',
            'NM_DATI2',
            'NM_KELURAHAN',
            'NM_KECAMATAN'
        )
            ->leftJoin('ref_dati2', 'dat_objek_pajak.KD_DATI2', '=', 'ref_dati2.KD_DATI2')
            ->leftJoin('ref_kecamatan', 'dat_objek_pajak.KD_KECAMATAN', '=', 'ref_kecamatan.KD_KECAMATAN')
            ->leftJoin('ref_kelurahan', function ($join) {
                $join->on('dat_objek_pajak.KD_KECAMATAN', '=', 'ref_kelurahan.KD_KECAMATAN')
                     ->on('dat_objek_pajak.KD_KELURAHAN', '=', 'ref_kelurahan.KD_KELURAHAN');
            })
            ->whereRaw('CONCAT(dat_objek_pajak.KD_PROPINSI, dat_objek_pajak.KD_DATI2, dat_objek_pajak.KD_KECAMATAN, dat_objek_pajak.KD_KELURAHAN, dat_objek_pajak.KD_BLOK, dat_objek_pajak.NO_URUT, dat_objek_pajak.KD_JNS_OP) = "' . $nop . '"')
            ->first();
        if (is_null($getObjekPajak)) :
            throw new \Exception($this->outputMessage('not found', 'objek pajak'));
        endif;

        /**
         * get subjek pajak
         */
        $getSubjekPajak = $this->datSubjekPajak->where('SUBJEK_PAJAK_ID', $getObjekPajak->SUBJEK_PAJAK_ID)->first();
        if (is_null($getSubjekPajak)) :
            throw new \Exception($this->outputMessage('not found', 'subjek pajak'));
        endif;

        /**
         * data A
         */
        $dataA = collect($getPelayananBphtb)->only([
            'nama_wp_2',
            'nik',
            'provinsi',
            'kabupaten',
            'kecamatan',
            'kelurahan',
            'no_registrasi',
            'no_hp_wp_1',
            'nilai_transaksi',
            'no_hp_wp_2',
            'jenis_perolehan',
            'dph_nama',
            'dph_nomor',
            'dph_npwp',
            'dph_alamat',
            'dph_nik',
            'dph_provinsi',
            'dph_kabupaten',
            'dph_kecamatan',
            'dph_kelurahan',
            'notaris',
            'uuid_jenis_perolehan',
            'piutang',
            'no_sertifikat',
            'id_jenis_perolehan',
            'tgl_dibuat',
            'tgl_bayar',
            'alamat_wp_2',
            'status_bayar'
        ])->toArray();
        $dataA['nama_op'] = $getSubjekPajak->NM_WP;
        $dataA['npwp'] = $getSubjekPajak->NPWP;
        $dataA['kode_pos'] = $getSubjekPajak->KD_POS_WP;
        $dataA['alamat_op'] = $getObjekPajak->JAlAN_OP;

        /**
         * data B
         */
        $njopBangunan = !is_null($getPelayananBphtb['njop_bangunan']) ? $getPelayananBphtb['njop_bangunan'] : ($getSubjekPajak->NJOP_BNG == 0 ? 0 : $getSubjekPajak->NJOP_BNG / $getSubjekPajak->TOTAL_LUAS_BNG);
        $njopTanah = !is_null($getPelayananBphtb['njop_tanah']) ? $getPelayananBphtb['njop_tanah'] : ($getSubjekPajak->NJOP_BUMI == 0 ? 0 : $getSubjekPajak->NJOP_BUMI / $getSubjekPajak->TOTAL_LUAS_BUMI);
        $njopPbb = !is_null($getPelayananBphtb['njop_bangunan']) && !is_null($getPelayananBphtb['njop_tanah']) ? $getPelayananBphtb['luas_njop_tanah'] + $getPelayananBphtb['luas_njop_bangunan'] : $getSubjekPajak->NJOP_BUMI + $getSubjekPajak->NJOP_BNG;
        $luasTanah = !is_null($getPelayananBphtb['luas_tanah']) ? $getPelayananBphtb['luas_tanah'] : $getSubjekPajak->TOTAL_LUAS_BUMI;
        $luasNjopTanah = !is_null($getPelayananBphtb['luas_njop_tanah']) ? $getPelayananBphtb['luas_njop_tanah'] : $getSubjekPajak->NJOP_BUMI;
        $luasBangunan = !is_null($getPelayananBphtb['luas_bangunan']) ? $getPelayananBphtb['luas_bangunan'] : $getSubjekPajak->TOTAL_LUAS_BNG;
        $luasNjopBangunan = !is_null($getPelayananBphtb['luas_njop_bangunan']) ? $getPelayananBphtb['luas_njop_bangunan'] : $getSubjekPajak->NJOP_BNG;
        $dataB = [
            'nop' => $nop,
            'letak_tanah_bangunan' => $getObjekPajak->JALAN_OP,
            'kelurahan' => $getObjekPajak->NM_KELURAHAN,
            'kecamatan' => $getObjekPajak->NM_KECAMATAN,
            'rt' => $getObjekPajak->RT_OP. ' - ' . $getObjekPajak->RW_OP,
            'kabupaten' => $getObjekPajak->NM_DATI2,
            'luas_tanah' => (int)$luasTanah,
            'njop_tanah' => (int)$njopTanah,
            'luas_njop_tanah' => (int)$luasNjopTanah,
            'luas_bangunan' => (int)$luasBangunan,
            'njop_bangunan' => (int)$njopBangunan,
            'luas_njop_bangunan' => (int)$luasNjopBangunan,
            'njop_pbb' => (int)$njopPbb,
            'nilai_transaksi' => $nilaiTransaksi,
            'pengurangan'     => (int)$pengurangan,
            'nilai_bphtb_pengurangan' => (int)$nilaiBphtbPengurangan,
            'keterangan_peraturan' => $ketPeraturan
        ];

        /**
         * count jumlah bphtb
         */
        $getBphtb = $this->pelayananBphtb->select(DB::raw("COUNT(*) AS total"), 'uuid_pelayanan_bphtb')
            ->where('nik', $nik)
            ->whereRaw('YEAR(created_at) = "' . $this->year . '"')
            ->orderBy('id', 'ASC')
            ->first();

        /**
         * get kaban
         */
        $getKaban = $this->checkerHelpers->userChecker(['role' => 'kaban']);

        /**
         * data c
         */
        $npop = !is_null($getPelayananBphtb['npop']) ? $getPelayananBphtb['npop'] : ($njopPbb > $nilaiTransaksi ? $njopPbb : $nilaiTransaksi);
        $npoptkp = !is_null($getPelayananBphtb['npoptkp']) ? $getPelayananBphtb['npoptkp'] : ($getBphtb->total > 1 && $getBphtb->uuid_pelayanan_bphtb != $uuidPelayananBphtb ? 0 : $this->npoptkp->orderBy('id', 'desc')->first()->nilai);
        $npopkp = !is_null($getPelayananBphtb['npopkp']) ? $getPelayananBphtb['npopkp'] : $npop - $npoptkp;
        $npopkp = $npopkp < 0 ? 0 : $npopkp;
        $dataC = [
            'npop' => (int)$npop,
            'npoptkp' => (int)$npoptkp,
            'npopkp' => (int)$npopkp,
            'bphtb_terhutang' => $this->bphtbTerhutang($npopkp),
        ];

        /**
         * data d
         */
        $dataD = [
            'ktp' => $ktp,
            'foto_op' => $fotoOp,
            'sertifikat_tanah' => $sertifikatTanah,
            'fc_sppt_thn_berjalan' => $fc_sppt_thn_berjalan,
            'perjanjian_kredit' => $perjanjian_kredit,
            'surat_pernyataan' => $surat_pernyataan,
            'fc_surat_kematian' => $fc_surat_kematian,
            'fc_sk_ahli_waris' => $fc_sk_ahli_waris,
            'sp_ganti_rugi' => $sp_ganti_rugi,
            'sk_bpn' => $sk_bpn,
            'fc_sk_hibah_desa' => $fc_sk_hibah_desa,
            'risalah_lelang' => $risalah_lelang,
            'status_verifikasi' => $status_verifikasi,
        ];

        /**
         * set response
         */
        $jumlahDisetor = $dataB['pengurangan'] > 0 ? $dataB['nilai_bphtb_pengurangan'] : $dataC['bphtb_terhutang'];
        $formatter = new NumberFormatter('id_ID', NumberFormatter::SPELLOUT);
        $data = [
            'id' => $id,
            'uuid_pelayanan_bphtb' => $uuidPelayananBphtb,
            'nop' => $nop,
            'nik' => $nik,
            'a' => $dataA,
            'b' => $dataB,
            'c' => $dataC,
            'd' => $dataD,
            'jumlah_disetor' => $jumlahDisetor,
            'terbilang' => ucwords($formatter->format($jumlahDisetor)) . " Rupiah",
            'kaban' => !is_null($getKaban) ? $getKaban->name : null,
            'no_sts' => $getPelayananBphtb['no_sts']
        ];

        return $data;
    }

    /**
     * uploding files
     */
    public function bptbhUploadingFiles($request, $method, $files = null)
    {
        if ($method == 'update') :
            $ktp = $files['ktp'];
            $fotoOp = $files['fotoOp'];
            $sertifikatTanah = $files['sertifikatTanah'];
            $fcSpptThnBerjalan = $files['fcSpptThnBerjalan'];
            $fcSkJualBeli = $files['fcSkJualBeli'];
            $perjalanKredit = $files['perjalanKredit'];
            $suratPernyataan = $files['suratPernyataan'];
            $fcSuratKematian = $files['fcSuratKematian'];
            $fcSkAhliWaris = $files['fcSkAhliWaris'];
            $spGantiRugi = $files['spGantiRugi'];
            $skBpn = $files['skBpn'];
            $fcSkHibahDesa = $files['fcSkHibahDesa'];
            $risalahLelang = $files['risalahLelang'];
        endif;

        /**
         * ktp
         */
        if (isset($_FILES['ktp'])) :

            /**
             * remove file
             */
            if ($method == 'update') :
                if (!is_null($ktp) && file_exists($this->storage . "/" . $ktp)) :
                    unlink($this->storage . "/" . $ktp);
                endif;
            endif;

            /**
             * upload
             */
            $ktpName = $_FILES['ktp']['name'];
            $ktpTempName = $_FILES['ktp']['tmp_name'];
            $ktpExt = explode('.', $ktpName);
            $ktpActualExt = strtolower(end($ktpExt));
            $ktpNew = Uuid::uuid4()->getHex() . "." . $ktpActualExt;
            $ktpDestination = $this->storage . '/' . $ktpNew;
            if (!move_uploaded_file($ktpTempName, $ktpDestination)) :
                throw new \Exception($this->outputMessage('directory'));
            endif;
            $request['ktp'] = $ktpNew;
        endif;

        /**
         * foto objek pajak
         */
        if (isset($_FILES['foto_op'])) :

            /**
             * remove file
             */
            if ($method == 'update') :
                if (!is_null($fotoOp) && file_exists($this->storage . "/" . $fotoOp)) :
                    unlink($this->storage . "/" . $fotoOp);
                endif;
            endif;

            /**
             * upload
             */
            $fotoOpName = $_FILES['foto_op']['name'];
            $fotoOpTempName = $_FILES['foto_op']['tmp_name'];
            $fotoOpExt = explode('.', $fotoOpName);
            $fotoOpActualExt = strtolower(end($fotoOpExt));
            $fotoOpNew = Uuid::uuid4()->getHex() . "." . $fotoOpActualExt;
            $fotoOpDestination = $this->storage . '/' . $fotoOpNew;
            if (!move_uploaded_file($fotoOpTempName, $fotoOpDestination)) :
                throw new \Exception($this->outputMessage('directory'));
            endif;
            $request['foto_op'] = $fotoOpNew;
        endif;

        /**
         * sertifikat tanah
         */
        if (isset($_FILES['sertifikat_tanah'])) :

            /**
             * remove file
             */
            if ($method == 'update') :
                if (!is_null($sertifikatTanah) && file_exists($this->storage . "/" . $sertifikatTanah)) :
                    unlink($this->storage . "/" . $sertifikatTanah);
                endif;
            endif;

            /**
             * upload
             */
            $sertifikatTanahName = $_FILES['sertifikat_tanah']['name'];
            $sertifikatTanahTempName = $_FILES['sertifikat_tanah']['tmp_name'];
            $sertifikatTanahExt = explode('.', $sertifikatTanahName);
            $sertifikatTanahActualExt = strtolower(end($sertifikatTanahExt));
            $sertifikatTanahNew = Uuid::uuid4()->getHex() . "." . $sertifikatTanahActualExt;
            $sertifikatTanahDestination = $this->storage . '/' . $sertifikatTanahNew;
            if (!move_uploaded_file($sertifikatTanahTempName, $sertifikatTanahDestination)) :
                throw new \Exception($this->outputMessage('directory'));
            endif;
            $request['sertifikat_tanah'] = $sertifikatTanahNew;
        endif;

        /**
         * fc sppt tahun berjalan
         */
        if (isset($_FILES['fc_sppt_thn_berjalan'])) :

            /**
             * remove file
             */
            if ($method == 'update') :
                if (!is_null($fcSpptThnBerjalan) && file_exists($this->storage . "/" . $fcSpptThnBerjalan)) :
                    unlink($this->storage . "/" . $fcSpptThnBerjalan);
                endif;
            endif;

            /**
             * upload
             */
            $fcSpptThnBerjalanName = $_FILES['fc_sppt_thn_berjalan']['name'];
            $fcSpptThnBerjalanTempName = $_FILES['fc_sppt_thn_berjalan']['tmp_name'];
            $fcSpptThnBerjalanExt = explode('.', $fcSpptThnBerjalanName);
            $fcSpptThnBerjalanActualExt = strtolower(end($fcSpptThnBerjalanExt));
            $fcSpptThnBerjalanNew = Uuid::uuid4()->getHex() . "." . $fcSpptThnBerjalanActualExt;
            $fcSpptThnBerjalanDestination = $this->storage . '/' . $fcSpptThnBerjalanNew;
            if (!move_uploaded_file($fcSpptThnBerjalanTempName, $fcSpptThnBerjalanDestination)) :
                throw new \Exception($this->outputMessage('directory'));
            endif;
            $request['fc_sppt_thn_berjalan'] = $fcSpptThnBerjalanNew;
        endif;

        /**
         * fc surat keterangna jual beli
         */
        if (isset($_FILES['fc_sk_jual_beli'])) :

            /**
             * remove file
             */
            if ($method == 'update') :
                if (!is_null($fcSkJualBeli) && file_exists($this->storage . "/" . $fcSkJualBeli)) :
                    unlink($this->storage . "/" . $fcSkJualBeli);
                endif;
            endif;

            /**
             * upload
             */
            $fcSkJualBeliName = $_FILES['fc_sk_jual_beli']['name'];
            $fcSkJualBeliTempName = $_FILES['fc_sk_jual_beli']['tmp_name'];
            $fcSkJualBeliExt = explode('.', $fcSkJualBeliName);
            $fcSkJualBeliActualExt = strtolower(end($fcSkJualBeliExt));
            $fcSkJualBeliNew = Uuid::uuid4()->getHex() . "." . $fcSkJualBeliActualExt;
            $fcSkJualBeliDestination = $this->storage . '/' . $fcSkJualBeliNew;
            if (!move_uploaded_file($fcSkJualBeliTempName, $fcSkJualBeliDestination)) :
                throw new \Exception($this->outputMessage('directory'));
            endif;
            $request['fc_sk_jual_beli'] = $fcSkJualBeliNew;
        endif;

        /**
         * perjanjian kredit
         */
        if (isset($_FILES['perjanjian_kredit'])) :

            /**
             * remove file
             */
            if ($method == 'update') :
                if (!is_null($perjalanKredit) && file_exists($this->storage . "/" . $perjalanKredit)) :
                    unlink($this->storage . "/" . $perjalanKredit);
                endif;
            endif;

            /**
             * upload
             */
            $perjanjianKreditName = $_FILES['perjanjian_kredit']['name'];
            $perjanjianKreditTempName = $_FILES['perjanjian_kredit']['tmp_name'];
            $perjanjianKreditExt = explode('.', $perjanjianKreditName);
            $perjanjianKreditActualExt = strtolower(end($perjanjianKreditExt));
            $perjanjianKreditNew = Uuid::uuid4()->getHex() . "." . $perjanjianKreditActualExt;
            $perjanjianKreditDestination = $this->storage . '/' . $perjanjianKreditNew;
            if (!move_uploaded_file($perjanjianKreditTempName, $perjanjianKreditDestination)) :
                throw new \Exception($this->outputMessage('directory'));
            endif;
            $request['perjanjian_kredit'] = $perjanjianKreditNew;
        endif;

        /**
         * surat pernyataan
         */
        if (isset($_FILES['surat_pernyataan'])) :

            /**
             * remove file
             */
            if ($method == 'update') :
                if (!is_null($suratPernyataan) && file_exists($this->storage . "/" . $suratPernyataan)) :
                    unlink($this->storage . "/" . $suratPernyataan);
                endif;
            endif;

            /**
             * upload
             */
            $suratPernyataanName = $_FILES['surat_pernyataan']['name'];
            $suratPernyataanTempName = $_FILES['surat_pernyataan']['tmp_name'];
            $suratPernyataanExt = explode('.', $suratPernyataanName);
            $suratPernyataanActualExt = strtolower(end($suratPernyataanExt));
            $suratPernyataanNew = Uuid::uuid4()->getHex() . "." . $suratPernyataanActualExt;
            $suratPernyataanDestination = $this->storage . '/' . $suratPernyataanNew;
            if (!move_uploaded_file($suratPernyataanTempName, $suratPernyataanDestination)) :
                throw new \Exception($this->outputMessage('directory'));
            endif;
            $request['surat_pernyataan'] = $suratPernyataanNew;
        endif;

        /**
         * fc surat kematian
         */
        if (isset($_FILES['fc_surat_kematian'])) :

            /**
             * remove file
             */
            if ($method == 'update') :
                if (!is_null($fcSuratKematian) && file_exists($this->storage . "/" . $fcSuratKematian)) :
                    unlink($this->storage . "/" . $fcSuratKematian);
                endif;
            endif;

            /**
             * upload
             */
            $fcSuratKematianName = $_FILES['fc_surat_kematian']['name'];
            $fcSuratKematianTempName = $_FILES['fc_surat_kematian']['tmp_name'];
            $fcSuratKematianExt = explode('.', $fcSuratKematianName);
            $fcSuratKematianActualExt = strtolower(end($fcSuratKematianExt));
            $fcSuratKematianNew = Uuid::uuid4()->getHex() . "." . $fcSuratKematianActualExt;
            $fcSuratKematianDestination = $this->storage . '/' . $fcSuratKematianNew;
            if (!move_uploaded_file($fcSuratKematianTempName, $fcSuratKematianDestination)) :
                throw new \Exception($this->outputMessage('directory'));
            endif;
            $request['fc_surat_kematian'] = $fcSuratKematianNew;
        endif;

        /**
         * fc surat keterangan ahli waris
         */
        if (isset($_FILES['fc_sk_ahli_waris'])) :

            /**
             * remove file
             */
            if ($method == 'update') :
                if (!is_null($fcSkAhliWaris) && file_exists($this->storage . "/" . $fcSkAhliWaris)) :
                    unlink($this->storage . "/" . $fcSkAhliWaris);
                endif;
            endif;

            /**
             * upload
             */
            $fcSkAhliWarisName = $_FILES['fc_sk_ahli_waris']['name'];
            $fcSkAhliWarisTempName = $_FILES['fc_sk_ahli_waris']['tmp_name'];
            $fcSkAhliWarisExt = explode('.', $fcSkAhliWarisName);
            $fcSkAhliWarisActualExt = strtolower(end($fcSkAhliWarisExt));
            $fcSkAhliWarisNew = Uuid::uuid4()->getHex() . "." . $fcSkAhliWarisActualExt;
            $fcSkAhliWarisDestination = $this->storage . '/' . $fcSkAhliWarisNew;
            if (!move_uploaded_file($fcSkAhliWarisTempName, $fcSkAhliWarisDestination)) :
                throw new \Exception($this->outputMessage('directory'));
            endif;
            $request['fc_sk_ahli_waris'] = $fcSkAhliWarisNew;
        endif;

        /**
         * sp ganti rugi
         */
        if (isset($_FILES['sp_ganti_rugi'])) :

            /**
             * remove file
             */
            if ($method == 'update') :
                if (!is_null($spGantiRugi) && file_exists($this->storage . "/" . $spGantiRugi)) :
                    unlink($this->storage . "/" . $spGantiRugi);
                endif;
            endif;

            /**
             * upload
             */
            $spGantiRugiName = $_FILES['sp_ganti_rugi']['name'];
            $spGantiRugiTempName = $_FILES['sp_ganti_rugi']['tmp_name'];
            $spGantiRugiExt = explode('.', $spGantiRugiName);
            $spGantiRugiActualExt = strtolower(end($spGantiRugiExt));
            $spGantiRugiNew = Uuid::uuid4()->getHex() . "." . $spGantiRugiActualExt;
            $spGantiRugiDestination = $this->storage . '/' . $spGantiRugiNew;
            if (!move_uploaded_file($spGantiRugiTempName, $spGantiRugiDestination)) :
                throw new \Exception($this->outputMessage('directory'));
            endif;
            $request['sp_ganti_rugi'] = $spGantiRugiNew;
        endif;

        /**
         * sk bpn
         */
        if (isset($_FILES['sk_bpn'])) :

            /**
             * remove file
             */
            if ($method == 'update') :
                if (!is_null($skBpn) && file_exists($this->storage . "/" . $skBpn)) :
                    unlink($this->storage . "/" . $skBpn);
                endif;
            endif;

            /**
             * upload
             */
            $skBpnName = $_FILES['sk_bpn']['name'];
            $skBpnTempName = $_FILES['sk_bpn']['tmp_name'];
            $skBpnExt = explode('.', $skBpnName);
            $skBpnActualExt = strtolower(end($skBpnExt));
            $skBpnNew = Uuid::uuid4()->getHex() . "." . $skBpnActualExt;
            $skBpnDestination = $this->storage . '/' . $skBpnNew;
            if (!move_uploaded_file($skBpnTempName, $skBpnDestination)) :
                throw new \Exception($this->outputMessage('directory'));
            endif;
            $request['sk_bpn'] = $skBpnNew;
        endif;

        /**
         * fc sk hibah desa
         */
        if (isset($_FILES['fc_sk_hibah_desa'])) :

            /**
             * remove file
             */
            if ($method == 'update') :
                if (!is_null($fcSkHibahDesa) && file_exists($this->storage . "/" . $fcSkHibahDesa)) :
                    unlink($this->storage . "/" . $fcSkHibahDesa);
                endif;
            endif;

            /**
             * upload
             */
            $fcSkHibahDesaName = $_FILES['fc_sk_hibah_desa']['name'];
            $fcSkHibahDesaTempName = $_FILES['fc_sk_hibah_desa']['tmp_name'];
            $fcSkHibahDesaExt = explode('.', $fcSkHibahDesaName);
            $fcSkHibahDesaActualExt = strtolower(end($fcSkHibahDesaExt));
            $fcSkHibahDesaNew = Uuid::uuid4()->getHex() . "." . $fcSkHibahDesaActualExt;
            $fcSkHibahDesaDestination = $this->storage . '/' . $fcSkHibahDesaNew;
            if (!move_uploaded_file($fcSkHibahDesaTempName, $fcSkHibahDesaDestination)) :
                throw new \Exception($this->outputMessage('directory'));
            endif;
            $request['fc_sk_hibah_desa'] = $fcSkHibahDesaNew;
        endif;

        /**
         * risalah lelanng
         */
        if (isset($_FILES['risalah_lelang'])) :

            /**
             * remove file
             */
            if ($method == 'update') :
                if (!is_null($risalahLelang) && file_exists($this->storage . "/" . $risalahLelang)) :
                    unlink($this->storage . "/" . $risalahLelang);
                endif;
            endif;

            /**
             * upload
             */
            $risalahLelangName = $_FILES['risalah_lelang']['name'];
            $risalahLelangTempName = $_FILES['risalah_lelang']['tmp_name'];
            $risalahLelangExt = explode('.', $risalahLelangName);
            $risalahLelangActualExt = strtolower(end($risalahLelangExt));
            $risalahLelangNew = Uuid::uuid4()->getHex() . "." . $risalahLelangActualExt;
            $risalahLelangDestination = $this->storage . '/' . $risalahLelangNew;
            if (!move_uploaded_file($risalahLelangTempName, $risalahLelangDestination)) :
                throw new \Exception($this->outputMessage('directory'));
            endif;
            $request['risalah_lelang'] = $risalahLelangNew;
        endif;
        return $request;
    }
}
