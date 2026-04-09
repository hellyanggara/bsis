<?php
    @session_start();
    $codeSource = 'bsis';
    $httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
    $documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';

?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $_SESSION['V1c1T2NHTjNQVDA9_nama_app'] ?> | RSKD</title>
<link rel="icon" type="image/png" href="<?php echo $httpHost ?>vendor/dist/img/bsis.png">
<link rel="stylesheet" href="<?php echo $httpHost ?>vendor/fonts/source-sans-pro/source-sans-pro.css">
<link rel="stylesheet" href="<?php echo $httpHost ?>vendor/plugins/font-awesome-pro-5.12.0/css/all.min.css">
<link rel="stylesheet" href="<?php echo $httpHost ?>vendor/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="<?php echo $httpHost ?>vendor/dist/css/adminlte.min.css">
<link rel="stylesheet" href="<?php echo $httpHost ?>vendor/dist/css/custom.css">
<link rel="stylesheet" href="<?php echo $httpHost ?>vendor/plugins/sweetalert2/css/sweetalert2.min.css">
<link rel="stylesheet" href="<?php echo $httpHost ?>vendor/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css">
<script src="<?php echo $httpHost ?>vendor/plugins/jquery/jquery.min.js"></script>

<link rel="stylesheet" href="<?php echo $httpHost ?>vendor/dist/css/tender-card.css">
<link rel="stylesheet" href="<?php echo $httpHost ?>vendor/dist/css/line-wobble.css">
<link rel="stylesheet" href="<?php echo $httpHost ?>vendor/plugins/daterangepicker/daterangepicker.css">
<link rel="stylesheet" href="<?php echo $httpHost ?>vendor/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
<link rel="stylesheet" href="<?php echo $httpHost ?>vendor/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="<?php echo $httpHost ?>vendor/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="<?php echo $httpHost ?>vendor/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<link rel="stylesheet" href="<?php echo $httpHost ?>vendor/plugins/bootstrap-fileupload/bootstrap-fileupload.min.css">
<!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&amp;display=fallback"> -->