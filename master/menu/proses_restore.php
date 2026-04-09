<?php
$codeSource = 'bsis';
$httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
$documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
require $documentRoot.'config/connection.config.php';
require $documentRoot.'config/utility.config.php';

$id = $_POST['id'];

$sql = "UPDATE `bsis_menu_aplikasi` SET id_delete = NULL, deleted_at = NULL, pc_delete = NULL, ip_delete = NULL WHERE id = '$id'";

$hapus = $connection->query($sql);

if($hapus){
    $data = array(
        'status' => 'sukses',
        'pesan' => 'Menu berhasil dikembalikan',
        'data' => $sql
    );
    $_SESSION['V1c1T2NHTjNQVDA9_notif_status'] = 'Sukses';
    $_SESSION['V1c1T2NHTjNQVDA9_notif_message'] = 'Menu berhasil dikembalikan';
}else{
    $data = array(
        'status' => 'Gagal',
        'pesan' => 'Menu gagal dikembalikan',
        'data' => $sql
    );
    $_SESSION['V1c1T2NHTjNQVDA9_notif_status'] = 'Gagal';
    $_SESSION['V1c1T2NHTjNQVDA9_notif_message'] = 'Menu gagal dikembalikan';
}

// $data = array(
//     'status' => 'gagal',
//     'pesan' => 'Ada data yang kosong'
// );
echo json_encode($data);