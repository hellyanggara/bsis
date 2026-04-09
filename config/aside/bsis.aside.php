<?php
    $codeSource = 'bsis';
    $httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
    $documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
    require $documentRoot.'config/connection.config.php';
    require $documentRoot.'config/utility.config.php';

    $level = $_SESSION['V1c1T2NHTjNQVDA9_level'];
    $idpegawai = $_SESSION['V1c1T2NHTjNQVDA9_id_pegawai'];
?>

<aside class="main-sidebar sidebar-light-dark elevation-4">
    <a href="javascript:void(0);" class="brand-link text-sm">
        <img src="<?= $httpHost; ?>vendor/dist/img/bsis.png" class="brand-image img-circle">
        <span class="brand-text info"><?= $_SESSION['V1c1T2NHTjNQVDA9_nama_app'] ?></span>
    </a>
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <?php
                if ($_SESSION['V1c1T2NHTjNQVDA9_foto'] == 'FTNOIMG.jpg' || $_SESSION['V1c1T2NHTjNQVDA9_foto'] == '') {
                    if($_SESSION['V1c1T2NHTjNQVDA9_jk'] == "L"){
                        $urlphoto = $httpHost.'vendor/dist/img/male.png';
                    }elseif($_SESSION['V1c1T2NHTjNQVDA9_jk'] == "P"){
                        $urlphoto = $httpHost.'vendor/dist/img/female.png';
                    }else{
                        $urlphoto = $httpHost.'vendor/dist/img/user.jpg';
                    }
                }else{
                    $urlphoto = '../../../../../bankdata/app/comp/photo/pegawai/'.$_SESSION['V1c1T2NHTjNQVDA9_foto'];
                }
                ?>
                <img src="<?= $urlphoto ?>" class="img-circle" alt="User Image">
            </div>
            <div class="info">
                <a href="javascript:void(0);"
                    class="d-block"><?= $_SESSION['V1c1T2NHTjNQVDA9_namaPegawai'] == "" ? substr($_SESSION['portal_namapegawai'], 0, 24) : substr($_SESSION['V1c1T2NHTjNQVDA9_namaPegawai'], 0, 24); ?></a>
            </div>
        </div>
        <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item">
                <a href="<?= $httpHost ?>dashboard/" class="nav-link <?= @$_SESSION['V1c1T2NHTjNQVDA9_activePage'] == 'dashboard' ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>
                        Dashboard
                    </p>
                </a>
            </li>
            <?php
                $sqlGroupMenu = "SELECT
                            t1.*,
                        CASE
                                
                                WHEN EXISTS ( SELECT 1 FROM bsis_group_menu_aplikasi t2 WHERE t2.parent_id = t1.id ) THEN
                                '1' ELSE '0' 
                            END AS 'child' 
                        FROM
                            bsis_group_menu_aplikasi t1 
                        WHERE
                            parent_id IS NULL 
                            AND deleted_at IS NULL 
                            AND id IN (
                            SELECT
                                * 
                            FROM
                                (
                                SELECT
                                    bsis_group_menu_aplikasi.id 
                                FROM
                                    bsis_mapping_hak_akses_jenis_user
                                    LEFT JOIN bsis_group_menu_aplikasi ON bsis_group_menu_aplikasi.id = bsis_mapping_hak_akses_jenis_user.`group` 
                                WHERE
                                    id_jenis_user = '$level' 
                                    AND bsis_mapping_hak_akses_jenis_user.`group` <> 0 
                                GROUP BY
                                    bsis_mapping_hak_akses_jenis_user.`group` UNION ALL
                                SELECT
                                    bsis_group_menu_aplikasi.id 
                                FROM
                                    bsis_mapping_hak_akses_jenis_user
                                    LEFT JOIN bsis_group_menu_aplikasi ON bsis_group_menu_aplikasi.id = bsis_mapping_hak_akses_jenis_user.id_menu_aplikasi 
                                WHERE
                                    bsis_mapping_hak_akses_jenis_user.id_jenis_user = '$level' 
                                    AND bsis_mapping_hak_akses_jenis_user.`group` = 0 UNION ALL
                                SELECT
                                    bsis_group_menu_aplikasi.id 
                                FROM
                                    bsis_mapping_hak_akses_user
                                    LEFT JOIN bsis_group_menu_aplikasi ON bsis_group_menu_aplikasi.id = bsis_mapping_hak_akses_user.`group` 
                                WHERE
                                    id_pegawai = '$idpegawai' 
                                    AND bsis_mapping_hak_akses_user.`group` <> 0 
                                GROUP BY
                                    bsis_mapping_hak_akses_user.`group` UNION ALL
                                SELECT
                                    bsis_group_menu_aplikasi.id 
                                FROM
                                    bsis_mapping_hak_akses_user
                                    LEFT JOIN bsis_group_menu_aplikasi ON bsis_group_menu_aplikasi.id = bsis_mapping_hak_akses_user.id_menu_aplikasi 
                                WHERE
                                    id_pegawai = '$idpegawai' 
                                    AND bsis_mapping_hak_akses_user.`group` = 0 
                                GROUP BY
                                    bsis_mapping_hak_akses_user.`group` 
                                ) AS DATA 
                            GROUP BY
                                DATA.id 
                            ORDER BY
                                DATA.id 
                            ) 
                        ORDER BY
                            `order`";
                $get_group_menu = $connection->query($sqlGroupMenu);
                $ortu = '';
                while($dtAppGroup = $get_group_menu->fetch_object()){
                    if(stripos(@$_SESSION['V1c1T2NHTjNQVDA9_activePage'], $dtAppGroup->active_menu) !== FALSE){
                        $groupActive = 'active';
                        $groupOpen = 'menu-open';
                    }else{
                        $groupActive = '';
                        $groupOpen = '';
                    }
                    if($dtAppGroup->group == '1'){
                        if($dtAppGroup->child == '0'){
                            echo'
                            <li class="nav-item '.$groupOpen.'">
                                <a href="#" class="nav-link '.$groupActive.'">
                                    <i class="'.$dtAppGroup->icon.'"></i>
                                    <p>'.$dtAppGroup->nama.'<i class="right fas fa-angle-left"></i></p>
                                </a>
                                <ul class="nav nav-treeview">';
                                    $get_menu = $connection->query("SELECT
                                        * 
                                    FROM
                                        bsis_menu_aplikasi 
                                    WHERE
                                        deleted_at IS NULL 
                                        AND id_group = '$dtAppGroup->id' 
                                        AND id IN ( SELECT * FROM (
                                        SELECT
                                            id_menu_aplikasi 
                                        FROM
                                            bsis_mapping_hak_akses_jenis_user 
                                        WHERE
                                            id_jenis_user = '$level' AND `group` <> 0
                                            UNION ALL
                                            SELECT
                                            id_menu_aplikasi 
                                        FROM
                                            bsis_mapping_hak_akses_user 
                                        WHERE
                                            id_pegawai = '$idpegawai' AND `group` <> 0) as DATA
                                            GROUP BY DATA.id_menu_aplikasi ) 
                                    ORDER BY
                                        FIELD( nama, 'Menu' ),
                                        bsis_menu_aplikasi.id");
                                    while($dtApp = $get_menu->fetch_object()){
                                    $active = stripos($dtApp->active_menu, @$_SESSION['V1c1T2NHTjNQVDA9_activePage']) !== FALSE ? 'active' : '';
                                    echo '
                                    <li class="nav-item">
                                    <a href="'.$httpHost.$dtApp->direktori.'" class="pl-3 nav-link '.$active.'">
                                        <i class="'.$dtApp->icon.'"></i>
                                        <p>'.$dtApp->nama.'</p>
                                    </a>
                                    </li>';
                                    }
                            echo '</ul>
                            </li>';
                        }else{
                            echo '
                            <li class="nav-item '.$groupOpen.'">
                                <a href="#" class="nav-link '.$groupActive.'">
                                    <i class="'.$dtAppGroup->icon.'"></i>
                                    <p>'.$dtAppGroup->nama.'<i class="right fas fa-angle-left"></i></p>
                                </a>
                                <ul class="nav nav-treeview">';
                                $get_sub_group_menu = $connection->query("SELECT
                                    * 
                                FROM
                                    bsis_group_menu_aplikasi 
                                WHERE
                                    parent_id = '$dtAppGroup->id' 
                                    AND id IN ( SELECT * FROM (
                                    SELECT
                                        sub_group 
                                    FROM
                                        bsis_mapping_hak_akses_jenis_user 
                                    WHERE
                                        id_jenis_user = '$level' 
                                        AND sub_group IS NOT NULL 
                                    GROUP BY
                                        sub_group UNION ALL
                                    SELECT
                                        sub_group 
                                    FROM
                                        bsis_mapping_hak_akses_user 
                                    WHERE
                                        id_pegawai = '$idpegawai' 
                                        AND sub_group IS NOT NULL 
                                    GROUP BY
                                        sub_group) as DATA
                                        GROUP BY DATA.sub_group ) 
                                    AND deleted_at IS NULL");
                                while($dtSubGroup = $get_sub_group_menu->fetch_object()){
                                    if(stripos(@$_SESSION['V1c1T2NHTjNQVDA9_activePage'], $dtSubGroup->active_menu) !== FALSE){
                                        $subGroupActive = 'active';
                                        $subGroupOpen = 'menu-open';
                                    }else{
                                        $subGroupActive = '';
                                        $subGroupOpen = '';
                                    }
                                    echo '
                                    <li class="nav-item '.$subGroupOpen.'">
                                        <a href="#" class="pl-3 nav-link '.$subGroupActive.'">
                                            <i class="'.$dtSubGroup->icon.'"></i>
                                            <p>'.$dtSubGroup->nama.'
                                            <i class="right fas fa-angle-left"></i></p>
                                        </a>
                                        <ul class="nav nav-treeview">';
                                            $get_menu = $connection->query("SELECT
                                                * 
                                            FROM
                                                bsis_menu_aplikasi 
                                            WHERE
                                                deleted_at IS NULL 
                                                AND id_group = '$dtSubGroup->id' 
                                                AND id IN ( SELECT * FROM (
                                        SELECT
                                            id_menu_aplikasi 
                                        FROM
                                            bsis_mapping_hak_akses_jenis_user 
                                        WHERE
                                            id_jenis_user = '$level' AND `group` <> 0
                                            UNION ALL
                                            SELECT
                                            id_menu_aplikasi 
                                        FROM
                                            bsis_mapping_hak_akses_user 
                                        WHERE
                                            id_pegawai = '$idpegawai' AND `group` <> 0) as DATA
                                            GROUP BY DATA.id_menu_aplikasi ) 
                                            ORDER BY
                                                FIELD( nama, 'Menu' ),
                                                bsis_menu_aplikasi.id");
                                            while($dtApp = $get_menu->fetch_object()){
                                            $active = stripos($dtApp->active_menu, @$_SESSION['V1c1T2NHTjNQVDA9_activePage']) !== FALSE ? 'active' : '';
                                            echo '
                                            <li class="nav-item">
                                            <a href="'.$httpHost.$dtApp->direktori.'" class="pl-4 nav-link '.$active.'">
                                                <i class="'.$dtApp->icon.'"></i>
                                                <p>'.$dtApp->nama.'</p>
                                            </a>
                                            </li>';
                                            }
                                        echo '
                                        </ul>
                                    </li>';
                                }
                            echo '
                                </ul>
                            </li>';
                        }
                    }else{
                        echo '
                            <li class="nav-item">
                            <a href="'.$httpHost.$dtAppGroup->direktori.'" class="nav-link '.$groupActive.'">
                                <i class="'.$dtAppGroup->icon.'"></i>
                                <p>'.$dtAppGroup->nama.'</p>
                            </a>
                            </li>';
                    }
                }
            ?>
        </ul>
    </div>
</aside>