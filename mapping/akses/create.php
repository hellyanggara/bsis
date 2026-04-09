<?php
    $codeSource = 'bsis';
    $httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
    $documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
    require $documentRoot.'config/connection.config.php';
    require $documentRoot.'config/utility.config.php';
    signInCheck();
    include ('akses.php');
    $q = new AKSES();

    $id = $_GET['id'];
    $slug = $_GET['ref'];
    switch ($slug) {
        case 'akses_jenis_user':
            $table = 'bsis_jenis_user';
            break;
        case 'akses_user':
            $table = 'bsis_mapping_user';
            break;
    }
    $jmlCek = $q->cek_table($table, $id, $slug);
    if($jmlCek < 1){
        header('Location: '.$httpHost.'404.php');
        die();
    }

    $qGroup = $q->get_group_menu();
    $dtJenisAkses = $q->get_jenis_user($id, $slug);
    $qMapping = $q->get_mapping($id, $slug);
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
                        <div class="col-sm-6">
                            <h1 class="m-0">Tambah Mapping <?php echo $dtJenisAkses->nama ?></h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                                <li class="breadcrumb-item active"><a href="index.php">Mapping</a></li>
                                <li class="breadcrumb-item active">Tambah Hak Akses per Jenis User</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-4">
                            <form id="addMapping" method="post">  
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Mapping</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">  
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Group Menu Aplikasi</label>
                                                    <input type="hidden" name="group" id="group">
                                                    <input type="hidden" name="sub_group" id="sub_group">
                                                    <input type="hidden" name="id_primary" id="id_primary" value="<?= $id ?>">
                                                    <select name="id_group_aplikasi" id="id_group_aplikasi" class="form-control select2 wajib">
                                                        <option value = "">--Silahkan Pilih--</option>
                                                        <?php
                                                        while($dtMenu = $qGroup->fetch_object()){
                                                        $idGroup = $dtMenu->group != "0" ? $dtMenu->id : $dtMenu->group;
                                                        echo '<option value="'.$dtMenu->id.'" data-group="'.$idGroup.'">'.$dtMenu->nama.'</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                    <span class="error invalid-feedback">Group menu harus dipilih</span>
                                                </div>
                                                <div id="subGroup"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button class="btn btn-primary btn-sm float-right" name="simpan">Simpan</button>
                                        <a href="<?= $httpHost ?>mapping/akses/?ref=<?= $slug ?>" class="btn btn-warning btn-sm">Kembali</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-8">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Data Mapping</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">  
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-bordered table-hover table-sm" id="tableMapping">
                                            <thead>
                                                <tr>
                                                    <th class="text-center align-middle" rowspan="2" width="5%">No.</th>
                                                    <th class="text-center align-middle" rowspan="2">Menu Aplikasi</th>
                                                    <th class="text-center align-middle" rowspan="2">Group</th>
                                                    <th class="text-center align-middle" colspan="3">Jenis Aksi</th>
                                                    <th class="text-center align-middle" rowspan="2">Opsi</th>
                                                </tr>
                                                <tr>
                                                    <th class="text-center align-middle">Create</th>
                                                    <th class="text-center align-middle">Update</th>
                                                    <th class="text-center align-middle">Delete</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $no = 1;
                                                while ($dt = $qMapping->fetch_object()) {
                                                $check = '<i class="fas fa-check text-success"></i>';
                                                $ban = '<i class="fas fa-ban text-danger"></i>';
                                                $create = $dt->create == 1 ? $check : $ban;
                                                $update = $dt->update == 1 ? $check : $ban;
                                                $delete = $dt->delete == 1 ? $check : $ban;
                                                echo '
                                                <tr>
                                                    <td class="text-center">'.$no++.'</td>
                                                    <td>'.$dt->nama.'</td>
                                                    <td>'.$dt->nama_group.'</td>
                                                    <td class="text-center">'.$create.'</td>
                                                    <td class="text-center">'.$update.'</td>
                                                    <td class="text-center">'.$delete.'</td>
                                                    <td class="text-center">
                                                        <button type="button" data-id="'.$dt->id.'" onclick="hapus(this)" class="btn btn-sm btn-danger mb-1">
                                                            <i class="fas fa-trash"></i> Hapus
                                                        </button>
                                                    </td>
                                                </tr>
                                                ';
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
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
        $("#id_group_aplikasi").on('select2:select', function (e) {
            let id = this.value
            $('#group').val($(this).find(":selected").data("group"))
            if(id != ''){
                rowNext(id);
            }else{
                $('#subGroup').html("");
            }
        });

        function rowNext(id) {
            $.ajax({
                type: 'GET',
                data: {
                    id:id
                },
                url: 'subGroup.php',
                success: function (res) {
                    <?php if(!isset($_SESSION['V1c1T2NHTjNQVDA9_notif_status'])){ ?>
                        Swal.close();
                    <?php }?>
                    $('#subGroup').html(res);
                },
            });
        }
        
        $("#addMapping").submit(function(e) {
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

            if($("#divChkMenu input:checkbox:checked").length < 1){
                $(".chkMenu").addClass("is-invalid")
                $("#err_chk_menu").show()
            }else{
                $(".chkMenu").removeClass("is-invalid")
                $("#err_chk_menu").hide()
            }
            if(valid && $("#divChkMenu input:checkbox:checked").length > 0 ){
               validasiForm();
            }
            // else if(valid && $('#group').val() == '0') {
            //     validasiForm();
            // }
        });

        function validasiForm(){
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
                    data = $("#addMapping").serializeArray();
                    data.push({ name: "slug", value: '<?php echo $slug ?>' });
                    // console.log(data)
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
                            // Swal.close()
                            // console.log(response)
                            window.location = ""
                        }
                    });
                }
            })
        }

        function hapus(e){
            let id = e.getAttribute("data-id")
            Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Anda akan menghapus data ini!",
            icon: 'info',
            showCancelButton: true,
            cancelButtonText:'Tidak',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "proses_hapus.php",
                        data: {
                            id:id,
                            slug:'<?php echo $slug ?>'
                        },
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
                            window.location = ""
                        }
                    });
                }
            })
        };

        
    $("#tableMapping").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "pageLength": 10,
        "buttons": [
            {
                extend: "excel",
                exportOptions: {
                    columns: ':visible'
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
    }).buttons().container().appendTo('#tableMapping_wrapper .col-md-6:eq(0)');
    </script>
</body>
</html>