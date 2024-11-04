<?php

namespace App\Repositories\Tunggakan;

/**
 * default component
 */

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Carbon;

/**
 * import traits
 */

use App\Traits\Message;
use App\Traits\Response;
use App\Traits\Calculation;
use App\Traits\Generator;
use App\Traits\Briva;

/**
 * import models
 */

use App\Models\Tunggakan\Tunggakan;
use App\Models\Sppt\Sppt;
use App\Models\DatObjekPajak\DatObjekPajak;
use App\Models\DatSubjekPajak\DatSubjekPajak;
use App\Models\Pelayanan\Pelayanan\Pelayanan;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;
use App\Libraries\PaginateHelpers;

/**
 * import repositories
 */

use App\Repositories\Tunggakan\TunggakanRepositories;

class EloquentTunggakanRepositories implements TunggakanRepositories
{
    use Message, Response, Calculation, Generator, Briva;

    private $tunggakan;
    private $checkerHelpers;
    private $storage;
    private $paginateHelpers;
    private $sppt;
    private $datObjekPajak;
    private $datSubjekPajak;
    private $datetime;
    private $provinsi;
    private $kabupaten;
    private $pelayanan;
    private $year;

    public function __construct(
        Tunggakan $tunggakan,
        Sppt $sppt,
        DatObjekPajak $datObjekPajak,
        DatSubjekPajak $datSubjekPajak,
        Pelayanan $pelayanan,
        CheckerHelpers $checkerHelpers,
        PaginateHelpers $paginateHelpers
    ) {
        /**
         * initialize model
         */
        $this->tunggakan = $tunggakan;
        $this->sppt = $sppt;

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;
        $this->paginateHelpers = $paginateHelpers;
        $this->datObjekPajak = $datObjekPajak;
        $this->datSubjekPajak = $datSubjekPajak;
        $this->pelayanan = $pelayanan;

        /**
         * static value
         */
        $this->storage = path('tunggakan');
        $this->datetime = Carbon::now()->toDateTimeLocalString();
        $this->provinsi  = [globalAttribute()['kdProvinsi'], 'SUMATERA UTARA'];
        $this->kabupaten = [globalAttribute()['kdKota'], 'BINJAI'];
        $this->year      = Carbon::now()->format('Y');
    }

