<?php
$codeSource = 'bsis';
$httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
$documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
require $documentRoot.'config/connection.config.php';
require $documentRoot.'config/utility.config.php';

$slug = $_POST['slug'];
require 'super.php';
$q = new SUPER();

$table = 'bsis_menu_aplikasi';
$jmlCek = $q->cek_table($table, '', $slug);
if($jmlCek < 1){
    header('Location: '.$httpHost.'404.php');
    die();
}else{
    $dt = $q->get_data($table, $slug);
}

$id = $_POST['id'];

$idpegawai = $_SESSION['V1c1T2NHTjNQVDA9_id_pegawai'];
list($deleted_at) = mysqli_fetch_array(mysqli_query($connection, "SELECT NOW()"));
$pc_delete = gethostbyaddr($_SERVER['REMOTE_ADDR']);
if (!empty($_SERVER['REMOTE_ADDR'])) {
    $ip_delete = $_SERVER['REMOTE_ADDR'];
} else {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip_delete = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip_delete = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
}

$sql = "UPDATE $dt->table SET id_delete = '$idpegawai', deleted_at = '$deleted_at', pc_delete = '$pc_delete', ip_delete = '$ip_delete' WHERE id = '$id'";

$hapus = $connection->query($sql);

if($hapus){
    $data = array(
        'status' => 'sukses',
        'pesan' => $dt->nama.' berhasil dihapus',
        'data' => $sql
    );
    $_SESSION['V1c1T2NHTjNQVDA9_notif_status'] = 'Sukses';
    $_SESSION['V1c1T2NHTjNQVDA9_notif_message'] = $dt->nama.' berhasil dihapus';
}else{
    $data = array(
        'status' => 'Gagal',
        'pesan' => $dt->nama.' gagal dihapus',
        'data' => $sql
    );
    $_SESSION['V1c1T2NHTjNQVDA9_notif_status'] = 'Gagal';
    $_SESSION['V1c1T2NHTjNQVDA9_notif_message'] = $dt->nama.' gagal dihapus';
}

// $data = array(
//     'status' => 'gagal',
//     'pesan' => 'Ada data yang kosong'
// );
echo json_encode($data);