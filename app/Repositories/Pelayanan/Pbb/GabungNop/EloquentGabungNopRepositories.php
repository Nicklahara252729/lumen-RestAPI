<?php

namespace App\Repositories\Pelayanan\Pbb\GabungNop;

/**
 * import component
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

/**
 * import traits
 */

use App\Traits\Message;
use App\Traits\Response;
use App\Traits\Notification;
use App\Traits\Generator;
use App\Traits\Calculation;

/**
 * import models
 */

use App\Models\Pelayanan\Pelayanan\Pelayanan;
use App\Models\Sppt\Sppt;
use App\Models\DatObjekPajak\DatObjekPajak;
use App\Models\DatSubjekPajak\DatSubjekPajak;
use App\Models\PembayaranSppt\PembayaranSppt\PembayaranSppt;
use App\Models\DatOpBumi\DatOpBumi;
use App\Models\DatOpBangunan\DatOpBangunan;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;

/**
 * import interface
 */

use App\Repositories\Pelayanan\Pbb\GabungNop\GabungNopRepositories;

class EloquentGabungNopRepositories implements GabungNopRepositories
{
    use Message, Response, Notification, Generator, Calculation;

    private $pelayanan;
    private $checkerHelpers;
    private $paginateHelpers;
    private $provinsi;
    private $kabupaten;
    private $sppt;
    private $datObjekPajak;
    private $datSubjekPajak;
    private $pembayaranSppt;
    private $datetime;
    private $datOpBumi;
    private $year;
    private $nip;
    private $datOpBangunan;

    public function __construct(
        Pelayanan $pelayanan,
        Sppt $sppt,
        DatObjekPajak $datObjekPajak,
        PembayaranSppt $pembayaranSppt,
        DatSubjekPajak $datSubjekPajak,
        CheckerHelpers $checkerHelpers,
        DatOpBangunan $datOpBangunan,
        DatOpBumi $datOpBumi
    ) {
        /**
         * initialize model
         */
        $this->pelayanan = $pelayanan;
        $this->sppt = $sppt;
        $this->datObjekPajak = $datObjekPajak;
        $this->datSubjekPajak = $datSubjekPajak;
        $this->pembayaranSppt = $pembayaranSppt;
        $this->datOpBumi = $datOpBumi;
        $this->datOpBangunan = $datOpBangunan;

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;

        /**
         * static value
         */
        $this->provinsi  = [globalAttribute()['kdProvinsi'], 'SUMATERA UTARA'];
        $this->kabupaten = [globalAttribute()['kdKota'], 'BINJAI'];
        $this->datetime = Carbon::now()->toDateTimeLocalString();
        $this->year = Carbon::now()->format('Y');
        $this->nip = authAttribute()['nip'];
    }

