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
        $colPrimary = 'id_jenis_user';
        break;
    case 'akses_user':
        $table = 'bsis_mapping_hak_akses_user';
        $colPrimary = 'id_pegawai';
        break;
}
$id_primary = $_POST['id_primary'];
$menu_array = @$_POST['menu'];
$create_array = isset($_POST['create']) ? $_POST['create'] : [];
$update_array = isset($_POST['update']) ? $_POST['update'] : [];
$delete_array = isset($_POST['delete']) ? $_POST['delete'] : [];
$group = $_POST['group'];
$subGroup_value = $_POST['sub_group'];
$subGroup = $subGroup_value == "" ? "NULL" : "'$subGroup_value'";
// if($menu_array != null){
    $strQ = implode ("','", $menu_array);
// }
// if($group == 0){
//     $strQ = $_POST['id_group_aplikasi'];
// }

$connection->query("STRAT TRANSACTION;");
if($menu_array != null){
    if($group <> 0){
        $andGroup = "AND `group` <> 0";
    }else{
        $andGroup = "AND `group` = 0";
    }
    $sqlDel = "DELETE FROM $table WHERE id_menu_aplikasi IN ('$strQ') AND $colPrimary = '$id_primary' $andGroup";
    $delDulu = $connection->query($sqlDel);
    $jumlahMenu = count($menu_array);
    for ($x = 0; $x < $jumlahMenu; $x++) {
        $menu = $menu_array[$x];
        $create = $create_array[$x];
        $update = $update_array[$x];
        $delete = $delete_array[$x];
        $sql = "INSERT INTO $table (`$colPrimary`,`id_menu_aplikasi`, `group`, `sub_group`,`create`,`update`,`delete`) VALUES ('$id_primary', '$menu', '$group', $subGroup, '$create','$update','$delete')";
        $tambah = $connection->query($sql);
    }
}else{
    $sqlDel = "DELETE FROM $table WHERE id_menu_aplikasi IN ('$strQ') AND $colPrimary = '$id_primary' AND `group` = 0";
    $delDulu = $connection->query($sqlDel);
    $sql = "INSERT INTO $table (`$colPrimary`,`id_menu_aplikasi`, `group`,`create`,`update`,`delete`) VALUES ('$id_primary', '$strQ', '$group',1,1,1)";
        $tambah = $connection->query($sql);
}

if($tambah){
    $res = $connection->query("COMMIT");
     if ($res) {
        $data = array(
            'status' => 'sukses',
            'pesan' => 'Mapping berhasil ditambahkan',
            'data' => $sqlDel
        );
        $_SESSION['V1c1T2NHTjNQVDA9_notif_status'] = 'Sukses';
        $_SESSION['V1c1T2NHTjNQVDA9_notif_message'] = 'Mapping berhasil ditambahkan';
    }else{
        $res = $connection->query("ROLLBACK");
        $data = array(
            'status' => 'Gagal',
            'pesan' => 'Mapping gagal ditambahkan, gagal commit',
            'data' => $sqlDel
        );
        $_SESSION['V1c1T2NHTjNQVDA9_notif_status'] = 'Gagal';
        $_SESSION['V1c1T2NHTjNQVDA9_notif_message'] = 'Mapping gagal ditambahkan';
    }
}else{
    $data = array(
        'status' => 'Gagal',
        'pesan' => 'Mapping gagal ditambahkan, Query Utama gagal',
        'data' => $sqlDel
    );
    $_SESSION['V1c1T2NHTjNQVDA9_notif_status'] = 'Gagal';
    $_SESSION['V1c1T2NHTjNQVDA9_notif_message'] = 'Menu gagal ditambahkan';
}

// $data = array(
//     'status' => 'sukses',
//     'pesan' => 'Ada data yang kosong',
//     'data' => $sql
// );
echo json_encode($data);