<?php
$codeSource = 'bsis';
$httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
$documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
require $documentRoot.'config/connection.config.php';
require $documentRoot.'config/utility.config.php';
signInCheck();
include ('operasi.php');
$q = new OPERASI();

$bln = $_GET['bln'] ?? null;
$thn = $_GET['thn'] ?? null;
$dt = $q->get_data();
?>
<style>
.editable {
    cursor: pointer;
    color: #007bff;
    text-decoration: underline;
}
</style>

<div class="table-responsive">
    <table class="table table-bordered table-hover table-sm" id="tableOperasi">
        <thead>
            <tr>
                <th class="text-center align-middle"></th>
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
                <tr class="main-row" data-id="<?= $row->id ?>">
                    <td class="text-center">
                        <i class="fas fa-caret-right toggle-icon"></i>
                    </td>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row->NamaLengkap) ?></td>
                    <td class="text-center"><?= htmlspecialchars($row->NoCM) ?></td>
                    <td class="text-center"><?= htmlspecialchars($row->Umur) ?></td>
                    <td class="text-center"><?= htmlspecialchars($row->NamaRuangan) ?></td>
                    <td class="text-center"><?= htmlspecialchars($row->diagnosa) ?></td>
                    <td class="text-center"><?= htmlspecialchars($row->tindakan) ?></td>
                    <td class="text-center">
                        <span class="editable operator"
                            data-id="<?= $row->id ?>">
                            <?= $row->id_dokter != null ? $row->dokter_operator : 'Pilih Operator' ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="editable anestesi"
                            data-id="<?= $row->id ?>">
                            <?= $row->id_dokter_anestesi != null ? $row->dokter_anestesi : 'Pilih Anestesi' ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="editable msk-kmr"
                            data-id="<?= $row->id ?>">
                            <?= $row->jam_masuk_kamar != null ? date("d/m/Y h:i", dateNormalize($row->jam_masuk_kamar)) : 'Pilih Jam' ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="editable jam-op"
                            data-id="<?= $row->id ?>"
                            data-msk="<?= $row->jam_masuk_kamar ?>">
                            <?= 
                            ($row->jam_masuk_kamar == null)
                            ? '<span class="text-muted">Isi Jam Masuk Kamar dulu</span>'
                            : ($row->jam_mulai_op != null 
                            ? date("h:i", dateNormalize($row->jam_mulai_op)) 
                            : 'Pilih Jam') 
                            ?>
                        </span>
                    </td>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="modal fade" id="modalOperator">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pilih Dokter Operator</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <select id="operatorSelect" class="form-control select2">
            <!-- diisi ajax -->
        </select>
        <input type="hidden" id="operatorRegId">
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary float-right" id="saveOperator">Simpan</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="modalAnestesi">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pilih Dokter Anestesi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <select id="anestesiSelect" class="form-control select2">
            <!-- diisi ajax -->
        </select>
        <input type="hidden" id="anestesiRegId">
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary float-right" id="saveAnestesi">Simpan</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="modalJam">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Jam Mulai Operasi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="jamRegId">
        <div class="input-group date" id="divJamMulai" data-target-input="nearest">
            <input type="text" class="form-control form-control-sm datetimepicker-input wajib" id="jamMulai" name="jamMulai" data-target="#divJamMulai" value="" placeholder="Silahkan pilih">
            <div class="input-group-append" data-target="#divJamMulai" data-toggle="datetimepicker">
                <div class="input-group-text">
                    <i class="fa fa-calendar"></i>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary float-right" id="saveJam">Simpan</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="modalMskKmr">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Jam Masuk Kamar</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="mskRegId">
        <div class="input-group date" id="divJamMskKmr" data-target-input="nearest">
            <input type="text" class="form-control form-control-sm datetimepicker-input wajib" id="jam_masuk_kamar" name="jam_masuk_kamar" data-target="#divJamMskKmr" value="" placeholder="Silahkan pilih">
            <div class="input-group-append" data-target="#divJamMskKmr" data-toggle="datetimepicker">
                <div class="input-group-text">
                    <i class="fa fa-calendar"></i>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary float-right" id="saveMskKmr">Simpan</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
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
    
    $('#tableOperasi tbody').on('click', 'tr.main-row', function (e) {
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

        $.get('<?= $httpHost ?>operasi/detail_operasi.php', { id: id }, function (html) {
            tr.data('detailHtml', html);
            row.child(html).show();
        });
    });
    
    $(document).on('click', '.jam-op', function () {
        const id = $(this).data('id');
        const jamMasuk = $(this).attr('data-msk');; // cek dari row

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
})
</script>