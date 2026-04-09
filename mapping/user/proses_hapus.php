<?php
$codeSource = 'bsis';
$httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
$documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
require $documentRoot.'config/connection.config.php';
require $documentRoot.'config/utility.config.php';

$slug = $_POST['slug'];
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

switch ($slug) {
    case 'mapping_user':
        $table = "bsis_mapping_user";
        $primary = 'id_pegawai';
        break;
    case 'bagian_user':
        $table = "bsis_mapping_bagian";
        $primary = 'idmapping';
        break;
}
$sql = "DELETE FROM $table WHERE $primary = '$id'";

$hapus = $connection->query($sql);

if($hapus){
    $data = array(
        'status' => 'sukses',
        'pesan' => 'Mapping berhasil dihapus',
        'data' => $sql
    );
    $_SESSION['V1c1T2NHTjNQVDA9_notif_status'] = 'Sukses';
    $_SESSION['V1c1T2NHTjNQVDA9_notif_message'] = 'Mapping berhasil dihapus';
}else{
    $data = array(
        'status' => 'Gagal',
        'pesan' => 'Mapping gagal dihapus',
        'data' => $sql
    );
    $_SESSION['V1c1T2NHTjNQVDA9_notif_status'] = 'Gagal';
    $_SESSION['V1c1T2NHTjNQVDA9_notif_message'] = 'Mapping gagal dihapus';
}

// $data = array(
//     'status' => 'gagal',
//     'pesan' => 'Ada data yang kosong'
// );
echo json_encode($data);