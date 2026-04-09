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
foreach(array_slice($_POST,2) as $key => $value){
    if(empty($value)){
        $value = "NULL";
    }else{
        $value = "'$value'";
    }
    $dataUpdate[] = "`".$key."` = $value";
}

$idpegawai = $_SESSION['V1c1T2NHTjNQVDA9_id_pegawai'];
list($updated_at) = mysqli_fetch_array(mysqli_query($connection, "SELECT NOW()"));
$pc_update = gethostbyaddr($_SERVER['REMOTE_ADDR']);
if (!empty($_SERVER['REMOTE_ADDR'])) {
    $ip_update = $_SERVER['REMOTE_ADDR'];
} else {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip_update = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip_update = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
}
array_push($dataUpdate,"`id_update`= '".$idpegawai."'","`updated_at`= '".$updated_at."'","`pc_update`= '".$pc_update."'","`ip_update`= '".$ip_update."'");

$sql = "UPDATE $dt->table SET ".implode(",", $dataUpdate)." WHERE id = '$id'";

$ubah = $connection->query($sql);

if($ubah){
    $data = array(
        'status' => 'sukses',
        'pesan' => $dt->nama.' berhasil diubah',
        'data' => $sql
    );
    $_SESSION['V1c1T2NHTjNQVDA9_notif_status'] = 'Sukses';
    $_SESSION['V1c1T2NHTjNQVDA9_notif_message'] = $dt->nama.' berhasil diubah';
}else{
    $data = array(
        'status' => 'Gagal',
        'pesan' => $dt->nama.' gagal diubah',
        'data' => $sql
    );
    $_SESSION['V1c1T2NHTjNQVDA9_notif_status'] = 'Gagal';
    $_SESSION['V1c1T2NHTjNQVDA9_notif_message'] = $dt->nama.' gagal diubah';
}

// $data = array(
//     'status' => 'gagal',
//     'pesan' => 'Ada data yang kosong',
//     'data' => $sql
// );
echo json_encode($data);