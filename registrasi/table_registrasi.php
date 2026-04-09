<?php
$codeSource = 'bsis';
$httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
$documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
require $documentRoot.'config/connection.config.php';
require $documentRoot.'config/utility.config.php';
signInCheck();
include ('registrasi.php');
$q = new REGISTRASI();

$unt = $_GET['unt'] ?? null;
$sts = $_GET['sts'] ?? null;
$tglAwal = $_GET['tglAwal'] ?? null;
$tglAkhir = $_GET['tglAkhir'] ?? null;
$dt = $q->get_data($unt, $sts, $tglAwal, $tglAkhir);
?>
<style>
.editable {
    cursor: pointer;
    color: #007bff;
    text-decoration: underline;
}
</style>

<div class="table-responsive">
    <table class="table table-bordered table-hover table-sm" id="tableRegistrasi">
        <thead>
            <tr>
                <th class="text-center align-middle"></th>
                <th class="text-center align-middle">No.</th>
                <th class="text-center align-middle">Tanggal dan Jam</th>
                <th class="text-center align-middle">Nama Pasien</th>
                <th class="text-center align-middle">No. RM</th>
                <th class="text-center align-middle">Umur</th>
                <th class="text-center align-middle">Ruangan</th>
                <th class="text-center align-middle">Diagnosa</th>
                <th class="text-center align-middle">Tindakan</th>
                <th class="text-center align-middle">Operator</th>
                <th class="text-center align-middle">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($dt as $row) :
                switch ($row->status) {
                    case 0:
                        $status = 'Pending';
                        $col = 'warning';
                        break;
                    
                    case 1:
                        $status = 'Disetujui';
                        $col = 'success';
                        break;

                    case 2:
                        $status = 'Ditolak';
                        $col = 'danger';
                        break;
                    
                    default:
                        $status = 'Tidak Valid';
                        $col = 'secondary';
                        break;
                }
            ?>
                <tr class="main-row" data-id="<?= $row->id ?>">
                    <td class="text-center">
                        <i class="fas fa-caret-right toggle-icon"></i>
                    </td>
                    <td class="text-center"><?= $no++ ?></td>
                    <td class="text-center"><?= date("d/m/Y H:i", dateNormalize($row->tanggal)) ?></td>
                    <td><?= htmlspecialchars($row->NamaLengkap) ?></td>
                    <td class="text-center"><?= htmlspecialchars($row->NoCM) ?></td>
                    <td class="text-center"><?= htmlspecialchars($row->Umur) ?></td>
                    <td class="text-center"><?= htmlspecialchars($row->NamaRuangan) ?></td>
                    <td class="text-center"><?= htmlspecialchars($row->diagnosa) ?></td>
                    <td class="text-center"><?= htmlspecialchars($row->tindakan) ?></td>
                    <td class="text-center">
                        <?= $row->id_dokter != null ? $row->dokter_operator : '' ?>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-<?= $col ?>"><?= $status ?></span>
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

    $('#divJamMulai').datetimepicker({
        format: 'HH:mm',
        icons: {
            time: 'fa fa-clock',
        }
    });

    $('#divJamMskKmr').datetimepicker({
        format: 'DD/MM/YYYY HH:mm',
        icons: {
            time: 'fa fa-clock',
        }
    });

    const table =  $("#tableRegistrasi").DataTable({
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
        .appendTo('#tableRegistrasi_wrapper .col-md-6:eq(0)');
    
    $('#tableRegistrasi tbody').on('click', 'tr.main-row', function (e) {
        // ❌ JANGAN toggle jika klik berasal dari tombol / form element
        if (
            $(e.target).closest('.btn-verif').length ||
            $(e.target).closest('.editable').length ||
            $(e.target).is('button, a, input, select, textarea')
        ) {
            return;
        }

        const row  = table.row(this);
        const tr   = $(row.node());
        const icon = tr.find('.toggle-icon');
        const id   = tr.data('id');

        /* AUTO CLOSE ROW LAIN */
        table.rows('.shown').every(function () {
            if (this.node() !== tr[0]) {
                this.child.hide();
                $(this.node())
                    .removeClass('shown')
                    .find('.toggle-icon')
                    .removeClass('fa-caret-down')
                    .addClass('fa-caret-right');
            }
        });

        /* TOGGLE */
        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
            icon.removeClass('fa-caret-down').addClass('fa-caret-right');
            return;
        }

        tr.addClass('shown');
        icon.removeClass('fa-caret-right').addClass('fa-caret-down');

        const cachedHtml = tr.data('detailHtml');
        if (cachedHtml) {
            row.child(cachedHtml).show();
            return;
        }

        row.child('<div class="p-3 text-center text-muted">Memuat detail...</div>').show();

        $.get('<?= $httpHost ?>registrasi/detail_regis.php', { id: id }, function (html) {
            tr.data('detailHtml', html);
            row.child(html).show();
        });
    });
    
    $(document).on('click', '.jam-op', function () {
        const id = $(this).data('id');
        const jamMasuk = $(this).data('msk'); // cek dari row

        if (!jamMasuk) {
            Swal.fire({
                icon: 'warning',
                title: 'Tidak bisa diisi',
                text: 'Silakan isi Jam Masuk Kamar terlebih dahulu.'
            });
            return;
        }

        $('#jamRegId').val($(this).data('id'));
        $('#modalJam').modal('show');
    });

    $(document).on('click', '.msk-kmr', function () {
        $('#mskRegId').val($(this).data('id'));
        $('#modalMskKmr').modal('show');
    });

    $('#saveJam').click(function () {
        const id = $('#jamRegId').val();
        const jam_mulai_op = $('#jamMulai').val();
        const jamMasukText = $('.msk-kmr[data-id="'+id+'"]').text();

        if (jamMasukText === 'Pilih Jam') {
            Swal.fire({
                icon: 'error',
                title: 'Tidak bisa disimpan',
                text: 'Jam Masuk Kamar belum diisi.'
            });
            return;
        }
        Swal.fire({
        title: 'Apakah anda yakin?',
        text: "Anda akan menyimpan data ini!",
        icon: 'info',
        showCancelButton: true,
        cancelButtonText:'Tidak',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, simpan!'
        }).then((result) => {
            if (result.isConfirmed) {
                data = [];
                data.push({name: 'id', value: id});
                data.push({name: 'jam_mulai_op', value: jam_mulai_op});
                // console.log(data)
                $.ajax({
                    type: "POST",
                    url: "<?= $httpHost; ?>operasi/save_jam.php",
                    data: data,
                    dataType: "json",
                    beforeSend: function () {
                        Swal.fire({
                            icon: 'info',
                            title: 'LOADING',
                            text: 'Silahkan menunggu!',
                            footer: '<div class="d-flex justify-content-center"><div class="line-wobble"></div></div>',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                        });
                    },
                    success: function(response) {
                        // console.log(response)
                        Swal.close();
                        if (response.status === "success") {
                            Swal.fire({
                                icon: 'success',
                                title: '<strong>Status Penyimpanan</strong>' + '<br><p style="font-size: 50% !important;"><i class="fa fa-square text-success"></i> Tersimpan</p>',
                                html: response.message,
                                showConfirmButton: false,
                                showCloseButton: false,
                                allowOutsideClick: true,
                                timer: 2500,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer)
                                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                                }
                            });
                            $('.jam-op[data-id="'+id+'"]').text(jam_mulai_op);
                            $('#modalJam').modal('hide');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '<strong>Status Penyimpanan</strong>' + '<br><p style="font-size: 50% !important;"><i class="fa fa-square text-danger"></i> Tidak tersimpan</p>',
                                text: response.message,
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log("XHR Response:", xhr.responseText);
                        console.log("Status:", status);
                        console.log("Error:", error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan dalam sistem.',
                        });
                    }
                });
            }
        })
    });

    $('#saveMskKmr').click(function () {
        const id = $('#mskRegId').val();
        const jam_masuk_kamar = $('#jam_masuk_kamar').val();
        
        Swal.fire({
        title: 'Apakah anda yakin?',
        text: "Anda akan menyimpan data ini!",
        icon: 'info',
        showCancelButton: true,
        cancelButtonText:'Tidak',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, simpan!'
        }).then((result) => {
            if (result.isConfirmed) {
                data = [];
                data.push({name: 'id', value: id});
                data.push({name: 'jam_masuk_kamar', value: jam_masuk_kamar});
                // console.log(data)
                $.ajax({
                    type: "POST",
                    url: "<?= $httpHost; ?>operasi/save_msk_kmr.php",
                    data: data,
                    dataType: "json",
                    beforeSend: function () {
                        Swal.fire({
                            icon: 'info',
                            title: 'LOADING',
                            text: 'Silahkan menunggu!',
                            footer: '<div class="d-flex justify-content-center"><div class="line-wobble"></div></div>',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                        });
                    },
                    success: function(response) {
                        // console.log(response)
                        Swal.close();
                        if (response.status === "success") {
                            Swal.fire({
                                icon: 'success',
                                title: '<strong>Status Penyimpanan</strong>' + '<br><p style="font-size: 50% !important;"><i class="fa fa-square text-success"></i> Tersimpan</p>',
                                html: response.message,
                                showConfirmButton: false,
                                showCloseButton: false,
                                allowOutsideClick: true,
                                timer: 2500,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer)
                                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                                }
                            });
                            $('.msk-kmr[data-id="'+id+'"]').text(jam_masuk_kamar);
                            $('#modalMskKmr').modal('hide');
                            $('.jam-op[data-id="'+id+'"]')
                            .removeClass('text-muted')
                            .text('Pilih Jam')
                            .attr('data-msk', jam_masuk_kamar);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '<strong>Status Penyimpanan</strong>' + '<br><p style="font-size: 50% !important;"><i class="fa fa-square text-danger"></i> Tidak tersimpan</p>',
                                text: response.message,
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log("XHR Response:", xhr.responseText);
                        console.log("Status:", status);
                        console.log("Error:", error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan dalam sistem.',
                        });
                    }
                });
            }
        })
    });

    $(document).on('click', '.operator', function () {
        $('#operatorRegId').val($(this).data('id'));

        $.get('get_dokter.php', function (html) {
            $('#operatorSelect').html(html);
            $('#modalOperator').modal('show');
        });
    });

    $('#saveOperator').click(function () {
        const id = $('#operatorRegId').val();
        const name = $('#operatorSelect option:selected').text();
        
        Swal.fire({
        title: 'Apakah anda yakin?',
        text: "Anda akan menyimpan data ini!",
        icon: 'info',
        showCancelButton: true,
        cancelButtonText:'Tidak',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, simpan!'
        }).then((result) => {
            if (result.isConfirmed) {
                data = [];
                data.push({name: 'id', value: id});
                data.push({name: 'id_dokter', value: $('#operatorSelect').val()});
                // console.log(data)
                $.ajax({
                    type: "POST",
                    url: "<?= $httpHost; ?>operasi/save_operator.php",
                    data: data,
                    dataType: "json",
                    beforeSend: function () {
                        Swal.fire({
                            icon: 'info',
                            title: 'LOADING',
                            text: 'Silahkan menunggu!',
                            footer: '<div class="d-flex justify-content-center"><div class="line-wobble"></div></div>',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                        });
                    },
                    success: function(response) {
                        // console.log(response)
                        Swal.close();
                        if (response.status === "success") {
                            Swal.fire({
                                icon: 'success',
                                title: '<strong>Status Penyimpanan</strong>' + '<br><p style="font-size: 50% !important;"><i class="fa fa-square text-success"></i> Tersimpan</p>',
                                html: response.message,
                                showConfirmButton: false,
                                showCloseButton: false,
                                allowOutsideClick: true,
                                timer: 2500,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer)
                                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                                }
                            });
                            $('.operator[data-id="'+id+'"]').text(name);
                            $('#modalOperator').modal('hide');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '<strong>Status Penyimpanan</strong>' + '<br><p style="font-size: 50% !important;"><i class="fa fa-square text-danger"></i> Tidak tersimpan</p>',
                                text: response.message,
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log("XHR Response:", xhr.responseText);
                        console.log("Status:", status);
                        console.log("Error:", error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan dalam sistem.',
                        });
                    }
                });
            }
        })
    });

    $(document).on('click', '.anestesi', function () {
        $('#anestesiRegId').val($(this).data('id'));

        $.get('get_dokter.php', function (html) {
            $('#anestesiSelect').html(html);
            $('#modalAnestesi').modal('show');
        });
    });

    $('#saveAnestesi').click(function () {
        const id = $('#anestesiRegId').val();
        const name = $('#anestesiSelect option:selected').text();
        
        Swal.fire({
        title: 'Apakah anda yakin?',
        text: "Anda akan menyimpan data ini!",
        icon: 'info',
        showCancelButton: true,
        cancelButtonText:'Tidak',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, simpan!'
        }).then((result) => {
            if (result.isConfirmed) {
                data = [];
                data.push({name: 'id', value: id});
                data.push({name: 'id_dokter_anestesi', value: $('#anestesiSelect').val()});
                // console.log(data)
                $.ajax({
                    type: "POST",
                    url: "<?= $httpHost; ?>operasi/save_anestesi.php",
                    data: data,
                    dataType: "json",
                    beforeSend: function () {
                        Swal.fire({
                            icon: 'info',
                            title: 'LOADING',
                            text: 'Silahkan menunggu!',
                            footer: '<div class="d-flex justify-content-center"><div class="line-wobble"></div></div>',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                        });
                    },
                    success: function(response) {
                        // console.log(response)
                        Swal.close();
                        if (response.status === "success") {
                            Swal.fire({
                                icon: 'success',
                                title: '<strong>Status Penyimpanan</strong>' + '<br><p style="font-size: 50% !important;"><i class="fa fa-square text-success"></i> Tersimpan</p>',
                                html: response.message,
                                showConfirmButton: false,
                                showCloseButton: false,
                                allowOutsideClick: true,
                                timer: 2500,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer)
                                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                                }
                            });
                            $('.anestesi[data-id="'+id+'"]').text(name);
                            $('#modalAnestesi').modal('hide');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '<strong>Status Penyimpanan</strong>' + '<br><p style="font-size: 50% !important;"><i class="fa fa-square text-danger"></i> Tidak tersimpan</p>',
                                text: response.message,
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log("XHR Response:", xhr.responseText);
                        console.log("Status:", status);
                        console.log("Error:", error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan dalam sistem.',
                        });
                    }
                });
            }
        })
    });

    $(document).on('click', '.btn-status', function () {
        const id = $(this).data('id');
        const status = $(this).data('status'); // 1 = setuju, 2 = tolak
        
        if (status == 1) {
            konfirmasiStatus(id, status);
            return;
        }

        Swal.fire({
            title: 'Alasan Penolakan',
            input: 'textarea',
            inputLabel: 'Masukkan alasan penolakan',
            inputPlaceholder: 'Contoh: pasien belum puasa, hasil lab belum lengkap, dll...',
            inputAttributes: {
                'aria-label': 'Alasan penolakan'
            },
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Tolak',
            cancelButtonText: 'Batal',
            inputValidator: (value) => {
                if (!value) {
                    return 'Alasan wajib diisi!';
                }
            }
        }).then((result) => {
            if (!result.isConfirmed) return;

            const alasan = result.value;

            konfirmasiStatus(id, status, alasan);
        });
    });
    
    function konfirmasiStatus(id, status, alasan = '') {

        const text = status == 1
            ? "Anda akan MENYETUJUI jadwal operasi ini?"
            : "Anda akan MENOLAK jadwal operasi ini?";

        Swal.fire({
            title: 'Konfirmasi',
            text: text,
            icon: 'question',
            showCancelButton: true,
            cancelButtonText: 'Batal',
            confirmButtonText: 'Ya, lanjutkan'
        }).then((result) => {

            if (!result.isConfirmed) return;

            $.ajax({
                type: "POST",
                url: "<?= $httpHost ?>registrasi/save_status.php",
                data: {
                    id: id,
                    status: status,
                    alasan: alasan   // ← kirim alasan
                },
                dataType: "json",
                beforeSend: function () {
                    Swal.fire({
                        icon: 'info',
                        title: 'Menyimpan...',
                        showConfirmButton: false,
                        allowOutsideClick: false
                    });
                },
                success: function (response) {
                    Swal.close();

                    if (response.status === "success") {

                        Swal.fire({
                            icon: 'success',
                            title: '<strong>Status Penyimpanan</strong>',
                            html: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        const badgeCell = $('tr.main-row[data-id="'+id+'"] td:last span.badge');

                        if (status == 1) {
                            badgeCell.removeClass('badge-warning badge-danger')
                                    .addClass('badge-success')
                                    .text('Disetujui');
                        } else {
                            badgeCell.removeClass('badge-warning badge-success')
                                    .addClass('badge-danger')
                                    .text('Ditolak');
                        }

                        const tr = $('tr.main-row[data-id="'+id+'"]');
                        const row = table.row(tr);
                        tr.removeData('detailHtml');

                        if (row.child.isShown()) {
                            row.child.hide();
                            tr.removeClass('shown')
                            .find('.toggle-icon')
                            .removeClass('fa-caret-down')
                            .addClass('fa-caret-right');
                        }

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan sistem.'
                    });
                }
            });

        });
    }

})
</script>