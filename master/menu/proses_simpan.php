<?php
$codeSource = 'bsis';
$httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
$documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
require $documentRoot.'config/connection.config.php';
require $documentRoot.'config/utility.config.php';

$nama = insertSingleQuote(@$_POST['nama']);
$slug = insertSingleQuote(@$_POST['slug']);
$table = insertSingleQuote(@$_POST['table']);
$icon = insertSingleQuote(@$_POST['icon']);
$active_menu = insertSingleQuote(@$_POST['active_menu']);
$direktori = insertSingleQuote(@$_POST['direktori']);
$id_group = insertSingleQuote(@$_POST['id_group']);

$idpegawai = $_SESSION['V1c1T2NHTjNQVDA9_id_pegawai'];
list($created_at) = mysqli_fetch_array(mysqli_query($connection, "SELECT NOW()"));
$pc_create = gethostbyaddr($_SERVER['REMOTE_ADDR']);
if (!empty($_SERVER['REMOTE_ADDR'])) {
    $ip_create = $_SERVER['REMOTE_ADDR'];
} else {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip_create = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip_create = $_SERVER['HTTP_X_FORWARDED_FOR'];
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
}else{
    $file = "NULL";
}
$sql = "INSERT INTO `bsis_menu_aplikasi` (nama, slug, `table`, icon, active_menu, direktori, `id_group`, `file`, id_create, created_at, pc_create, ip_create) VALUES ('$nama', '$slug', '$table', '$icon', '$active_menu', '$direktori', '$id_group', $file,'$idpegawai','$created_at','$pc_create','$ip_create')";

$tambah = $connection->query($sql);

if($tambah){
    if($_FILES['file']['name'] != ''){
        move_uploaded_file($tmp, 'img/' . $fileLampiran);
    }
    $data = array(
        'status' => 'sukses',
        'pesan' => 'Menu berhasil ditambahkan',
        'data' => $sql
    );
    $_SESSION['V1c1T2NHTjNQVDA9_notif_status'] = 'Sukses';
    $_SESSION['V1c1T2NHTjNQVDA9_notif_message'] = 'Menu berhasil ditambahkan';
}else{
    $data = array(
        'status' => 'Gagal',
        'pesan' => 'Menu gagal ditambahkan',
        'data' => $sql
    );
    $_SESSION['V1c1T2NHTjNQVDA9_notif_status'] = 'Gagal';
    $_SESSION['V1c1T2NHTjNQVDA9_notif_message'] = 'Menu gagal ditambahkan';
}

// $data = array(
//     'status' => 'sukses',
//     'pesan' => 'Ada data yang kosong',
//     'data' => $_FILES['file']
// );
echo json_encode($data);