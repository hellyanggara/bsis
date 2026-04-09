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
    $addTable = $q->get_additional_column($dt->table);
}
// $nama = insertSingleQuote(@$_POST['nama']);

foreach(array_slice($_POST,1) as $key => $value){
    if(!empty($value)){
        $keys[] = $key;
        $values[] = $value;
    }
}
// $keteranganValue = insertSingleQuote(@$_POST['keterangan']);
// $keterangan = $_POST['keterangan'] !== "" ? "'$keteranganValue'" : "NULL";
$idpegawai = $_SESSION['V1c1T2NHTjNQVDA9_id_pegawai'];
list($created_at) = mysqli_fetch_array(mysqli_query($connection, "SELECT NOW()"));
$pc_create = gethostbyaddr($_SERVER['REMOTE_ADDR']);
if (!empty($_SERVER['REMOTE_ADDR'])) {
    $ip_create = $_SERVER['REMOTE_ADDR'];
}else{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip_create = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip_create = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
}
array_push($keys,"id_create","created_at","pc_create","ip_create");
array_push($values,$idpegawai,$created_at,$pc_create,$ip_create);

// // $sql = "INSERT INTO $dt->table (`nama`, `keterangan`, id_create, created_at, pc_create, ip_create) VALUES ('$nama', $keterangan, '$idpegawai','$created_at','$pc_create','$ip_create')";
$sql = "INSERT INTO `$dt->table` (`".implode("`,`", $keys)."`) VALUES('".implode("','", $values)."')";
$tambah = $connection->query($sql);

if($tambah){
    $data = array(
        'status' => 'sukses',
        'pesan' => $dt->nama.' berhasil ditambahkan',
        'data' => $sql
    );
    $_SESSION['V1c1T2NHTjNQVDA9_notif_status'] = 'Sukses';
    $_SESSION['V1c1T2NHTjNQVDA9_notif_message'] = $dt->nama.' berhasil ditambahkan';
}else{
    $data = array(
        'status' => 'Gagal',
        'pesan' => $dt->nama.' gagal ditambahkan',
        'data' => $sql
    );
    $_SESSION['V1c1T2NHTjNQVDA9_notif_status'] = 'Gagal';
    $_SESSION['V1c1T2NHTjNQVDA9_notif_message'] = $dt->nama.' gagal ditambahkan';
}

// $data = array(
//     'status' => 'gagal',
//     'pesan' => 'Ada data yang kosong'
// );
echo json_encode($data);