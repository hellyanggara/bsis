<?php
$codeSource = 'bsis';
$httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
$documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
require $documentRoot.'config/connection.config.php';
require $documentRoot.'config/utility.config.php';

$id_pegawai = insertSingleQuote(@$_POST['id_pegawai']);
$id_jenis_user = insertSingleQuote(@$_POST['id_jenis_user']);

$sql = "UPDATE `bsis_mapping_user` SET `id_jenis_user` = '$id_jenis_user' WHERE id_pegawai = '$id_pegawai'";

$ubah = $connection->query($sql);

if($ubah){
    $data = array(
        'status' => 'sukses',
        'pesan' => 'Mapping User berhasil diubah',
        'data' => $sql
    );
    $_SESSION['V1c1T2NHTjNQVDA9_notif_status'] = 'Sukses';
    $_SESSION['V1c1T2NHTjNQVDA9_notif_message'] = 'Mapping User berhasil diubah';
}else{
    $data = array(
        'status' => 'Gagal',
        'pesan' => 'Mapping User gagal diubah',
        'data' => $sql
    );
    $_SESSION['V1c1T2NHTjNQVDA9_notif_status'] = 'Gagal';
    $_SESSION['V1c1T2NHTjNQVDA9_notif_message'] = 'Mapping User gagal diubah';
}

// $data = array(
//     'status' => 'gagal',
//     'pesan' => 'Ada data yang kosong'
// );
echo json_encode($data);