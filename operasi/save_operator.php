<?php
$codeSource = 'bsis';
$httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
$documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
require $documentRoot.'config/connection.config.php';
require $documentRoot.'config/utility.config.php';
signInCheck();
require_once('operasi.php');
function parseDate($input) {
    $pecah = explode(',', $input);
    $tgl = isset($pecah[1]) ? trim($pecah[1]) : trim($pecah[0]);

    $formats = [
        'd/m/Y H:i:s',
        'd/m/Y H:i',
        'd/m/Y', // fallback kalau hanya tanggal saja
    ];

    foreach ($formats as $format) {
        $date = DateTime::createFromFormat($format, $tgl);
        if ($date) {
            return $date->format('Y-m-d H:i:s');
        }
    }

    return null;
}
try {
    $requiredFields = [
        'id',
        'id_dokter',
    ];
    foreach ($requiredFields as $field) {
        // if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
        if (!isset($_POST[$field]) || (is_array($_POST[$field]) ? empty(array_filter($_POST[$field])) : trim($_POST[$field]) === '')) {
            throw new Exception("Field '$field' wajib diisi.");
        }
    }
    $data = [];
    
    $data = [
        'id' => cleanInput($_POST['id']),
        'id_dokter' => isset($_POST['id_dokter']) ? cleanInput($_POST['id_dokter']) : NULL,
    ];

    $fx = new OPERASI();

    $result = $fx->simpanOperator($data);

    if ($result['status'] === 'success') {
        echo json_encode(["status" => "success", "message" => $result['message']]);
    } else {
        echo json_encode(["status" => "error", "message" => $result['message']]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>