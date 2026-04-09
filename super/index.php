<?php
    $codeSource = 'bsis';
    $httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
    $documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
    require $documentRoot.'config/connection.config.php';
    require $documentRoot.'config/utility.config.php';
    signInCheck();
    $slug = $_GET['ref'];

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
    $_SESSION['V1c1T2NHTjNQVDA9_activePage'] = $dt->active_menu;
    $_SESSION['V1c1T2NHTjNQVDA9_titlePage'] = $dt->nama;
?>
<!DOCTYPE html>
<html>
<head>
    <?php include $documentRoot.'config/style.config.php';?>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed text-sm">
    <div class="wrapper">
        <?php include $documentRoot.'config/nav.config.php';?>
        <?php include $documentRoot.'config/aside/bsis.aside.php';?>
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0"><?php echo $_SESSION['V1c1T2NHTjNQVDA9_titlePage']?></h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="../dashboard/">Dashboard</a></li>
                            <li class="breadcrumb-item">Master</a></li>
                            <li class="breadcrumb-item active"><?php echo $_SESSION['V1c1T2NHTjNQVDA9_titlePage']?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-header">
                            <?php if(!isset($_GET['trash'])){
                            echo '
                            <a href="create.php?ref='.$slug.'" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah
                            </a>';
                            }?>
                            <a href="<?php echo isset($_GET['trash']) ? '?ref='.$slug : '?ref='.$slug.'&trash'?>" class="btn btn-<?php echo isset($_GET['trash']) ? 'dark' : 'light'?> btn-sm float-right">
                                <i class="fas fa-trash"></i> Trash
                            </a>
                        </div>
                        <div class="card-body">
                            <div id="cardBodyManrisk"></div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php include $documentRoot.'config/footer.config.php';?>
    </div>
    <?php include $documentRoot.'config/script.config.php';?>
    <script>
        $('#manriskYear').datetimepicker({
            format: 'YYYY'
        });

        $(document).ready(function () {
            tableContent();
        });

        function tableContent() {
            $.ajax({
                type: 'get',
                data: {
                    slug : '<?php echo $dt->slug ?>',
                    trash : '<?php echo isset($_GET['trash']) ? "trash" : "" ?>' 
                },
                url: 'table_super.php',
                beforeSend: function () {
                    Swal.fire({ 
                        imageUrl: '<?php echo $httpHost ?>vendor/dist/img/rskd.png',
                        imageHeight: 60,
                        imageAlt: 'RSKD',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        footer: '<div class="d-flex justify-content-center"><div class="line-wobble"></div></div>',
                        title: 'Data sedang disiapkan..',
                    });
                },
                success: function (res) {
                    <?php if(!isset($_SESSION['V1c1T2NHTjNQVDA9_notif_status'])){ ?>
                        Swal.close();
                    <?php }?>
                    $('#cardBodyManrisk').html(res);
                },
            });
        }
    </script>
</body>
</html>