    /**
     * all record
     */
    public function data($kdKecamatan, $kdKelurahan)
    {
        try {
            /**
             * data tunggakan
             */
            $data = $this->tunggakan->select([
                DB::raw('SUBSTRING(nop, 11, 3) AS kd_blok'),
                DB::raw('COUNT(*) AS jumlah_data'),
            ])
                ->whereRaw('MID(nop, 5, 3) = ?', [$kdKecamatan])
                ->whereRaw('MID(nop, 8, 3) = ?', [$kdKelurahan])
                ->where('umur_pajak', '>=', "5")
                ->whereNull('status_cetak_sppt')
                ->groupBy(DB::raw('SUBSTRING(nop, 11, 3)'))
                ->paginate(10);

            /**
             * set response
             */
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * get data by kd blok
     */
    public function dataNopByKdBlok($kdBlok, $kdKecamatan, $kdKelurahan)
    {
        try {
            /**
             * data tunggakan
             */
            $data = $this->tunggakan->select(
                'nop',
                'nama_kecamatan',
                'nama_kelurahan',
                'nama_wp',
                'JALAN_OP AS alamat_sppt',
                'thn_sppt',
                'tunggakan.pbb_yg_harus_dibayar_sppt'
            )
                ->join('dat_objek_pajak', function ($join) {
                    $join->on(DB::raw('CONCAT(dat_objek_pajak.KD_PROPINSI, dat_objek_pajak.KD_DATI2, dat_objek_pajak.KD_KECAMATAN, dat_objek_pajak.KD_KELURAHAN, dat_objek_pajak.KD_BLOK, dat_objek_pajak.NO_URUT, dat_objek_pajak.KD_JNS_OP)'), '=', DB::raw('tunggakan.nop'));
                })
                ->whereRaw('MID(nop, 5, 3) = ?', [$kdKecamatan])
                ->whereRaw('MID(nop, 8, 3) = ?', [$kdKelurahan])
                ->whereRaw('MID(nop, 11, 3) = ?', [$kdBlok])
                ->where('umur_pajak', '>=', "5")
                ->whereNull('status_cetak_sppt')
                ->groupBy(DB::raw('nama_wp,nop,nama_kecamatan,nama_kelurahan,nama_wp,thn_sppt,JALAN_OP,pbb_yg_harus_dibayar_sppt'))
                ->paginate(10);

            /**
             * set response
             */
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * update data tunggakan
     */
    public function update($request, $param)
    {
        DB::beginTransaction();
        try {

            $role = authAttribute()['role'];
            if ($role == 'kasubbid') :
                /**
                 * check data pelayanan
                 */
                $getPelayanan   = $this->checkerHelpers->pelayananChecker(["uuid_pelayanan" => $param]);
                if (is_null($getPelayanan)) :
                    throw new \Exception($this->outputMessage('not found', 'pelayanan'));
                endif;
            else :
                /**
                 * check data nop
                 */
                $checkSppt = $this->sppt->whereRaw('CONCAT(KD_PROPINSI, KD_DATI2, KD_KECAMATAN, KD_KELURAHAN, KD_BLOK, NO_URUT, KD_JNS_OP) = ' . $param)
                    ->orderBy('THN_PAJAK_SPPT', 'DESC')
                    ->first();
                if (is_null($checkSppt)) :
                    throw new \Exception($this->outputMessage('not found', 'sppt'));
                endif;
            endif;

            /**
             * check tunggakan
             */
            if (isset($_FILES['photo'])) :
                $photoName = $_FILES['photo']['name'];
                $photoTempName = $_FILES['photo']['tmp_name'];
                $photoExt = explode('.', $photoName);
                $photoActualExt = strtolower(end($photoExt));
                $photoNew = Uuid::uuid4()->getHex() . "." . $photoActualExt;
                $photoDestination = $this->storage . '/' . $photoNew;
                if (!move_uploaded_file($photoTempName, $photoDestination)) :
                    throw new \Exception($this->outputMessage('directory'));
                endif;
                $request['photo'] = $photoNew;
            endif;

            /**
             * add more request
             */
            $request['updated_by'] = authAttribute()['id'];
            $request['updated_at'] = $this->datetime;
            $request['STATUS_CETAK_SPPT'] = "9";

            if ($role == 'kasubbid') :

                if ($getPelayanan->status_verifikasi != 4) :
                    /**
                     * get keluarahan
                     */
                    $getKelurahan   = $this->checkerHelpers->kelurahanChecker(["id_kelurahan" => $getPelayanan->id_kelurahan]);
                    if (is_null($getKelurahan)) :
                        throw new \Exception($this->outputMessage('not found', 'kelurahan'));
                    endif;

                    /**
                     * get kota
                     */
                    $getKota        = $this->checkerHelpers->kabupatenChecker(["id_kabupaten" => $getPelayanan->id_kabupaten]);
                    if (is_null($getKota)) :
                        throw new \Exception($this->outputMessage('not found', 'kota'));
                    endif;

                    $opKdKecamatan  = $getPelayanan->op_kd_kecamatan;
                    $opKdKelurahan  = $getPelayanan->op_kd_kelurahan;
                    $opKdBlok       = $getPelayanan->op_kd_blok;
                    $statusKolektif = $getPelayanan->status_kolektif;
                    $spopKelurahan  = $getKelurahan->nama_kelurahan;
                    $spopKota       = $getKota->nama_kabupaten;
                    $kodeBayar      = $this->kodeBriva($opKdKecamatan, $opKdKelurahan, $opKdBlok);
                    $noUrut         = $getKelurahan->no_urut;

                    /**
                     * perhitungan
                     */
                    $getKelasBumi = $this->checkerHelpers->kelasBumiChecker(['KD_KLS_TANAH' => $getPelayanan->op_kelas_bumi]);
                    $njopBumi = $this->njopBumi($getKelasBumi->NILAI_PER_M2_TANAH, $getPelayanan->op_luas_tanah);
                    $njopBangunan = 0;
                    $njopSppt = $this->njopSppt($njopBumi, $njopBangunan);
                    $njoptkp = $this->njoptkp($getPelayanan->op_luas_bangunan);
                    $pbbTerhutang = $this->pbbTerhutang($njopSppt, $njoptkp);
                    $faktorPengurang = $this->faktorPengurang();
                    $pbbHarusDibayar = $this->pbbHarusDibayar($pbbTerhutang, $faktorPengurang);

                    /**
                     * input for sppt
                     */
                    $spptInput['kode_bayar']                = $kodeBayar;
                    $spptInput['KD_PROPINSI']               = $this->provinsi[0];
                    $spptInput['KD_DATI2']                  = $this->kabupaten[0];
                    $spptInput['KD_KECAMATAN']              = $opKdKecamatan;
                    $spptInput['KD_KELURAHAN']              = $opKdKelurahan;
                    $spptInput['KD_BLOK']                   = $opKdBlok;
                    $spptInput['NO_URUT']                   = $noUrut;
                    $spptInput['KD_JNS_OP']                 = $statusKolektif;
                    $spptInput['THN_PAJAK_SPPT']            = $this->year;
                    $spptInput['NM_WP_SPPT']                = $getPelayanan->sp_nama_lengkap;
                    $spptInput['JLN_WP_SPPT']               = $getPelayanan->sp_alamat;
                    $spptInput['RW_WP_SPPT']                = $getPelayanan->sp_rw;
                    $spptInput['RT_WP_SPPT']                = $getPelayanan->sp_rt;
                    $spptInput['KELURAHAN_WP_SPPT']         = $spopKelurahan;
                    $spptInput['KOTA_WP_SPPT']              = $spopKota;
                    $spptInput['KD_KLS_TANAH']              = $getPelayanan->op_kelas_bumi;
                    $spptInput['LUAS_BUMI_SPPT']            = $getPelayanan->op_luas_tanah;
                    $spptInput['LUAS_BNG_SPPT']             = $getPelayanan->op_luas_bangunan;
                    $spptInput['NJOP_BUMI_SPPT']            = $njopBumi;
                    $spptInput['NJOP_SPPT']                 = $njopSppt;
                    $spptInput['PBB_TERHUTANG_SPPT']        = $pbbTerhutang;
                    $spptInput['PBB_YG_HARUS_DIBAYAR_SPPT'] = $pbbHarusDibayar;

                    /**
                     * input for subjek pajak
                     */
                    $subjekPajakInput['SUBJEK_PAJAK_ID']     = $getPelayanan->id_pemohon;
                    $subjekPajakInput['NM_WP']               = $getPelayanan->sp_nama_lengkap;
                    $subjekPajakInput['JALAN_WP']            = $getPelayanan->sp_alamat;
                    $subjekPajakInput['RW_WP']               = $getPelayanan->sp_rw;
                    $subjekPajakInput['RT_WP']               = $getPelayanan->sp_rt;
                    $subjekPajakInput['KELURAHAN_WP']        = $spopKelurahan;
                    $subjekPajakInput['KOTA_WP']             = $spopKota;
                    $subjekPajakInput['TELP_WP']             = $getPelayanan->sp_no_hp;
                    $subjekPajakInput['NPWP']                = $getPelayanan->sp_npwp;
                    $subjekPajakInput['STATUS_PEKERJAAN_WP'] = $getPelayanan->sp_kd_pekerjaan;

                    /**
                     * input for objek pajak
                     */
                    $objekPajakInput['KD_PROPINSI']        = $this->provinsi[0];
                    $objekPajakInput['KD_DATI2']           = $this->kabupaten[0];
                    $objekPajakInput['KD_KECAMATAN']       = $opKdKecamatan;
                    $objekPajakInput['KD_KELURAHAN']       = $opKdKelurahan;
                    $objekPajakInput['KD_BLOK']            = $opKdBlok;
                    $objekPajakInput['NO_URUT']            = $noUrut;
                    $objekPajakInput['KD_JNS_OP']          = $statusKolektif;
                    $objekPajakInput['SUBJEK_PAJAK_ID']    = $getPelayanan->id_pemohon;
                    $objekPajakInput['NO_FORMULIR_SPOP']   = $getPelayanan->nomor_pelayanan;
                    $objekPajakInput['JALAN_OP']           = $getPelayanan->op_alamat;
                    $objekPajakInput['TOTAL_LUAS_BUMI']    = $getPelayanan->op_luas_tanah;
                    $objekPajakInput['TOTAL_LUAS_BNG']     = $getPelayanan->op_luas_bangunan;
                    $objekPajakInput['NJOP_BUMI']          = $njopBumi;
                    $objekPajakInput['NJOP_BNG']           = 0;

                    /**
                     * save sppt
                     */
                    $saveSppt = $this->sppt->insert($spptInput);
                    if (!$saveSppt) :
                        throw new \Exception($this->outputMessage('unsaved', 'SPPT'));
                    endif;

                    /**
                     * save subjek pajak
                     */
                    $saveSubjekPajak = $this->datSubjekPajak->insert($subjekPajakInput);
                    if (!$saveSubjekPajak) :
                        throw new \Exception($this->outputMessage('unsaved', 'Subjek Pajak'));
                    endif;


                    /**
                     * save objek pajak
                     */
                    $saveObjekPajak = $this->datObjekPajak->insert($objekPajakInput);
                    if (!$saveObjekPajak) :
                        throw new \Exception($this->outputMessage('unsaved', 'Objek Pajak'));
                    endif;

                    /**
                     * update no urut sppt di pelayanan
                     */
                    $updatePelayanan = $this->pelayanan->where(["uuid_pelayanan" => $param])->update(['no_urut' => $noUrut]);
                    if (!$updatePelayanan) :
                        throw new \Exception($this->outputMessage('update fail', 'no urut dipelayanan'));
                    endif;
                endif;

            else :
                /**
                 * update data sppt
                 */
                $updateSppt = $this->sppt
                    ->whereRaw('CONCAT(KD_PROPINSI, KD_DATI2, KD_KECAMATAN, KD_KELURAHAN, KD_BLOK, NO_URUT, KD_JNS_OP) = "' . $param . '" AND THN_PAJAK_SPPT = ' . $checkSppt->THN_PAJAK_SPPT)
                    ->update($request);
                if (!$updateSppt) :
                    throw new \Exception($this->outputMessage('update fail', 'SPPT NOP ' . $param));
                endif;

                /**
                 * update data tunggakan
                 */
                $updateTunggakan = $this->tunggakan->where('nop', $param)->update([
                    'uuid_user' => authAttribute()['id'],
                    'status_cetak_sppt' => 9
                ]);
                if (!$updateTunggakan) :
                    throw new \Exception($this->outputMessage('update fail', 'tunggakan NOP ' . $param));
                endif;
            endif;

            DB::commit();
            $dataUpdate = $role == 'kasubbid' ? $getPelayanan->nomor_pelayanan : $param;
            $response = $this->success($this->outputMessage('updated', $dataUpdate));
        } catch (\Exception $e) {
            DB::rollback();
            $response  = $this->error($e->getMessage());
        }

        /**
         * send response to controller
         */
        return $response;
    }
}
