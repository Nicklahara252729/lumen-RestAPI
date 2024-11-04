<?php

namespace App\Traits;

/**
 * import helper
 */

use App\Libraries\CheckerHelpers;

trait Message
{
    /**
     * message for response
     */
    public function outputMessage(string $type, string $value = null)
    {
        $key = "type";
        $data = [
            [
                // data ada atau kosong
                'type' => 'data',
                'message' => ($value > 0) ? "data found" : "data empty",
            ],
            [
                // data tidak ditemukan
                'type' => 'not found',
                'message' => 'data ' . $value . ' tidak ditemukan',
            ],
            [
                // data berhasil di simpan
                'type' => 'saved',
                'message' => 'data ' . $value . ' berhasil disimpan',
            ],
            [
                // data gagal disimpan
                'type' => 'unsaved',
                'message' => 'data ' . $value . ' gagal disimpan',
            ],
            [
                // data berhasil di ubah
                'type' => 'updated',
                'message' => 'data ' . $value . ' berhasil diubah',
            ],
            [
                // data gagal diubah
                'type' => 'update fail',
                'message' => 'data ' . $value . ' gagal diubah',
            ],
            [
                // data suda ada
                'type' => 'exists',
                'message' => 'data ' . $value . ' sudah ada / terdaftar',
            ],
            [
                // data berhasil di hapus
                'type' => 'deleted',
                'message' => 'data ' . $value . ' berhasil dihapus',
            ],
            [
                // data gagal di hapus
                'type' => 'undeleted',
                'message' => 'data ' . $value . ' gagal dihapus',
            ],
            [
                // format file tidak didukung
                'type' => 'unsupported',
                'message' => 'format file yang di upload tidak didukung',
            ],
            [
                // directori tidak ditemukan
                'type' => 'directory',
                'message' => 'directori tidak ditemukan',
            ],
            [
                // file yang diupload tidak ada
                'type' => 'no file',
                'message' => 'file yang akan diupload tidak ada',
            ],
            [
                // gagal menghapus data
                'type' => 'remove fail',
                'message' => 'gagal menghapus file ' . $value . ' dari penyimpanan server',
            ],
            [
                // unsend notification
                'type' => 'unsend',
                'message' => 'gagal mengirim notifikasi ' . $value,
            ],
            [
                // logout
                'type' => 'logout',
                'message' => 'Successfully logged out',
            ],
            [
                // pembatasan akses
                'type' => 'resitrict',
                'message' => 'anda bukan ' . $value,
            ],
            [
                // terdapat piutang
                'type' => 'credit',
                'message' => 'NOP ' . $value . ' memiliki piutang',
            ],
            [
                // penggabungan
                'type' => 'mergered',
                'message' => 'data ' . $value . ' berhasil digabung',
            ],
            [
                // fasum
                'type' => 'fasum',
                'message' => 'NOP ' . $value . ' berhasil diubah menjadi FASUM ',
            ],
            [
                // permohonan belum dibayar
                'type' => 'unpaid',
                'message' => 'Data ' . $value . ' belum dibayar ',
            ],
            [
                // tidak sesuai
                'type' => 'unmatch',
                'message' => 'Data ' . $value . ' yang dikirim tidak sesuai',
            ],
        ];

        $filteredArray = array_filter($data, function ($item) use ($key, $type) {
            return $item[$key] === $type;
        });

        return ucwords(array_values($filteredArray)[0]['message']);
    }

