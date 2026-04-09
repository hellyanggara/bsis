<?php
$codeSource = 'bsis';
$httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
$documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
require $documentRoot.'config/connection.config.php';
require $documentRoot.'config/utility.config.php';

$id = $_POST['id'];
$nama = insertSingleQuote(@$_POST['nama']);
$slug = insertSingleQuote(@$_POST['slug']);
$table = insertSingleQuote(@$_POST['table']);
$icon = insertSingleQuote(@$_POST['icon']);
$active_menu = insertSingleQuote(@$_POST['active_menu']);
$direktori = insertSingleQuote(@$_POST['direktori']);
$id_group = insertSingleQuote(@$_POST['id_group']);

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

if($_FILES['file']['name'] != ''){
    $namafile = $_FILES['file']['name'];
    $tmp = $_FILES['file']['tmp_name'];
    $x = explode('.', $namafile);
    $ekstensi = strtolower(end($x));
    $milliseconds = round(microtime(true) * 1000);
    $fileLampiran = $milliseconds.'.'.$ekstensi;
    $file = "'$fileLampiran'";
    $sql = "UPDATE `bsis_menu_aplikasi` SET `nama` = '$nama', `icon` = '$icon', `slug` = '$slug', `table` = '$table', `active_menu` = '$active_menu', `direktori` = '$direktori', `id_group` = '$id_group', `file` = $file, id_update = '$idpegawai', updated_at = '$updated_at', pc_update = '$pc_update', ip_update = '$ip_update' WHERE id = '$id'";
}else{
    $sql = "UPDATE `bsis_menu_aplikasi` SET `nama` = '$nama', `icon` = '$icon', `slug` = '$slug', `table` = '$table', `active_menu` = '$active_menu', `direktori` = '$direktori', `id_group` = '$id_group', id_update = '$idpegawai', updated_at = '$updated_at', pc_update = '$pc_update', ip_update = '$ip_update' WHERE id = '$id'";
}

$ubah = $connection->query($sql);

if($ubah){
    if($_FILES['file']['name'] != ''){
        move_uploaded_file($tmp, 'img/' . $fileLampiran);
    }
    $data = array(
        'status' => 'sukses',
        'pesan' => 'Menu berhasil diubah',
        'data' => $sql
    );
    $_SESSION['V1c1T2NHTjNQVDA9_notif_status'] = 'Sukses';
    $_SESSION['V1c1T2NHTjNQVDA9_notif_message'] = 'Menu berhasil diubah';
}else{
    $data = array(
        'status' => 'Gagal',
        'pesan' => 'Menu gagal diubah',
        'data' => $sql
    );
    $_SESSION['V1c1T2NHTjNQVDA9_notif_status'] = 'Gagal';
    $_SESSION['V1c1T2NHTjNQVDA9_notif_message'] = 'Menu gagal diubah';
}

// $data = array(
//     'status' => 'gagal',
//     'pesan' => 'Ada data yang kosong'
// );
echo json_encode($data);