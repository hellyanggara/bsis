<?php
    $codeSource = 'bsis';
    $httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
    $documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
    require $documentRoot.'config/connection.config.php';
    require $documentRoot.'config/utility.config.php';
    signInCheck();
    $slug = $_GET['slug'];

    require ('akses.php');
    $q = new AKSES();

    switch ($slug) {
        case 'akses_jenis_user':
            $table = 'bsis_jenis_user';
            $join = false;
            $tableJoin = false;
            $primaryId = false;
            $foreignId = false;
            $select = false;
            $whereNull = true;
            $group = false;
            break;
        case 'akses_user':
            $table = 'bsis_mapping_user';
            $join = 'INNER JOIN';
            $tableJoin = 'pegawai';
            $primaryId = 'idpegawai';
            $foreignId = 'id_pegawai';
            $select = 'pegawai.idpegawai as id,
            pegawai.namapegawai as nama';
            $whereNull = false;
            $group = 'id';
            break;
    }

    $query = $q->get_list($table,$join,$tableJoin,$primaryId, $foreignId,$select, $whereNull, false, $group);
    // echo $query;
?>
<table class="table table-bordered table-hover table-sm" id="tableMapping">
    <thead>
        <tr>
            <th class="text-center align-middle" width="5%">No.</th>
            <th>Nama</th>
            <th class="text-center align-middle">Opsi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        while ($dt = $query->fetch_object()) {
        echo'
        <tr>
            <td class="text-center">'.$no++.'</td>
            <td>'.$dt->nama.'</td>
            <td class="text-center">
                <a href="create.php?ref='.$_GET['slug'].'&id='.$dt->id.'" class="btn btn-sm btn-warning mb-1">
                    <i class="fas fa-not-equal"></i> Mapping
                </a>
            </td>
        </tr>
        ';
        }
        ?>
    </tbody>
</table>
<script>
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
    
    $(".batalLaporan").on("click", function(e){  
        alert('batal')
    })
</script>