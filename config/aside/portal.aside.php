<?php
    @session_start();
    $codeSource = 'bsis';
    $httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
    $documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';

    echo '
    <aside class="main-sidebar sidebar-light-primary elevation-4">
        <a href="javascript:void(0);" class="brand-link text-sm">
            <img src="'.$httpHost.'vendor/dist/img/rskd.png" class="brand-image img-circle">
            <span class="brand-text info">RSKD</span>
        </a>
        <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="'.$httpHost.'vendor/dist/img/user.jpg" class="img-circle">
                </div>
                <div class="info">
                    <a href="javascript:void(0);" class="d-block text-sm">'.($_SESSION['V1c1T2NHTjNQVDA9_namaPegawai'] == "" ? substr($_SESSION['portal_namapegawai'], 0, 24) : substr($_SESSION['V1c1T2NHTjNQVDA9_namaPegawai'], 0, 24)).'</a>
                </div>
            </div>
        </div>
    </aside>';
?>