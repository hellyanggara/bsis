<?php
$codeSource = 'bsis';
$httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
$documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
require $documentRoot.'config/connection.config.php';
require $documentRoot.'config/utility.config.php';

$slug = $_POST['slug'];
$id_pegawai = insertSingleQuote(@$_POST['id_pegawai']);
$id_primary = insertSingleQuote(@$_POST['id_primary']);

switch ($slug) {
    case 'mapping_user':
        $table = "bsis_mapping_user";
        $primary = 'id_jenis_user';
        $jmlcek = '0';
        break;
    case 'bagian_user':
        $table = "bsis_mapping_bagian";
        $primary = 'kd_bagian';
        $jmlcek = $connection->query("SELECT * FROM bsis_mapping_bagian WHERE id_pegawai = '$id_pegawai' AND kd_bagian = '$id_primary'")->num_rows;
        break;
}

if($jmlcek == 0){
    $sql = "INSERT INTO $table (`id_pegawai`, `$primary`) VALUES ('$id_pegawai', '$id_primary')";
    $tambah = $connection->query($sql);
}else{
    goto sukses;
}


if($tambah){
    sukses:
    $data = array(
        'status' => 'sukses',
        'pesan' => 'Mapping User berhasil ditambahkan',
        // 'data' => $sql
    );
    $_SESSION['V1c1T2NHTjNQVDA9_notif_status'] = 'Sukses';
    $_SESSION['V1c1T2NHTjNQVDA9_notif_message'] = 'Mapping User berhasil ditambahkan';
}else{
    $data = array(
        'status' => 'Gagal',
        'pesan' => 'Mapping User gagal ditambahkan',
        'data' => $sql
    );
    $_SESSION['V1c1T2NHTjNQVDA9_notif_status'] = 'Gagal';
    $_SESSION['V1c1T2NHTjNQVDA9_notif_message'] = 'Mapping User gagal ditambahkan';
}

// $data = array(
//     'status' => 'gagal',
//     'pesan' => 'Ada data yang kosong',
//     'data' => $sql
// );
echo json_encode($data);