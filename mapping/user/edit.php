<?php
    $codeSource = 'bsis';
    $httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
    $documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
    require $documentRoot.'config/connection.config.php';
    require $documentRoot.'config/utility.config.php';
    signInCheck();
    
    include ('user.php');
    $q = new USER();

    $slug = $_GET['ref'];
    $table = 'bsis_menu_aplikasi';
    $jmlCek = $q->cek_table($table, '', $slug);
    if($jmlCek < 1){
        header('Location: '.$httpHost.'404.php');
        die();
    }else{
        $dtM = $q->get_data($table, $slug);
        $cekAkses = cek_akses($_SESSION['V1c1T2NHTjNQVDA9_level'],$_SESSION['V1c1T2NHTjNQVDA9_id_pegawai'],$dtM->id,'0');
        if($cekAkses[0]->update < 1){
            hell:
            header('Location: '.$httpHost.'404.php');
            die();
        }
    }

    $id = $_GET['id'];
    $jmlCek = $q->cek_table('bsis_mapping_user', $id, false, 'id_pegawai', true);
    if($jmlCek < 1){
        header('Location: '.$httpHost.'404.php');
        die();
    }
    $dt = $q->get_data_user_jenis_akses($id);
    $qPegawai = $q->get_pegawai($slug);
    $qJenisUser = $q->get_jenis_user();
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
                                <li class="breadcrumb-item active">Ubah <?php echo $_SESSION['V1c1T2NHTjNQVDA9_titlePage']?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <form id="ubahStruktur" method="post">  
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Ubah <?php echo $_SESSION['V1c1T2NHTjNQVDA9_titlePage']?></h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">    
                                        <div class="form-group">
                                            <label>Pegawai</label>
                                            <p><?= $dt->namapegawai ?></p>
                                            <span class="error invalid-feedback">Atasan harus dipilih</span>
                                        </div>
                                        <div class="form-group">
                                            <label>Jenis User</label>
                                            <select name="id_jenis_user" id="id_jenis_user" class="form-control select2 wajib">
                                                <option value = "">--Silahkan Pilih--</option>
                                                <?php
                                                while($dtJenisUser = $qJenisUser->fetch_object()){
                                                $selJenisUser = $dtJenisUser->id == $dt->id_jenis_user ? 'selected' : '';
                                                echo '<option value="'.$dtJenisUser->id.'" '.$selJenisUser.'>'.$dtJenisUser->nama.'</option>';
                                                }
                                                ?>
                                            </select>
                                            <span class="error invalid-feedback">Atasan harus dipilih</span>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button class="btn btn-primary btn-sm float-right" name="simpan">Simpan</button>
                                        <a href="<?= $httpHost ?>mapping/user/?ref=mapping_user" class="btn btn-danger btn-sm">Batal</a>
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
            $(".select2").select2({
                placeholder: "--Silahkan pilih--",
                allowClear: true
            });
        })
        $("#ubahStruktur").submit(function(e) {
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
                        data = $("#ubahStruktur").serializeArray();
                        data.push({name: 'id_pegawai', value: '<?php echo $id ?>'});
                        $.ajax({
                            type: "POST",
                            url: "proses_ubah.php",
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
                                // Swal.close()
                                // console.log(response)
                                if(response.status == 'sukses'){
                                    window.location = "<?php echo $httpHost ?>mapping/user/?ref=mapping_user"
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