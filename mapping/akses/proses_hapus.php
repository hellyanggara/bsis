<?php
$codeSource = 'bsis';
$httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
$documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
require $documentRoot.'config/connection.config.php';
require $documentRoot.'config/utility.config.php';

$slug = $_POST['slug'];
switch ($slug) {
    case 'akses_jenis_user':
        $table = 'bsis_mapping_hak_akses_jenis_user';
        break;
    case 'akses_user':
        $table = 'bsis_mapping_hak_akses_user';
        break;
}
$id = $_POST['id'];
$connection->query("STRAT TRANSACTION;");
$sql = "DELETE FROM $table WHERE id = '$id'";
$delete = $connection->query($sql);
if($delete){
    $res = $connection->query("COMMIT");
     if ($res) {
        $data = array(
            'status' => 'sukses',
            'pesan' => 'Mapping berhasil dihapus',
            'data' => $sql
        );
        $_SESSION['V1c1T2NHTjNQVDA9_notif_status'] = 'Sukses';
        $_SESSION['V1c1T2NHTjNQVDA9_notif_message'] = 'Mapping berhasil dihapus';
    }else{
        $res = $connection->query("ROLLBACK");
        $data = array(
            'status' => 'Gagal',
            'pesan' => 'Mapping gagal dihapus, gagal commit',
            'data' => $sql
        );
        $_SESSION['V1c1T2NHTjNQVDA9_notif_status'] = 'Gagal';
        $_SESSION['V1c1T2NHTjNQVDA9_notif_message'] = 'Mapping gagal dihapus';
    }
}else{
    $data = array(
        'status' => 'Gagal',
        'pesan' => 'Mapping gagal dihapus, Query Utama gagal',
        'data' => $sql
    );
    $_SESSION['V1c1T2NHTjNQVDA9_notif_status'] = 'Gagal';
    $_SESSION['V1c1T2NHTjNQVDA9_notif_message'] = 'Menu gagal dihapus';
}

// $data = array(
//     'status' => 'gagal',
//     'pesan' => 'Ada data yang kosong',
//     'data' => $sql
// );
echo json_encode($data);