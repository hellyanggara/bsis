<?php
    $codeSource = 'bsis';
    $httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
    $documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
    require $documentRoot.'config/connection.config.php';
    require $documentRoot.'config/utility.config.php';
    signInCheck();

    $slug = $_GET['ref'];

    require 'super.php';
    $q = new SUPER();

    $table = 'bsis_menu_aplikasi';
    $jmlCek = $q->cek_table($table, '', $slug);
    if($jmlCek < 1){
        header('Location: '.$httpHost.'404.php');
        die();
    }else{
        $dt = $q->get_data($table, $slug);
        $addTable = $q->get_additional_column($dt->table);
    }
?>
<!DOCTYPE html>
<html>
<head>
    <?php include $documentRoot.'config/style.config.php';?>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed text-sm">
    <div class="wrapper">
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="<?php echo $httpHost ?>vendor/dist/img/rskd.png" alt="RSKDLogo" height="60" width="60">
        </div>
        <?php include $documentRoot.'config/nav.config.php';?>
        <?php include $documentRoot.'config/aside/bsis.aside.php';?>
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-12 float-right">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo $httpHost ?>dashboard/">Dashboard</a></li>
                                <li class="breadcrumb-item active">Master</li>
                                <li class="breadcrumb-item active"><a href="index.php"><?php echo $_SESSION['V1c1T2NHTjNQVDA9_titlePage']?></a></li>
                                <li class="breadcrumb-item active">Tambah <?php echo $_SESSION['V1c1T2NHTjNQVDA9_titlePage']?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <form id="tambahDampak" method="post">  
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Tambah <?php echo $_SESSION['V1c1T2NHTjNQVDA9_titlePage']?></h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">    
                                        <input type="hidden" name="slug" value="<?= $slug ?>">
                                        <div class="form-group">
                                            <label>Nama</label>
                                            <input name="nama" id="nama" type="text" class="form-control form-control-sm wajib" placeholder="Masukkan Nama" autofocus>
                                            <span class="error invalid-feedback">Nama harus diisi</span>
                                        </div>
                                        <?php
                                            foreach ($addTable as $key => $column) {
                                                $label = @$column['label'];
                                                $object = get_object($column['jenis'], $column['nama'], $column['tipe'], $column['wajib'], false, $column['sql'], $label);
                                                echo $object;
                                            }
                                        ?>
                                        <div class="form-group">
                                            <label>Keterangan</label>
                                            <code>.Optional</code>
                                            <textarea name="keterangan" id="keterangan" class="form-control form-control-sm" placeholder="Masukkan Keterangan"></textarea>
                                            <span class="error invalid-feedback">Keterangan harus diisi</span>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button class="btn btn-primary btn-sm float-right" name="simpan">Simpan</button>
                                        <a href="<?= $httpHost ?>super/?ref=<?= $slug ?>" class="btn btn-danger btn-sm">Batal</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
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
        $("#tambahDampak").submit(function(e) {
            e.preventDefault();
            var all = $(".wajib");
            all.each(function() {
                if($(this).val() == ''){
                    $(this).addClass("is-invalid")
                    $(this).focus()
                } else {
                    $(this).removeClass("is-invalid")
                }
            });
            var valid = true;
            all.each(function (index, element) {
                if ($(this).val() == '') {
                    valid = false;
                }
            });
            if(valid){
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
                        data = $("#tambahDampak").serializeArray();
                        $.ajax({
                            type: "POST",
                            url: "proses_simpan.php",
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
                                // swal.close()
                                // console.log(response)
                                if(response.status == 'sukses'){
                                    window.location = "<?= $httpHost ?>super/?ref=<?= $slug ?>"
                                }else{
                                    window.location = ""
                                }
                            }
                        });
                    }
                })
            }
        });
    </script>
</body>
</html>