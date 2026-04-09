<?php
    $codeSource = 'bsis';
    $httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
    $documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
    require $documentRoot.'config/connection.config.php';
    require $documentRoot.'config/utility.config.php';
    signInCheck();
    $slug = $_GET['slug'];
    include ('user.php');
    $q = new USER();

    $table = 'bsis_menu_aplikasi';
    $jmlCek = $q->cek_table($table, '', $slug);
    if($jmlCek < 1){
        header('Location: '.$httpHost.'404.php');
        die();
    }else{
        $dtM = $q->get_data($table, $slug);
        $cekAkses = cek_akses($_SESSION['V1c1T2NHTjNQVDA9_level'],$_SESSION['V1c1T2NHTjNQVDA9_id_pegawai'],$dtM->id,'0');
    }
    switch ($slug) {
        case 'mapping_user':
            $query = $q->get_data_user_jenis_akses();
            $addcol = "Jenis User";
            break;
    }
?>
<table class="table table-bordered table-hover table-sm" id="tableMappingUser">
    <thead>
        <tr>
            <th class="text-center align-middle" width="5%">No.</th>
            <th class="text-center align-middle">Nama Pegawai</th>
            <th class="text-center align-middle"><?php echo $addcol ?></th>
            <th class="text-center align-middle">Opsi</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $noMenu = 1;
    while($dt = $query->fetch_object()){
        
    if($slug == "mapping_user"){
        if($cekAkses[0]->update == '1'){
            $btnEdit = '
                <a href="edit.php?id='.$dt->primary_id.'&ref='.$slug.'" class="btn btn-sm btn-warning mb-1">
                    <i class="fas fa-pencil"></i> Ubah
                </a>';
        }else{
            $btnEdit = '';
        }
    }else{
        $btnEdit = '';
    }
    if($cekAkses[0]->delete == '1'){
        $btnDelete = '
        <button type="button" data-id="'.$dt->primary_id.'" class="btn btn-sm btn-danger mb-1" onclick="hapus(this)">
            <i class="fas fa-trash"></i> Hapus
        </button>';
    }else{
        $btnDelete = '';
    }
    echo '
    <tr>
        <td class="text-center">'.$noMenu++.'</td>
        <td>'.$dt->namapegawai.'</td>
        <td>'.$dt->addcol_value.'</td>
        <td class="text-center">
            '.$btnEdit.'
            '.$btnDelete.'
        </td>
    </tr>
    ';
    }
    ?>
    </tbody>
</table>
<script>
    $("#tableMappingUser").DataTable({
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
    }).buttons().container().appendTo('#tableMappingUser_wrapper .col-md-6:eq(0)');
    
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
                // console.log(id)
                $.ajax({
                    type: "POST",
                    url: "proses_hapus.php",
                    data: {
                        id:id,
                        slug : '<?php echo $slug ?>',
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

    function restore(e){
        let id = e.getAttribute("data-id")
        Swal.fire({
        title: 'Apakah anda yakin?',
        text: "Anda akan mengembalikan data ini!",
        icon: 'info',
        showCancelButton: true,
        cancelButtonText:'Tidak',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, kembalikan!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "proses_restore.php",
                    data: {
                        id:id
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
</script>