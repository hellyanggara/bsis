<?php
$codeSource = 'bsis';
$httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
$documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
require $documentRoot.'config/connection.config.php';
require $documentRoot.'config/utility.config.php';
signInCheck();
include ('daftar_operasi.php');
$q = new DAFTAR_OPERASI();

$tglAwal = $_GET['tglAwal'] ?? null;
$tglAkhir = $_GET['tglAkhir'] ?? null;
$unt = $_GET['unt'] ?? null;
$dt = $q->get_data($tglAwal, $tglAkhir, $unt);
?>
<div class="table-responsive">
    <table class="table table-bordered table-hover table-sm" id="tableOperasi">
        <thead>
            <tr>
                <th class="text-center align-middle">No.</th>
                <th class="text-center align-middle">Nama Pasien</th>
                <th class="text-center align-middle">No. RM</th>
                <th class="text-center align-middle">Umur</th>
                <th class="text-center align-middle">Ruangan</th>
                <th class="text-center align-middle">Diagnosa</th>
                <th class="text-center align-middle">Tindakan</th>
                <th class="text-center align-middle">Operator</th>
                <th class="text-center align-middle">Anestesi</th>
                <th class="text-center align-middle">Jam Masuk Kamar</th>
                <th class="text-center align-middle">Jam Mulai Op</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($dt as $row) :?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row->NamaLengkap) ?></td>
                    <td class="text-center"><?= htmlspecialchars($row->NoCM) ?></td>
                    <td class="text-center"><?= htmlspecialchars($row->Umur) ?></td>
                    <td class="text-center"><?= htmlspecialchars($row->NamaRuangan) ?></td>
                    <td class="text-center"><?= htmlspecialchars($row->diagnosa) ?></td>
                    <td class="text-center"><?= htmlspecialchars($row->tindakan) ?></td>
                    <td class="text-center">
                        <?= $row->id_dokter != null ? $row->dokter_operator : 'Pilih Operator' ?>
                    </td>
                    <td class="text-center">
                        <?= $row->id_dokter_anestesi != null ? $row->dokter_anestesi : '' ?>
                    </td>
                    <td class="text-center">
                        <?= $row->jam_masuk_kamar != null ? date("d/m/Y h:i", dateNormalize($row->jam_masuk_kamar)) : '' ?>
                    </td>
                    <td class="text-center">
                        <?= $row->jam_mulai_op != null ? date("h:i", dateNormalize($row->jam_mulai_op)) : '' ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script>
$(function () {
    $('.select2').select2({
        placeholder: "--Silahkan pilih--",
        allowClear: true,
        width: '100%',
    });

    const table =  $("#tableOperasi").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "pageLength": 10,
        "buttons": [
            {
                text: 'Excel',
                action: function ( e, dt, node, config ) {
                    window.location = "export_excel.php"
                }
            },
            {
                extend: "pdf",
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: "print",
                exportOptions: {
                    columns: ':visible'
                }
            },
            "colvis"
        ]
    });
    table.buttons().container()
        .appendTo('#tableOperasi_wrapper .col-md-6:eq(0)');
})
</script>