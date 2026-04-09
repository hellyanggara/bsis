<?php
    $codeSource = 'bsis';
    $httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
    $documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
    require $documentRoot.'config/connection.config.php';
    require $documentRoot.'config/utility.config.php';
    $_SESSION['V1c1T2NHTjNQVDA9_activePage'] = 'dashboard';
    $id_pegawai = $_SESSION['V1c1T2NHTjNQVDA9_id_pegawai'];
    // signInCheck();
    include ('dashboard.php');
    $q = new DASHBOARD();

    
?>
<!DOCTYPE html>
<html>
<head>
    <?php include $documentRoot.'config/style.config.php';?>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed text-sm">
    <div class="wrapper">
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="<?= $httpHost ?>vendor/dist/img/rskd.png" alt="RSKDLogo" height="60" width="60">
        </div>
        <?php include $documentRoot.'config/nav.config.php';?>
        <?php include $documentRoot.'config/aside/bsis.aside.php';?>
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Dashboard</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <center>
                        <img class="mw-100" src="<?php echo $httpHost.'vendor/dist/img/underconstruction.jpg' ?>"  class="product-image" alt="Product Image">
                    </center>
                </div>
            </section>
        </div>
        <?php include $documentRoot.'config/footer.config.php';?>
    </div>
        <?php include $documentRoot.'config/script.config.php';?>
</body>
</html>