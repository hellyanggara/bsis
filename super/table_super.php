<?php
    $codeSource = 'bsis';
    $httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
    $documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
    require $documentRoot.'config/connection.config.php';
    require $documentRoot.'config/utility.config.php';
    signInCheck();
    
    $slug = $_GET['slug'];
    require 'super.php';
    $q = new SUPER();

    $table = 'bsis_menu_aplikasi';
    $jmlCek = $q->cek_table($table, '', $slug);
    if($jmlCek < 1){
        header('Location: '.$httpHost.'404.php');
        die();
    }else{
        $dtM = $q->get_data($table, $slug);
    }

    $trash = $_GET['trash'];
    $table = $dtM->table;
    switch ($table) {
        case 'bsis_jenis_user':
            $select = "
                    id,
                    nama,
                    keterangan,
                    CASE
                            
                            WHEN is_admin = 1 THEN
                            'Ya' ELSE 'Tidak' 
                        END 'is_admin'";
            $join = FALSE;
            break;
        default:
            $select = FALSE;
            $join = FALSE;
            break;
    }
    $query = $q->get_list($table, $trash, $select, $join);
    $addTable = $q->get_additional_column($table);
?>
<table class="table table-bordered table-hover table-sm" id="tableDimensiMutu">
    <thead>
        <tr>
            <th class="text-center align-middle" width="5%">No.</th>
            <th class="text-center align-middle">Nama</th>
            <?php
            foreach ($addTable as $key => $column) {
                echo '<th class="text-center align-middle">'.str_replace('_', ' ',ucwords($column['nama'])).'</th>';
            }
            ?>
            <th class="text-center align-middle">Keterangan</th>
            <th class="text-center align-middle">Opsi</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $noManrisk = 1;
    while($dt = $query->fetch_object()){
    if($trash != ''){
        $button = '
            <button type="button" data-id="'.$dt->id.'" data-slug="'.$dtM->slug.'" onclick="restore(this)" class="btn btn-sm btn-secondary">
                <i class="fas fa-trash-restore"></i> Restore
            </button>';
    }else{
        $button = '
            <a href="edit.php?id='.$dt->id.'&ref='.$dtM->slug.'" class="btn btn-sm btn-warning mb-1">
                <i class="fas fa-pencil"></i> Ubah
            </a>
            <button type="button" data-id="'.$dt->id.'" data-slug="'.$dtM->slug.'" onclick="hapus(this)" class="btn btn-sm btn-danger mb-1">
                <i class="fas fa-trash"></i> Hapus
            </button>';
    }
    echo '
    <tr>
        <td class="text-center">'.$noManrisk++.'</td>
        <td>'.$dt->nama.'</td>';
        foreach ($addTable as $key => $column) {
            $columnName = $column['nama'];
            if($column['jenis'] == 'colorpicker'){
                $bg = 'style="background-color:'.$dt->$columnName.'"';
            }else{
                $bg = '';
            }
            echo '<td class="text-center" '.$bg.'>'.nl2br($dt->$columnName).'</td>';
        }
        echo '
        <td>'.nl2br($dt->keterangan).'</td>
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
        let slug = e.getAttribute("data-slug")
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
                        slug:slug
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
        let slug = e.getAttribute("data-slug")
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
                        id:id,
                        slug:slug
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