    /**
     * set for log
     */
    protected function setForLog(string $table, string $value)
    {
        try {
            $checkerHelpers = new CheckerHelpers;
            if ($table == 'bidang') :
                $getData = $checkerHelpers->bidangChecker(['uuid_bidang' => $value]);
                $value = 'bidang ' . $getData->nama_bidang;
            elseif ($table == 'sub bidang') :
                $getData = $checkerHelpers->subBidangChecker(['uuid_sub_bidang' => $value]);
                $value = 'sub bidang ' . $getData->nama_sub_bidang;
            elseif ($table == 'setting') :
                $getData = $checkerHelpers->settingChecker($value);
                $value = $getData->description == null || $getData->description == '' ? 'null' : $getData->description;
            elseif ($table == 'setting layanan') :
                $getData = $checkerHelpers->layananChecker(['uuid_layanan' => $value]);
                $value = 'status layanan ' . $getData->layanan;
            elseif ($table == 'setting slider') :
                $getData = $checkerHelpers->sliderChecker(['uuid_slider' => $value]);
                $value = 'slider ' . json_encode($getData);
            elseif ($table == 'setting menu') :
                $getData = $checkerHelpers->menuChecker(['uuid_menu' => $value]);
                $value = 'menu ' . json_encode($getData);
            elseif ($table == 'user') :
                $getData = $checkerHelpers->userChecker(['uuid_user' => $value]);
                $value = 'user ' . json_encode($getData);
            elseif ($table == 'layanan') :
                $getData = $checkerHelpers->layananChecker(['uuid_layanan' => $value]);
                $value = 'layanan ' . $getData->layanan;
            elseif ($table == 'jenis layanan') :
                $getData = $checkerHelpers->jenisLayananChecker(['uuid_jenis_layanan' => $value]);
                $value = 'jenis layanan ' . $getData->jenis_layanan;
            elseif ($table == 'pbb minimal') :
                $getData = $checkerHelpers->pbbMInimalChecker(['THN_PBB_MINIMAL' => $value]);
                $value = 'pbb minimal ' . json_encode($getData);
            elseif ($table == 'status pelayanan' || $table == 'pelayanan') :
                $getData = $checkerHelpers->pelayananChecker(['uuid_pelayanan' => $value]);
                $value = $table == 'status pelayanan' ? 'status verifikasi ' . $getData->status_verifikasi : json_encode($getData);
            elseif ($table == 'jenis perolehan') :
                $getData = $checkerHelpers->jenisPerolehanChecker(['uuid_jenis_perolehan' => $value]);
                $value = 'jenis perolehan ' . $getData->jenis_perolehan;
            elseif ($table == 'pelayanan') :
                $getData = $checkerHelpers->pelayananChecker(['uuid_pelayanan' => $value]);
                $value = json_encode($getData);
            elseif ($table == 'pelayanan bphtb' || $table == 'status pelayanan bphtb') :
                $getData = $checkerHelpers->pelayananBphtbChecker(['uuid_pelayanan_bphtb' => $value]);
                $value = $table == 'status pelayanan' ? 'status verifikasi ' . $getData->status_verifikasi : json_encode($getData);
            elseif ($table == 'npoptkp') :
                $getData = $checkerHelpers->npoptkpChecker(['uuid_npoptkp' => $value]);
                $value = 'npoptkp ' . json_encode($getData);
            elseif ($table == 'status nop') :
                $value = 'status NOP ' . $value;
            endif;
        } catch (\Exception $e) {
            $value = $e->getMessage();
        }

        return $value;
    }

