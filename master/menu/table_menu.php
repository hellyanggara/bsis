<?php
    $codeSource = 'bsis';
    $httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
    $documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
    require $documentRoot.'config/connection.config.php';
    require $documentRoot.'config/utility.config.php';
    signInCheck();
    include ('../master.php');
    $q = new MASTER();

    $trash = $_GET['trash'];
    $table = 'bsis_menu_aplikasi';
    $tableJoin = 'bsis_group_menu_aplikasi';
    $select = $table.'.*,'.$tableJoin.'.nama as namaGroup,'.$tableJoin.'.order';
    $order = "ORDER BY $tableJoin.`order`,$table.id";
    $query = $q->get_list_menu($table, $trash, 'Left Join', $tableJoin,'id', 'id_group', $select,'',$order);
?>
<table class="table table-bordered table-hover table-sm" id="tableDimensiMutu">
    <thead>
        <tr>
            <th class="text-center align-middle" width="5%">No.</th>
            <th class="text-center align-middle">Nama</th>
            <th class="text-center align-middle">Slug</th>
            <th class="text-center align-middle">Table</th>
            <th class="text-center align-middle">Icon</th>
            <th class="text-center align-middle">Menu Aktif</th>
            <th class="text-center align-middle">Direktori</th>
            <th class="text-center align-middle">Group</th>
            <th class="text-center align-middle">File</th>
            <th class="text-center align-middle">Opsi</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $noMenu = 1;
    while($dt = $query->fetch_object()){
    if($trash != ''){
        $button = '
            <button type="button" data-id="'.$dt->id.'" class="btn btn-sm btn-secondary" onclick="restore(this)">
                <i class="fas fa-trash-restore"></i> Restore
            </button>';
    }else{
        $button = '
            <a href="edit.php?id='.$dt->id.'" class="btn btn-sm btn-warning mb-1">
                <i class="fas fa-pencil"></i> Ubah
            </a>
            <button type="button" data-id="'.$dt->id.'" class="btn btn-sm btn-danger mb-1" onclick="hapus(this)">
                <i class="fas fa-trash"></i> Hapus
            </button>';
    }
    if($dt->file != ''){
        $image = '
            <a href="'.$httpHost.'master/menu/img/'.$dt->file.'" target="_blank" class="btn btn-primary btn-sm">
                <i class="fas fa-paperclip"></i>
            </a>';
    }else{
        $image = '';
    }
    echo '
    <tr>
        <td class="text-center">'.$noMenu++.'</td>
        <td>'.$dt->nama.'</td>
        <td>'.$dt->slug.'</td>
        <td>'.$dt->table.'</td>
        <td>'.$dt->icon.'</td>
        <td>'.$dt->active_menu.'</td>
        <td>'.$dt->direktori.'</td>
        <td>'.$dt->namaGroup.'</td>
        <td>'.$image.'</td>
        <td class="text-center">'.$button.'</td>
    </tr>
    ';
    }
    ?>
    </tbody>
</table>
<script>
    $("#tableDimensiMutu").DataTable({
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
    }).buttons().container().appendTo('#tableDimensiMutu_wrapper .col-md-6:eq(0)');
    
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