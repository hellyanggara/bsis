<?php
$documentRoot = $_SERVER['DOCUMENT_ROOT'].'/bsis/';
require $documentRoot.'config/connection.config.php';
require $documentRoot.'config/utility.config.php';

include 'daftar_operasi.php';
$q = new DAFTAR_OPERASI();
$dt = $q->get_data();

// panggil library (tanpa composer)
require $documentRoot.'lib/xlsxwriter.class.php';

$filename = "LaporanDaftarOperasiElektif.xlsx";

// HEADER KOLOM
$header = [
    'No' => 'integer',
    'Nama Pasien' => 'string',
    'No. RM' => 'string',
    'Umur' => 'string',
    'Ruangan' => 'string',
    'Diagnosa' => 'string',
    'Tindakan' => 'string',
    'Operator' => 'string',
    'Anestesi' => 'string',
    'Jam Masuk Kamar' => 'string',
    'Jam Mulai Op' => 'string',
];

$writer = new XLSXWriter();
$styleHeader = [
    'border' => 'top,left,right,bottom',   // sisi mana yang diberi border
    'border-style' => 'thick',             // ketebalan garis
    'font' => 'bold',
    'fill' => '#D9E1F2',
    'halign' => 'center',
];
$styleBody = [
    'border' => 'top,left,right,bottom',
    'border-style' => 'thin',
];
$writer->writeSheetHeader('Daftar Operasi', $header, $styleHeader);

$no = 1;
foreach ($dt as $row) {

    $jamMasuk = $row->jam_masuk_kamar
        ? date("d/m/Y H:i", dateNormalize($row->jam_masuk_kamar))
        : '';

    $jamMulai = $row->jam_mulai_op
        ? date("d/m/Y H:i", dateNormalize($row->jam_mulai_op))
        : '';

    $writer->writeSheetRow('Daftar Operasi', [
        $no++,
        $row->NamaLengkap,
        $row->NoCM,
        $row->Umur,
        $row->NamaRuangan,
        $row->diagnosa,
        $row->tindakan,
        $row->dokter_operator ?? '',
        $row->dokter_anestesi ?? '',
        $jamMasuk,
        $jamMulai
    ], $styleBody);
}

// kirim ke browser
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer->writeToStdOut();
exit;
