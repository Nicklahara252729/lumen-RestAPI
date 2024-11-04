<?php
/* Call this file 'hello-world.php' */
require __DIR__ . '/vendor/autoload.php';

use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;

try {
    //  Initiate curl
    $noPelayanan = $_GET['noPelayanan'];
    $printer = $_GET['printer'];
    $ch = curl_init('http://192.168.1.5:8000/api/print/permohonan/' . $noPelayanan);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    $respons_decode = json_decode($result, true);
    echo json_encode($respons_decode);
    
    $tanggal_pendaftaran = $respons_decode['data']['tanggal_pendaftaran'];
    $id_pemohon          = $respons_decode['data']['id_pemohon'];
    $jenis_pelayanan     = $respons_decode['data']['jenis_pelayanan'];
    $operator            = $respons_decode['data']['operator'];
    $nama_lengkap        = $respons_decode['data']['nama_lengkap'];

    /**
     * Printer Harus Dishare
     * Nama Printer Contoh: Generic
     */
    $connector = new WindowsPrintConnector($printer);
    $printer = new Printer($connector);

    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $img = EscposImage::load("logo.png", false);
    $printer->bitImage($img);
    $printer->initialize();
    $printer->text("\n");

    $printer->initialize();
    $printer->setFont(Printer::FONT_B);
    $printer->setTextSize(2, 2);
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->text("BPKPAD KOTA BINJAI" . "\n");

    $printer->initialize();
    $printer->setFont(Printer::FONT_B);
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->text("Jl.Jambi Kelurahan Rambung Barat \n Kecamatan Binjai Selatan Kota Binjai");
    $printer->text("\n");

    $printer->initialize();
    $printer->setFont(Printer::FONT_B);
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->text($tanggal_pendaftaran);
    $printer->text("\n");

    $printer->initialize();
    $printer->setFont(Printer::FONT_A);
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->text(strtoupper($nama_lengkap) . "\n");
    $printer->text($id_pemohon . "\n");
    $printer->text("\n");

    $printer->initialize();
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->qrCode($noPelayanan, Printer::QR_ECLEVEL_L, 16);
    $printer->text("\n");

    $printer->initialize();
    $printer->setFont(Printer::FONT_A);
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->text('Nomor Pelayanan ' . $noPelayanan . "\n");
    $printer->text('Pelayanan ' . $jenis_pelayanan . "\n");
    $printer->text("\n");

    $printer->initialize();
    $printer->setFont(Printer::FONT_A);
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->text("Operator : " . $operator . "\n");
    $printer->text("Berkas Permohonan Anda Akan Diproses Secepatnya\n");
    $printer->text("Terima Kasih\n");
    $printer->text("\n");

    $printer->cut();
    $printer->close();

} catch (Exception $e) {
    echo "Couldn't print to this printer: " . $e->getMessage() . "\n";
}
