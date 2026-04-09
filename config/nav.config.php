<?php
    @session_start();
    $codeSource = 'bsis';
    $httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
    $documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
    if (isset($_POST['signOut'])) {
        unset($_POST['signOut']);
        clearSession();
        redirectUrl('../../portal/');
    } elseif (isset($_POST['gotoPortal'])) {
        unset($_POST['gotoPortal']);
        unset($_SESSION['V1c1T2NHTjNQVDA9_level']);
        unset($_SESSION['V1c1T2NHTjNQVDA9_id_pegawai']);
        unset($_SESSION['V1c1T2NHTjNQVDA9_nama_akses']);
        unset($_SESSION['V1c1T2NHTjNQVDA9_namaPegawai']);
        unset($_SESSION['V1c1T2NHTjNQVDA9_foto']);
        unset($_SESSION['V1c1T2NHTjNQVDA9_jk']);
        unset($_SESSION['V1c1T2NHTjNQVDA9_nama_app']);
        clearNotification();
        redirectUrl('../../../../portal/');
    }

    if (getHostByName(getHostName()) != '172.16.0.168') {
        $navColor = 'navbar-danger';
        $textColor = 'text-white';
    } else {
        $navColor = 'navbar-white';
        $textColor = '';
    }

    echo '
    <nav class="main-header navbar navbar-expand navbar-light '.$navColor.' text-sm">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link '.$textColor.'" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a class="nav-link">SELAMAT DATANG DI EKSEKUTIF INFORMATION SISTEM</a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="#" class="nav-link '.$textColor.'">'.@$_SESSION['V1c1T2NHTjNQVDA9__namaPegawai'].'</a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">';
            echo '
            <li class="nav-item mt-1">
                '.(isset($_SESSION['V1c1T2NHTjNQVDA9_namaJenisUser']) && isset($_SESSION['V1c1T2NHTjNQVDA9_namaBagian']) ? ucwords($_SESSION['V1c1T2NHTjNQVDA9_namaJenisUser']).' | '.$_SESSION['V1c1T2NHTjNQVDA9_namaBagian'] : '').'
            </li>
            <li class="nav-item">
                <a class="nav-link '.$textColor.'" href="#" role="button" id="tanyaAkses">
                    <i class="fas fa-th-large"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link '.$textColor.'" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link '.$textColor.'" href="#" role="button" id="navSignOut">
                    <i class="fas fa-power-off '.(($textColor == '') ? 'text-danger' : '').'"></i>
                </a>
            </li>
        </ul>
    </nav>

    <form class="d-none" method="post" id="formPortal" name="formPortal">
        <button type="submit" class="btn btn-primary d-none" id="gotoPortal" name="gotoPortal">Go to Portal</button>
    </form>

    <form class="d-none" method="post" id="formSignOut" name="formSignOut">
        <button type="submit" class="btn btn-danger d-none" id="signOut" name="signOut">Sign Out</button>
    </form>
    <script>
        $(document).ready(function () {
            document.getElementById(\'navSignOut\').onclick = function() {
                Swal.fire({
                    title: \'Lanjut keluar akun?\',
                    text: \'Sesi ini akan berakhir!\',
                    icon: \'question\',
                    showCancelButton: true,
                    confirmButtonColor: \'#dc3545\',
                    cancelButtonColor: \'#6c757d\',
                    confirmButtonText: \'Ya, sign out\',
                }).then((result) => {
                    if (result.isConfirmed) {
                        let timerInterval;
                        Swal.fire({
                            title: \'Silahkan menunggu!\',
                            html: \'Loading <b></b> milliseconds.\',
                            timer: 20111994,
                            timerProgressBar: true,
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                                const b = Swal.getHtmlContainer().querySelector(\'b\');
                                timerInterval = setInterval(() => {
                                    b.textContent = Swal.getTimerLeft()
                                }, 100);
                            },
                            willClose: () => {
                                clearInterval(timerInterval);
                            }
                        }).then((result) => {
                            if (result.dismiss === Swal.DismissReason.timer) {
                                //
                            }
                        });
                        $(\'#signOut\').click();
                    }
                });
            };

            $("#tanyaAkses").click(function(){
                $(\'#gotoPortal\').click(); 
            });
        });
    </script>';
?>