<?php

/**
 * import component
 */

use Illuminate\Support\Facades\Auth;

/**
 * global atribute
 */
function globalAttribute()
{
    return [
        'kdProvinsi'         => 12,
        'kdKota'             => 76,
        'thnAkhirKelasTanah' => 9999,
        'stsBphtb'           => 410115,
        'stsReklame'         => 410109,
        'stsPat'             => 410112,
        'stsRestoran'        => 410107,
        'jumlahPajakRestoran' => 2000
    ];
}

/**
 * auth attribute
 */
function authAttribute()
{
    return [
        'id' => isset(Auth::user()->uuid_user) ? Auth::user()->uuid_user : null,
        'role' => isset(Auth::user()->role) ? Auth::user()->role : null,
        'kd_kecamatan' => isset(Auth::user()->kd_kecamatan) ? Auth::user()->kd_kecamatan : null,
        'kd_kelurahan' => isset(Auth::user()->kd_kelurahan) ? Auth::user()->kd_kelurahan : null,
        'nip' => isset(Auth::user()->nip) ? Auth::user()->nip : null,
    ];
}

/**
 * path
 */
function path($type)
{
    $key  = "type";
    $data = [
        [
            'type' => 'layanan',
            'path' => 'assets/images/layanan/'
        ],
        [
            'type' => 'user',
            'path' => 'assets/images/user/'
        ],
        [
            'type' => 'tunggakan',
            'path' => 'assets/images/tunggakan/'
        ],
        [
            'type' => 'pembatalan transaksi',
            'path' => 'assets/images/pembatalan-transaksi/'
        ],
        [
            'type' => 'pelayanan bphtb',
            'path' => 'assets/images/pelayanan-bphtb/'
        ],
        [
            'type' => 'pelayanan',
            'path' => 'assets/images/pelayanan/'
        ],
        [
            'type' => 'peta op',
            'path' => 'assets/images/peta-objek-pajak/'
        ],
        [
            'type' => 'slider',
            'path' => 'assets/images/slider/'
        ],
        [
            'type' => 'pembayaran manual',
            'path' => 'assets/images/pembayaran-manual/'
        ],
        [
            'type' => 'operator lapangan',
            'path' => 'assets/images/operator-lapangan/'
        ]
    ];

    $filteredArray = array_filter($data, function ($item) use ($key, $type) {
        return $item[$key] === $type;
    });

    return array_values($filteredArray)[0]['path'];
}

/**
 * kecamatan & kelurahan
 */
function kecamatanKelurahan()
{
    return [
        '010' => ['001', '002', '003', '004', '005', '006', '007', '008'],
        '020' => ['001', '002', '003', '004', '005', '006', '007'],
        '030' => ['001', '002', '003', '004', '005', '006', '007'],
        '040' => ['001', '002', '003', '004', '005', '006', '007', '008', '009'],
        '050' => ['001', '002', '003', '004', '005', '006'],
    ];
}
