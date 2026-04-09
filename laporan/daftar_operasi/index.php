<?php
$codeSource = 'bsis';
$httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
$documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
require $documentRoot.'config/connection.config.php';
require $documentRoot.'config/utility.config.php';
signInCheck();
include ('daftar_operasi.php');
$q = new DAFTAR_OPERASI();

$unt = $_GET['unt'] ?? null;
$tglAwal = $_GET['tglAwal'] ?? null;
$tglAkhir = $_GET['tglAkhir'] ?? null;
$_SESSION['V1c1T2NHTjNQVDA9_activePage'] = 'laporanDiagnosa';
$_SESSION['V1c1T2NHTjNQVDA9_titlePage'] = 'Daftar Operasi Elektif';
$listRuangan = $q->getRuangan();
$dtRuangan = $q->get_data_Ruangan($unt);

?>

<!DOCTYPE html>
<html>
<head>
    <?php include $documentRoot.'config/style.config.php';?>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed text-sm">
    <div class="wrapper">
        <?php include $documentRoot.'config/nav.config.php';?>
        <?php include $documentRoot.'config/aside/bsis.aside.php';?>
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0"><?= $_SESSION['V1c1T2NHTjNQVDA9_titlePage'] ?></h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="../dashboard/">Dashboard</a></li>
                            <li class="breadcrumb-item active"><?= $_SESSION['V1c1T2NHTjNQVDA9_titlePage'] ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><?= $_SESSION['V1c1T2NHTjNQVDA9_titlePage'] ?></h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-target="#modalFilter" data-toggle="modal" data-backdrop="static" data-keyboard="false">
                                    <i class="fas fa-filter"></i>
                                </button>
                            </div><br>
                            <?php
                                echo $tglAwal != '' ? '<span class="badge badge-info mr-1">'.@$tglAwal.'</span>' : '';
                                echo $tglAkhir != '' ? '<span class="badge badge-info mr-1">'.@$tglAkhir.'</span>' : '';
                                echo $unt != '' ? '<span class="badge badge-info mr-1">'.$dtRuangan->NamaRuangan.'</span>' : '';
                            ?>
                        </div>
                        <div class="card-body">
                            <div id="tableList"></div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <div class="modal fade" id="modalFilter">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="card-title">Filter</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="" method="get" autocomplete="off">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tanggal Awal</label>
                                    <div class="input-group date" id="awal_aktif" data-target-input="nearest">
                                        <input type="text" id="tgl_awal" name="tglAwal" class="form-control form-control-sm datetimepicker-input wajib" data-target="#awal_aktif" value="<?= $tglAwal ?>" placeholder="Silahkan pilih" autocomplete="off"/>
                                        <div class="input-group-append" data-target="#awal_aktif" data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                        <span id="err_tgl_awal" class="error invalid-feedback">Tgl mulai berlaku wajib diisi</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tanggal Akhir</label>
                                    <div class="input-group date" id="akhir_aktif" data-target-input="nearest">
                                        <input type="text" id="tgl_akhir" name="tglAkhir" class="form-control form-control-sm datetimepicker-input wajib" data-target="#akhir_aktif" value="<?= $tglAkhir ?>" placeholder="Silahkan pilih" autocomplete="off"/>
                                        <div class="input-group-append" data-target="#akhir_aktif" data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                        <span id="err_tgl_akhir" class="error invalid-feedback">Tanggal akhir berlaku wajib diisi</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Ruangan</label>
                                    <select class="form-control select2" name="unt" id="unt">
                                        <option></option>
                                        <?php foreach ($listRuangan as $row): 
                                        $selected = $row->KdRuangan == $unt ? 'selected' : '';
                                        ?>
                                            <option value = "<?= $row->KdRuangan ?>" <?= $selected ?>><?= $row->NamaRuangan ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                        <button type="submit" id="simpanFilter" class="btn btn-primary float-right">Simpan</button>
                        <a href="<?= $httpHost ?>laporan/daftar_operasi" class="btn btn-default float-right">Clear Filter</a>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <?php include $documentRoot.'config/footer.config.php';?>
    </div>
    <?php include $documentRoot.'config/script.config.php';?>
    <script>
        $(function () {
            $('.select2').select2({
                placeholder: "--Silahkan pilih--",
                allowClear: true,
                width: '100%',
            });

            $('#awal_aktif').datetimepicker({
                icons: { time: 'far fa-clock' },
                format: 'DD/MM/YYYY'
            });

            $('#akhir_aktif').datetimepicker({
                icons: { time: 'far fa-clock' },
                format: 'DD/MM/YYYY'
            });
        })

        $(document).ready(function () {
            tableContent();
            
        });

        function tableContent() {
            let data = {
                unt : '<?= $unt ?>',
                tglAwal : '<?= $tglAwal ?>',
                tglAkhir : '<?= $tglAkhir ?>',
            }
            target = 'table_daftar_operasi.php'
            $.ajax({
                type: 'GET',
                url: target,
                data: data,
                beforeSend: function () {
                    Swal.fire({ 
                        imageUrl: '<?= $httpHost ?>vendor/dist/img/bsis.png',
                        imageHeight: 60,
                        imageAlt: 'bsis',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        footer: '<div class="d-flex justify-content-center"><div class="line-wobble"></div></div>',
                        title: 'Data sedang disiapkan..',
                    });
                },
                success: function (res) {
                    <?php if(!isset($_SESSION['V1c1T2NHTjNQVDA9_notif_status'])){ ?>
                        Swal.close();
                    <?php }?>
                    $('#tableList').html(res);
                },
                error: function (xhr, status, error) {
                    if (xhr.status === 404) {
                        $.get('<?= $httpHost ?>503.php', function(notFoundContent) {
                            $('#tableList').html(notFoundContent);
                        });
                        Swal.close();
                    } else {
                        console.error('AJAX Error:', status, error);
                        Swal.fire('Error', 'Terjadi kesalahan saat memuat data.', 'error');
                    }
                }
            });
        }
    </script>
</body>
</html>