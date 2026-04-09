<?php
$codeSource = 'bsis';
$httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
$documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
require $documentRoot.'config/connection.config.php';
require $documentRoot.'config/utility.config.php';
signInCheck();
require_once('operasi.php');
$q = new OPERASI();

$listDokter = $q->getDokter();
echo "
    <option></option>
    <option value='0000000000'>Anestesi Lokal</option>";
foreach ($listDokter as $d) {
    echo "
    <option value=\"{$d->KodeDokter}\">{$d->NamaDokter}</option>";
}
?>