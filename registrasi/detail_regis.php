<?php
$codeSource = 'bsis';
$httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
$documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
require $documentRoot.'config/connection.config.php';
require $documentRoot.'config/utility.config.php';
require_once $documentRoot.'config/app_helper.php';
signInCheck();
include ('registrasi.php');
$q = new REGISTRASI();

$id = $_GET['id'] ?? null;
$dataRegistrasi = getOr404(fn() => $q->get_detail($id));
$x = '<i class="fa fa-times text-danger"></i>';
$check = '<i class="fa fa-check text-success"></i>';
?>
<div class="row">
    <div class="col-md-4">
        <label class="mb-0">No Pendaftaran</label>
        <h6><?= $dataRegistrasi->NoPendaftaran ?></h6>
    </div>
    <div class="col-md-4">
        <label class="mb-0">Tgl Masuk RS</label>
        <h6><?= date('d/m/Y H:i:s',dateNormalize($dataRegistrasi->TglPendaftaran)) ?></h6>
    </div>
    <div class="col-md-4">
        <label class="mb-0">JK</label>
        <input type="hidden" id="jeniskelamin" value="<?= $dataRegistrasi->JenisKelamin?>"/>
        <h6><?= $dataRegistrasi->JenisKelamin == 'L' ? 'Laki-laki' : 'Perempuan' ?></h6>
    </div>
    <div class="col-md-4">
        <label class="mb-0">Tgl Lahir</label>
        <h6><?= date('d/m/Y',dateNormalize($dataRegistrasi->TglLahir)) ?></h6>
    </div>
    <div class="col-md-4">
        <label class="mb-0">Kelas / Kelompok Pasien</label>
        <h6><?= $dataRegistrasi->DeskKelas.' / '.$dataRegistrasi->NamaPenjamin ?></h6>
    </div>
    <div class="col-md-4">
        <label class="mb-0">Checklist</label>
        <ul>
            <li>Asesmen Operasi <?= $dataRegistrasi->asesmen_operasi == 1 ? $check : $x ?></li>
            <li>Side Marking <?= $dataRegistrasi->side_marking == 1 ? $check : $x ?></li>
            <li>Informed Consent <?= $dataRegistrasi->informed_consent == 1 ? $check : $x ?></li>
            <li>Edukasi <?= $dataRegistrasi->edukasi == 1 ? $check : $x ?></li>
            <li>Konsultasi <?= $dataRegistrasi->konsultasi == 1 ? $check : $x ?></li>
        </ul>
    </div>
    <?php if($dataRegistrasi->status == 2): ?>
    <div class="col-md-12">
        <label class="mb-0">Status Penolakan</label>
        <h6 class="text-danger"><?= nl2br($dataRegistrasi->alasan) ?></h6>
    </div>
    <?php endif; ?>
</div>
<div class="row">
    <div class="col-md-12 text-right">
        <button type="button"
                class="btn btn-danger mr-2 btn-status"
                data-id="<?= $dataRegistrasi->id ?>"
                data-status="2">
            <i class="fa fa-ban"></i> Tolak
        </button>

        <button type="button"
                class="btn btn-primary mr-2 btn-status"
                data-id="<?= $dataRegistrasi->id ?>"
                data-status="1">
            <i class="fa fa-check"></i> Setuju
        </button>
    </div>
</div>