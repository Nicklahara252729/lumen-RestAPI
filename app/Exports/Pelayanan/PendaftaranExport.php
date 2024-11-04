<?php

namespace App\Exports\Pelayanan;

/**
 * import component
 */

use Maatwebsite\Excel\Excel;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * import model
 */

use App\Models\Pelayanan\Pelayanan\Pelayanan;

class PendaftaranExport implements FromQuery, Responsable, WithHeadings
{
    use Exportable;

    private $fileName = 'Pelayanan.xlsx';
    private $writerType = Excel::XLSX;

    public function query()
    {
        $data = Pelayanan::query()
            ->select(
                DB::raw(
                    "nomor_pelayanan,
                    pelayanan.created_at as tanggal_pendaftaran,
                    nama_lengkap,
                    id_pemohon,
                    status_verifikasi,
                    layanan,
                    jenis_layanan"
                )
            )
            ->leftJoin("layanan", "pelayanan.uuid_layanan", "=", "layanan.uuid_layanan")
            ->leftJoin("jenis_layanan", "pelayanan.uuid_jenis_pelayanan", "=", "jenis_layanan.uuid_jenis_layanan")
            ->orderBy('pelayanan.id', 'desc');
            
        return $data;
    }

    public function headings(): array
    {
        return [
            "Nomor Pelayanan",
            "Tanggal Pendaftaran",
            "Nama Lengkap",
            "No KTP",
            "Status Verifikasi",
            "Layanan",
            "Jenis Layanan"
        ];
    }
}
