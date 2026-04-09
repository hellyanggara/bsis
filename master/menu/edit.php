<?php
    $codeSource = 'bsis';
    $httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
    $documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
    require $documentRoot.'config/connection.config.php';
    require $documentRoot.'config/utility.config.php';
    signInCheck();
    
    include ('../master.php');
    $q = new MASTER();

    $id = $_GET['id'];
    $table = 'bsis_menu_aplikasi';
    $jmlCek = $q->cek_table($table, $id,'');
    if($jmlCek < 1){
        header('Location: '.$httpHost.'404.php');
        die();
    }else{
        $tableGroup = 'bsis_group_menu_aplikasi';
        $where = "AND `group` = '1'";
        $qGroup = $q->get_list($tableGroup,false,false,false,false,false,false,$where);
        $dt = $q->get_data($table, $id);
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
                            <form id="ubahMenu" method="post" enctype="multipart/form-data">  
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
                                        <input type="hidden" name="id" id="id" value="<?php echo $id ?>">
                                        <div class="form-group">
                                            <label>Nama</label>
                                            <input name="nama" id="nama" type="text" class="form-control form-control-sm wajib" placeholder="Masukkan Nama" value="<?= $dt->nama ?>" autofocus>
                                            <span class="error invalid-feedback">Nama harus diisi</span>
                                        </div>
                                        <div class="form-group">
                                            <label>Slug</label>
                                            <input name="slug" id="slug" type="text" class="form-control form-control-sm wajib" placeholder="Masukkan Slug" value="<?= $dt->slug ?>" >
                                            <span id="spanSlug" class="error invalid-feedback">Slug harus diisi dan mengandung "_"</span>
                                        </div>
                                        <div class="form-group">
                                            <label>Table</label>
                                            <input name="table" id="table" type="text" class="form-control form-control-sm" placeholder="Masukkan Table" value="<?= $dt->table ?>" >
                                            <span class="error invalid-feedback">Table harus diisi</span>
                                        </div>
                                        <div class="form-group">
                                            <label>Icon</label>
                                            <input name="icon" id="icon" type="text" class="form-control form-control-sm wajib" placeholder="Masukkan Icon" value="<?= $dt->icon ?>">
                                            <span class="error invalid-feedback">Icon harus diisi</span>
                                        </div>
                                        <div class="form-group">
                                            <label>Active Menu</label>
                                            <input name="active_menu" id="active_menu" type="text" class="form-control form-control-sm wajib" placeholder="Masukkan Active" value="<?= $dt->active_menu ?>">
                                            <span class="error invalid-feedback">Active harus diisi</span>
                                        </div>
                                        <div class="form-group">
                                            <label>Direktori</label>
                                            <input name="direktori" id="direktori" type="text" class="form-control form-control-sm wajib" placeholder="Masukkan Direktori" value="<?= $dt->direktori ?>">
                                            <span class="error invalid-feedback">Direktori harus diisi</span>
                                        </div>
                                        <div class="form-group">
                                            <label>Group</label>
                                            <select name="id_group" id="id_group" class="form-control select2 wajib">
                                                <option value = "">--Silahkan Pilih--</option>
                                                <?php
                                                while($dtGroup = $qGroup->fetch_object()){
                                                $sel = $dtGroup->id == $dt->id_group ? 'selected' : '';
                                                echo '<option value="'.$dtGroup->id.'" '.$sel.'>'.$dtGroup->nama.'</option>';
                                                }
                                                ?>
                                            </select>
                                            <span class="error invalid-feedback">Group harus dipilih</span>
                                        </div>
                                        <div class="form-group">
                                            <label>File</label>
                                            <?php
                                                if($dt->file != ''){
                                                    echo '
                                                    <p>
                                                        <a target="_blank" href="'.$httpHost.'master/menu/img/'.$dt->file.'" class="link-black text-sm"><i class="fas fa-link mr-1"></i>'.$dt->file.'</a>
                                                    </p>';
                                                }
                                            ?>
                                            <div class="custom-file">
                                                <input type="file" name="file" id="file" class="custom-file-input">
                                                <label class="custom-file-label" for="file">Choose file</label>
                                            </div>
                                            <span class="error invalid-feedback">File harus dipilih</span>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button class="btn btn-primary btn-sm float-right" name="simpan">Simpan</button>
                                        <a href="index.php" class="btn btn-danger btn-sm">Batal</a>
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
        $("#ubahMenu").submit(function(e) {
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
            if(valid && $('#slug').val().indexOf('_') > -1){
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
                        data = new FormData(this);
                        $.ajax({
                            type: "POST",
                            url: "proses_ubah.php",
                            data: data,
                            cache: false,
                            contentType: false,
                            processData: false,
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
                                    window.location = "index.php"
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