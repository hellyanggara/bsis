<?php
$codeSource = 'bsis';
$httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
$documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
require $documentRoot.'config/connection.config.php';
require $documentRoot.'config/utility.config.php';

$slug = $_POST['slug'];
require $documentRoot.'master/master.php';
$q = new MASTER();

$table = 'bsis_menu_aplikasi';
$jmlCek = $q->cek_table($table, '', $slug);
if($jmlCek < 1){
    header('Location: '.$httpHost.'404.php');
    die();
}else{
    $dt = $q->get_data($table, $slug);
}

$id = $_POST['id'];

$sql = "UPDATE $dt->table SET id_delete = NULL, deleted_at = NULL, pc_delete = NULL, ip_delete = NULL WHERE id = '$id'";

$hapus = $connection->query($sql);

if($hapus){
    $data = array(
        'status' => 'sukses',
        'pesan' => $dt->nama.' berhasil dikembalikan',
        'data' => $sql
    );
    $_SESSION['V1c1T2NHTjNQVDA9_notif_status'] = 'Sukses';
    $_SESSION['V1c1T2NHTjNQVDA9_notif_message'] = $dt->nama.' berhasil dikembalikan';
}else{
    $data = array(
        'status' => 'Gagal',
        'pesan' => $dt->nama.' gagal dikembalikan',
        'data' => $sql
    );
    $_SESSION['V1c1T2NHTjNQVDA9_notif_status'] = 'Gagal';
    $_SESSION['V1c1T2NHTjNQVDA9_notif_message'] = $dt->nama.' gagal dikembalikan';
}

// $data = array(
//     'status' => 'gagal',
//     'pesan' => 'Ada data yang kosong'
// );
echo json_encode($data);