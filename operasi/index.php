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
$_SESSION['V1c1T2NHTjNQVDA9_activePage'] = 'operasi';
$_SESSION['V1c1T2NHTjNQVDA9_titlePage'] = 'Daftar Operasi';

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
                            <!-- <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-target="#modalFilter" data-toggle="modal" data-backdrop="static" data-keyboard="false">
                                    <i class="fas fa-filter"></i>
                                </button>
                            </div> -->
                            <?php
                                // echo $jns != '' ? '<span class="badge badge-info mr-1">'.@$jns.'</span>' : '';
                                echo $bln != '' ? '<span class="badge badge-info mr-1">'.namaBulan($bln).'</span>' : '';
                                echo $thn != '' ? '<span class="badge badge-info mr-1">'.$thn.'</span>' : '';
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
                            <input type="hidden" name="ref" value="<?= $slug ?>">
                            <?php
                                foreach ($addTable as $key => $column) {
                                    $nama = $_GET[$column['nama']] ?? null;  // aman
                                    $sql   = $column['sql'] ?? null;          // aman
                                    $object = get_object(
                                        $column['jenis'],
                                        $column['nama'],
                                        $column['tipe'],
                                        false,
                                        $nama,
                                        $sql,
                                        $column['label'],
                                        false,
                                        $column['additional']
                                    );
                                    echo '
                                        <div class="'.$column['div'].'">'.
                                            $object.'
                                        </div>';
                                }
                            ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                        <button type="submit" id="simpanFilter" class="btn btn-primary float-right">Simpan</button>
                        <a href="<?= $httpHost ?>laporan/?ref=<?= $slug ?>" class="btn btn-default float-right">Clear Filter</a>
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
        })

        $('#datepicker-thn').datetimepicker({
            format: 'YYYY'
        });

        $('#datepicker-awal').datetimepicker({
            format: 'DD/MM/YYYY'
        });
        $('#datepicker-akhir').datetimepicker({
            format: 'DD/MM/YYYY'
        });

        $(document).ready(function () {
            tableContent();
            
        });
        $('select[name="inst"]').on('change', function() {
            let value = $(this).val();
            updateSort(value);
        });

        // jalankan sekali saat modal dibuka, supaya sort sesuai inst yang sudah terpilih sebelumnya
        $('#modalFilter').on('shown.bs.modal', function() {
            let instValue = $('select[name="inst"]').val();
            updateSort(instValue);
        });

        function updateSort(instValue) {

            const sort = $('select[name="sort"]'); // target dropdown sort
            sort.empty(); // hapus semua option

            if (instValue === "03") {
                // instalasi = 02 → hilangkan kasus_baru
                sort.append('<option value="kunjungan">Jumlah Kunjungan Pasien</option>');
                sort.append('<option value="keluar_mati">Jumlah Pasien Keluar Mati</option>');
            } else {
                // default
                sort.append('<option value="kunjungan">Jumlah Kunjungan Pasien</option>');
                sort.append('<option value="kasus_baru">Jumlah Kasus Baru</option>');
            }
        }

        function tableContent() {
            let data = {
                bln : '<?= $bln ?>',
                thn : '<?= $thn ?>',
            }
            target = 'table_operasi.php'
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