    /**
     * store gabung NOP
     */
    public function store($request)
    {
        DB::beginTransaction();
        try {

            /**
             * query
             */
            $select = "LUAS_BUMI_SPPT, LUAS_BNG_SPPT, NJOP_BNG_SPPT, NJOPTKP_SPPT, FAKTOR_PENGURANG_SPPT, PBB_TERHUTANG_SPPT, PBB_YG_HARUS_DIBAYAR_SPPT, NJOP_BUMI_SPPT, NJOP_SPPT, THN_PAJAK_SPPT";
            $where = "CONCAT(KD_PROPINSI,KD_DATI2,KD_KECAMATAN,KD_KELURAHAN,KD_BLOK,NO_URUT,KD_JNS_OP)";

            /**
             * getting single data
             */
            $getSpptAwal = $this->sppt->selectRaw($select)->whereRaw($where . ' = "' . $request['nop_awal'] . '"')->orderByDesc('THN_PAJAK_SPPT')->first();
            $getSpptGabung = $this->sppt->selectRaw($select)->whereRaw($where . ' = "' . $request['nop_gabung'] . '"')->orderByDesc('THN_PAJAK_SPPT')->first();
            $getOpAwal = $this->datObjekPajak->select('TOTAL_LUAS_BUMI', 'TOTAL_LUAS_BNG', 'NJOP_BUMI', 'NJOP_BNG')->whereRaw($where . ' = "' . $request['nop_awal'] . '"')->first();
            $getOpGabung = $this->datObjekPajak->select('TOTAL_LUAS_BUMI', 'TOTAL_LUAS_BNG', 'NJOP_BUMI', 'NJOP_BNG')->whereRaw($where . ' = "' . $request['nop_gabung'] . '"')->first();
            $getOpBumiAwal = $this->datOpBumi->select('LUAS_BUMI')->whereRaw($where . ' = "' . $request['nop_awal'] . '"')->first();
            $getOpBumiGabung = $this->datOpBumi->select('LUAS_BUMI')->whereRaw($where . ' = "' . $request['nop_gabung'] . '"')->first();
            $getOpBngAwal = $this->datOpBangunan->select('LUAS_BNG')->whereRaw($where . ' = "' . $request['nop_awal'] . '"')->first();
            $getOpBngGabung = $this->datOpBangunan->select('LUAS_BNG')->whereRaw($where . ' = "' . $request['nop_gabung'] . '"')->first();

            /**
             * check piutang nop awal
             */
            $checkPiutang = $this->sppt->whereRaw($where . ' = "' . $request['nop_awal'] . '"');
            if (is_null($checkPiutang)) :
                throw new \Exception($this->outputMessage('credit'));
            endif;

            /**
             * perhitungan
             */
            $luasTanah = $getSpptGabung->LUAS_BUMI_SPPT + $getSpptAwal->LUAS_BUMI_SPPT;
            $luasBangunan = $getSpptGabung->LUAS_BNG_SPPT + $getSpptAwal->LUAS_BNG_SPPT;
            $njopBangunan = $getSpptGabung->NJOP_BNG_SPPT + $getSpptAwal->NJOP_BNG_SPPT;
            $njoptkp = $getSpptGabung->NJOPTKP_SPPT + $getSpptAwal->NJOPTKP_SPPT;
            $faktorPengurang = $getSpptGabung->FAKTOR_PENGURANG_SPPT + $getSpptAwal->FAKTOR_PENGURANG_SPPT;
            $pbbTerhutang = $getSpptGabung->PBB_TERHUTANG_SPPT + $getSpptAwal->PBB_TERHUTANG_SPPT;
            $pbbHarusDibayar = $getSpptGabung->PBB_YG_HARUS_DIBAYAR_SPPT + $getSpptAwal->PBB_YG_HARUS_DIBAYAR_SPPT;
            $njopBumi = $getSpptGabung->NJOP_BUMI_SPPT + $getSpptAwal->NJOP_BUMI_SPPT;
            $njopSppt = $getSpptGabung->NJOP_SPPT + $getSpptAwal->NJOP_SPPT;

            /**
             * input for pelayanan
             */
            $pelayananInput['nomor_pelayanan']      = $request['nomor_pelayanan'];
            $pelayananInput['uuid_layanan']         = $request['uuid_layanan'];
            $pelayananInput['uuid_jenis_pelayanan'] = $request['uuid_jenis_pelayanan'];
            $pelayananInput['created_by']           = authAttribute()['id'];

            /**
             * input for sppt tujuan
             */
            $spptTujuanAwal['LUAS_BUMI_SPPT'] = $luasTanah;
            $spptTujuanAwal['LUAS_BNG_SPPT'] = $luasBangunan;
            $spptTujuanAwal['NJOP_BUMI_SPPT'] = $njopBumi;
            $spptTujuanAwal['NJOP_BNG_SPPT'] = $njopBangunan;
            $spptTujuanAwal['NJOP_SPPT'] = $njopSppt;
            $spptTujuanAwal['NJOPTKP_SPPT'] = $njoptkp;
            $spptTujuanAwal['FAKTOR_PENGURANG_SPPT'] = $faktorPengurang;
            $spptTujuanAwal['PBB_TERHUTANG_SPPT'] = $pbbTerhutang;
            $spptTujuanAwal['PBB_YG_HARUS_DIBAYAR_SPPT'] = $pbbHarusDibayar;

            /**
             * save data pelayanan
             */
            $savePelayanan = $this->pelayanan->create($pelayananInput);
            if (!$savePelayanan) :
                throw new \Exception($this->outputMessage('unsaved', 'pelayanan'));
            endif;

            /**
             * update sppt awal
             */
            $updateSpptAwal = $this->sppt->whereRaw($where . ' = "' . $request['nop_awal'] . '"')->where('THN_PAJAK_SPPT', $getSpptAwal->THN_PAJAK_SPPT)->update($spptTujuanAwal);
            if (!$updateSpptAwal) :
                throw new \Exception($this->outputMessage('update fail', 'sppt awal'));
            endif;

            /**
             * update data objek bumi gabung
             */
            $luasBumi = $getOpBumiAwal->LUAS_BUMI + $getOpBumiGabung->LUAS_BUMI;
            $updateDatOpBumi = $this->datOpBumi->whereRaw($where . ' = "' . $request['nop_gabung'] . '"')->update(['LUAS_BUMI' => $luasBumi, 'JNS_BUMI' => 4]);
            if (!$updateDatOpBumi) :
                throw new \Exception($this->outputMessage('update fail', 'data op bumi'));
            endif;

            /**
             * update data objek pajak
             */
            $totalLuasBumiOp = $getOpAwal->TOTAL_LUAS_BUMI + $getOpGabung->TOTAL_LUAS_BUMI;
            $totalLuasBngOp = $getOpAwal->TOTAL_LUAS_BNG + $getOpGabung->TOTAL_LUAS_BNG;
            $njopBumiOp = $getOpAwal->NJOP_BUMI + $getOpGabung->NJOP_BUMI;
            $njopBngOp = $getOpAwal->NJOP_BNG + $getOpGabung->NJOP_BNG;
            $inputOp = [
                'TOTAL_LUAS_BUMI' => $totalLuasBumiOp,
                'TOTAL_LUAS_BNG' => $totalLuasBngOp,
                'NJOP_BUMI' => $njopBumiOp,
                'NJOP_BNG' => $njopBngOp,
                'TGL_PENDATAAN_OP' => $this->datetime,
                'NIP_PENDATA' => $this->nip,
                'TGL_PEMERIKSAAN_OP' => $this->datetime,
                'NIP_PEMERIKSA_OP' => $this->nip,
                'TGL_PEREKAMAN_OP' => $this->datetime,
                'NIP_PEREKAM_OP' => $this->nip,

            ];
            $updateDataOp = $this->datObjekPajak->whereRaw($where . ' = "' . $request['nop_awal'] . '"')->update($inputOp);
            if (!$updateDataOp) :
                throw new \Exception($this->outputMessage('update fail', 'objek pajak'));
            endif;

            /**
             * update op bangunan
             */
            if (!is_null($getOpBngAwal) && !is_null($getOpBngGabung)) :
                $luasBngOp = $getOpBngAwal->LUAS_BNG + $getOpBngGabung->LUAS_BNG;
                $inputOpBangunan = [
                    'LUAS_BNG' => $luasBngOp,
                    'TGL_PENDATAAN_BNG' => $this->datetime,
                    'NIP_PENDATA_BNG' => $this->nip,
                    'TGL_PEMERIKSAAN_BNG' => $this->datetime,
                    'NIP_PEMERIKSA_BNG' => $this->nip,
                    'TGL_PEREKAMAN_BNG' => $this->datetime,
                    'NIP_PEREKAM_BNG' => $this->nip,
                ];
                $updateOpBng = $this->datOpBangunan->whereRaw($where . ' = "' . $request['nop_awal'] . '"')->update($inputOpBangunan);
                if (!$updateOpBng) :
                    throw new \Exception($this->outputMessage('update fail', 'OP Bangunan'));
                endif;
            endif;

            /**
             * check current sppt gabung
             */
            $querySpptGabungNop = $this->sppt->whereRaw($where . ' = "' . $request['nop_gabung'] . '"');
            $checkSpptGabungNop = $querySpptGabungNop->where('THN_PAJAK_SPPT', $this->year)->first();
            if (is_null($checkSpptGabungNop)) :
                throw new \Exception($this->outputMessage('not found', 'SPPT gabung yang sedang berjalan'));
            endif;

            $deleteSppt = $querySpptGabungNop->delete();
            if (!$deleteSppt) :
                throw new \Exception($this->outputMessage('delete', 'SPPT gabung yang sedang berjalan'));
            endif;

            /**
             * whatsapp notification
             */
            // $getSetting = $this->checkerHelpers->settingChecker('whatsapp notif');
            // if (is_null($getSetting)) :
            //     throw new \Exception($this->outputMessage('not found', 'whatsapp notification setting'));
            // endif;

            // if ($getSetting->description == 'enabled') :

            //     /**
            //      * get data kasubbid
            //      */
            //     $getKasubbid = $this->checkerHelpers->userChecker(['role' => 'kasubbid']);
            //     if (is_null($getKasubbid)) :
            //         throw new \Exception($this->outputMessage('not found', 'Kasubbid'));
            //     endif;

            //     /**
            //      * get tanggal pendaftaran
            //      */
            //     $getCurrentPelayanan = $this->checkerHelpers->pelayananChecker(['nomor_pelayanan' => $request['nomor_pelayanan']]);
            //     $tanggalPendaftaran = Carbon::parse($getCurrentPelayanan->created_at)->locale('id');
            //     $tanggalPendaftaran->settings(['formatFunction' => 'translatedFormat']);

            //     /**
            //      * send notification
            //      */
            //     $message = "Pembetulan SPPT/SKP/STP";
            //     $message .= "\nAtas nama " . $request['nama_lengkap'];
            //     $message .= "\nNo Pelayanan " . $request['nomor_pelayanan'];
            //     $message .= "\n" . $tanggalPendaftaran->format('l, j F Y ; h:i:s ') . "\n\n";
            //     $message .= "\nMohon segera diproses";
            //     $message .= "\n(OPERATOR)";
            //     $message .= "\n _BPKPAD KOTA BINJAI_";
            //     $callBack = $this->whatsapp($getKasubbid->no_hp, $message);

            //     /**
            //      * fail send whatsapp notification
            //      */
            //     if ($callBack->status != true) :
            //         throw new \Exception($this->outputMessage('unsend', 'whatsapp'));
            //     endif;
            // endif;
            DB::commit();
            $response  = $this->success($this->outputMessage('mergered', 'NOP ' . $request['nop_awal'] . ' dan NOP ' . $request['nop_gabung']));
        } catch (\Exception $e) {
            DB::rollback();
            $response  = $this->error($e->getMessage());
        }

        /**
         * send response to controller
         */
        return $response;
    }

