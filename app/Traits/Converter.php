<?php

namespace App\Traits;

trait Converter
{
    private $angka;
    private $satuan = array("", "ribu", "juta", "miliar", "triliun", "kuadriliun");

    /**
     * angka ke kata
     */
    private function angkaKeKata($angka)
    {
        $angkaKata = array(
            1 => 'satu',
            2 => 'dua',
            3 => 'tiga',
            4 => 'empat',
            5 => 'lima',
            6 => 'enam',
            7 => 'tujuh',
            8 => 'delapan',
            9 => 'sembilan'
        );

        $ratusan = (int) ($angka / 100);
        $puluhan = (int) (($angka % 100) / 10);
        $satuan = $angka % 10;

        $hasil = '';

        if ($ratusan > 0) {
            if ($angkaKata[$ratusan] == 'satu') {
                $hasil .= 'seratus ';
            } else {
                $hasil .= $angkaKata[$ratusan] . ' ratus ';
            }
        }

        if ($puluhan == 1) {
            if ($satuan == 0) {
                $hasil .= 'sepuluh';
            } elseif ($satuan == 1) {
                $hasil .= 'sebelas';
            } else {
                $hasil .= $angkaKata[$satuan] . ' belas';
            }
        } elseif ($puluhan > 1) {
            $hasil .= $angkaKata[$puluhan] . ' puluh ';
            if ($satuan > 0) {
                $hasil .= $angkaKata[$satuan];
            }
        } elseif ($satuan > 0) {
            $hasil .= $angkaKata[$satuan];
        }

        return $hasil;
    }

    /**
     * konversi ke kalimat
     */
    public function konversiKeKalimat($angka)
    {
        $angka = (string) $angka;

        $pecah = explode('.', $angka);
        $bagianDepan = $pecah[0];
        $bagianBelakang = isset($pecah[1]) ? $pecah[1] : '0';

        $panjangDepan = strlen($bagianDepan);
        $kalimat = "";

        if ($panjangDepan % 3 != 0) {
            $bagianDepan = str_pad($bagianDepan, $panjangDepan + (3 - ($panjangDepan % 3)), "0", STR_PAD_LEFT);
            $panjangDepan = strlen($bagianDepan);
        }

        for ($i = 0; $i < $panjangDepan; $i += 3) {
            $ratusan = (int) $bagianDepan[$i] * 100;
            $puluhan = (int) ($bagianDepan[$i + 1]) * 10;
            $satuan = (int) $bagianDepan[$i + 2];

            $bagianKalimat = $this->angkaKeKata($ratusan + $puluhan + $satuan);

            if (!empty($bagianKalimat)) {
                if ($i > 0) {
                    $kalimat .= ' ' . $this->satuan[($panjangDepan - $i) / 3];
                }
                $kalimat .= ' ' . $bagianKalimat;
            }
        }

        // Tambahkan kondisi untuk membaca koma jika angka adalah angka desimal
        if ($bagianBelakang != '0') {
            $kalimat .= ' koma ';
            $panjangBelakang = strlen($bagianBelakang);

            if ($panjangBelakang == 1) {
                $kalimat .= $this->angkaKeKata($bagianBelakang);
            } else {
                for ($i = 0; $i < $panjangBelakang; $i++) {
                    if ($i == $panjangBelakang - 1 && $bagianBelakang[$i] == '0') {
                        $kalimat .= 'nol';
                    } else {
                        $kalimat .= $this->angkaKeKata((int) $bagianBelakang[$i]);
                    }

                    if ($i < $panjangBelakang - 1) {
                        $kalimat .= ' ';
                    }
                }
            }
        }

        return $kalimat . " Rupiah";
    }

    /**
     * convert to kode pembayaran sts bphtb format
     */
    public function stsBphtbConvert($noSts)
    {
        // Memisahkan angka ke dalam kelompok
        $kelompok1 = substr($noSts, 0, 4);
        $kelompok2 = substr($noSts, 4, 5);
        $kelompok3 = substr($noSts, 9, 6);
        $kelompok4 = substr($noSts, 15, 6);

        // Menggabungkan kelompok dengan titik di antaranya
        $formatAngka = "{$kelompok1}.{$kelompok2}.{$kelompok3}.{$kelompok4}";

        return $formatAngka;
    }

    /**
     * convert nop format
     */
    public function nopConvert($nop)
    {
        // Memisahkan angka ke dalam kelompok
        $kelompok1 = substr($nop, 0, 2);
        $kelompok2 = substr($nop, 2, 2);
        $kelompok3 = substr($nop, 4, 3);
        $kelompok4 = substr($nop, 7, 3);
        $kelompok5 = substr($nop, 10, 3);
        $kelompok6 = substr($nop, 13, 4);
        $kelompok7 = substr($nop, 17, 1);

        // Menggabungkan kelompok dengan titik di antaranya
        $formatAngka = "{$kelompok1}.{$kelompok2}.{$kelompok3}.{$kelompok4}.{$kelompok5}.{$kelompok6}.{$kelompok7}";

        return $formatAngka;
    }
}