    /**
     * message for log
     */
    public function outputLogMessage(string $type, string $value = null, string $moreValue = null, string $table = null)
    {
        if ($type == 'update' || $type == 'delete') :
            $value = $this->setForLog($table, $value);
        endif;

        $key = "type";
        $data = [
            [
                // login success
                'type' => 'login success',
                'action' => 'login berhasil',
                'message' => 'percobaan login oleh ' . $value,
            ],
            [
                // login fail
                'type' => 'login fail',
                'action' => 'login gagal',
                'message' => 'akun ' . $value . ' tidak ditemukan',
            ],
            [
                // logout
                'type' => 'logout',
                'action' => 'berhasil logout',
                'message' => 'berhasil keluar dari sistem',
            ],
            [
                // validation token
                'type' => 'validation',
                'action' => 'validasi token',
                'message' => 'percobaan validasi token user',
            ],
            [
                // refresh token
                'type' => 'refresh',
                'action' => 'refresh token',
                'message' => 'percobaan refresh token user',
            ],
            [
                // get all data
                'type' => 'all data',
                'action' => 'get all data ' . $value,
                'message' => 'percobaan mengambil semua data ' . $moreValue,
            ],
            [
                // get single data
                'type' => 'single data',
                'action' => 'get single data ' . $value,
                'message' => 'percobaan mengambil 1 data dengan ' . $moreValue,
            ],
            [
                // save data
                'type' => 'save',
                'action' => 'save data',
                'message' => 'berhasil menyimpan data ' . $value,
            ],
            [
                // update data
                'type' => 'update',
                'action' => 'update data ' . $value,
                'message' => 'berhasil mengubah data ' . $value . ' menjadi ' . $moreValue,
            ],
            [
                // delete data
                'type' => 'delete',
                'action' => 'delete data',
                'message' => 'percobaan menghapus data ' . $value,
            ],
            [
                // total data
                'type' => 'total',
                'action' => 'get total data',
                'message' => 'mengambil total data ' . $value,
            ],
            [
                // search data
                'type' => 'search',
                'action' => 'search data ' . $value,
                'message' => 'pencarian data ' . $value . ' berdasarkan ' . $moreValue,
            ],
            [
                // generate
                'type' => 'generate',
                'action' => 'generate data ' . $value,
                'message' => 'generate data ' . $value . ' value ' . $moreValue,
            ],
            [
                // export
                'type' => 'export',
                'action' => 'export data',
                'message' => 'export data ' . $value,
            ],
            [
                // count data
                'type' => 'count',
                'action' => 'count data',
                'message' => 'menghitung total data ' . $value,
            ],
            [
                // data sudah ada
                'type' => 'exists',
                'action' => 'data exists',
                'message' => 'data sudah ada',
            ],
        ];

        $filteredArray = array_filter($data, function ($item) use ($key, $type) {
            return $item[$key] === $type;
        });

        $return = [
            'action' => ucwords(array_values($filteredArray)[0]['action']),
            'message' => ucwords(array_values($filteredArray)[0]['message']),
        ];

        return $return;
    }

    /**
     * message log for bphtb
     */
    public function outputLogMessageBphtb(string $type, string $value = null, string $moreValue = null)
    {
        $key = "type";
        $data = [
            [
                // store 
                'type' => 'store',
                'action' => 'pembuatan permohonan baru',
                'message' => 'pembuatan permohonan baru dengan value ' . $value,
            ],
            [
                // update 
                'type' => 'update',
                'action' => 'edit data',
                'message' => 'edit permohonan BPHTB dari ' . $value . ', menjadi ' . $moreValue,
            ],
            [
                // update status verifiksai 
                'type' => 'update status verifikasi',
                'action' => 'update status verifikasi',
                'message' => 'update status verifikasi BPHTB dari ' . $value . ', menjadi ' . $moreValue,
            ],
            [
                // status ditolak
                'type' => 'reject',
                'action' => 'data ditolak',
                'message' => 'permohonan BPHTB ditolak',
            ],
            [
                // perhitungan njop
                'type' => 'perhitungan njop',
                'action' => 'update NJOP',
                'message' => 'update nilai NJOP dengan nilai value ' . $value,
            ],
            [
                // delete dokumen
                'type' => 'delete document',
                'action' => 'hapus dokumen',
                'message' => 'menghapus dokumen ' . $value,
            ],
            [
                // perhitungan bphtb
                'type' => 'perhitungan bphtb',
                'action' => 'update perhitungan BPHTB',
                'message' => 'update nilai BPHTB dengan nilai value ' . $value,
            ],
            [
                // delete
                'type' => 'delete',
                'action' => 'hapus data',
                'message' => 'menghapus data BPHTB',
            ],
        ];

        $filteredArray = array_filter($data, function ($item) use ($key, $type) {
            return $item[$key] === $type;
        });

        $return = [
            'action' => ucwords(array_values($filteredArray)[0]['action']),
            'message' => ucwords(array_values($filteredArray)[0]['message']),
        ];

        return $return;
    }
}