    /**
     * autocomplete gabung nop
     */
    public function autocomplete($nop, $tahun)
    {
        try {

            /**
             * data sppt
             */
            $data = $this->sppt->select('NM_WP_SPPT', 'LUAS_BUMI_SPPT', 'LUAS_BNG_SPPT')
                ->selectRaw("(SELECT JALAN_OP FROM dat_objek_pajak WHERE concat(dat_objek_pajak.KD_PROPINSI,dat_objek_pajak.KD_DATI2,dat_objek_pajak.KD_KECAMATAN,dat_objek_pajak.KD_KELURAHAN,dat_objek_pajak.KD_BLOK,dat_objek_pajak.NO_URUT,dat_objek_pajak.KD_JNS_OP) 
                = concat(sppt.KD_PROPINSI,sppt.KD_DATI2,sppt.KD_KECAMATAN,sppt.KD_KELURAHAN,sppt.KD_BLOK,sppt.NO_URUT,sppt.KD_JNS_OP)) AS JALAN_OP")
                ->whereRaw('concat(KD_PROPINSI,KD_DATI2,KD_KECAMATAN,KD_KELURAHAN,KD_BLOK,NO_URUT,KD_JNS_OP) = "' . $nop . '"')
                ->where('THN_PAJAK_SPPT', $tahun)
                ->first();
            if (is_null($data)) :
                throw new \Exception($this->outputMessage('not found', 'SPPT'));
            endif;

            /**
             * set response
             */
            $response  = $this->successData($this->outputMessage('data', 1), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}
