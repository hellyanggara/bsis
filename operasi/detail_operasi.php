<?php
$codeSource = 'bsis';
$httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
$documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
require $documentRoot.'config/connection.config.php';
require $documentRoot.'config/utility.config.php';
require_once $documentRoot.'config/app_helper.php';
signInCheck();
include ('operasi.php');
$q = new OPERASI();

$id = $_GET['id'] ?? null;
$dataOperasi = getOr404(fn() => $q->get_detail($id));
$x = '<i class="fa fa-times text-danger"></i>';
$check = '<i class="fa fa-check text-success"></i>';
?>
<div class="row">
    <div class="col-md-4">
        <label class="mb-0">No Pendaftaran</label>
        <h6><?= $dataOperasi->NoPendaftaran ?></h6>
    </div>
    <div class="col-md-4">
        <label class="mb-0">Tgl Masuk RS</label>
        <h6><?= date('d/m/Y H:i:s',dateNormalize($dataOperasi->TglPendaftaran)) ?></h6>
    </div>
    <div class="col-md-4">
        <label class="mb-0">JK</label>
        <input type="hidden" id="jeniskelamin" value="<?= $dataOperasi->JenisKelamin?>"/>
        <h6><?= $dataOperasi->JenisKelamin == 'L' ? 'Laki-laki' : 'Perempuan' ?></h6>
    </div>
    <div class="col-md-4">
        <label class="mb-0">Tgl Lahir</label>
        <h6><?= date('d/m/Y',dateNormalize($dataOperasi->TglLahir)) ?></h6>
    </div>
    <div class="col-md-4">
        <label class="mb-0">Kelas / Kelompok Pasien</label>
        <h6><?= $dataOperasi->DeskKelas.' / '.$dataOperasi->NamaPenjamin ?></h6>
    </div>
    <div class="col-md-4">
        <label class="mb-0">Checklist</label>
        <ul>
            <li>Asesmen Operasi <?= $dataOperasi->asesmen_operasi == 1 ? $check : $x ?></li>
            <li>Side Marking <?= $dataOperasi->side_marking == 1 ? $check : $x ?></li>
            <li>Informed Consent <?= $dataOperasi->informed_consent == 1 ? $check : $x ?></li>
            <li>Edukasi <?= $dataOperasi->edukasi == 1 ? $check : $x ?></li>
            <li>Konsultasi <?= $dataOperasi->konsultasi == 1 ? $check : $x ?></li>
        </ul>
    </div>
</div>