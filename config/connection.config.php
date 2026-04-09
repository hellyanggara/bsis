<?php
	$lifetime = 3600 * 24;
	@session_set_cookie_params($lifetime, '/');
    @session_start();
	// @setcookie(session_name(), session_id(), time() + $lifetime);
    $codeSource = 'bsis';
    $httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
    $documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
    
    //server
    // $globalHostname = '172.16.0.168';
    // $globalUsername = 'pde_sa';
    // $globalPassword = 'rsud656#KANUJOSO';
    // $globalDatabase = 'pderskd';

    //localhost
    // $globalHostname = 'localhost';
    // $globalUsername = 'root';
    // $globalPassword = '';
    // $globalDatabase = 'pderskd';

    if(getHostByName(getHostName()) == '172.16.0.168' AND gethostname() == 'WIN-JPBCE8KUBJ0'){
        $globalHostname = "172.16.0.168";
        $globalUsername = "pde_sa";
        $globalPassword = "rsud656#KANUJOSO";
        $globalDatabase = "pderskd";
        $_SESSION['server'] = 'operasional';
    }elseif(getHostByName(getHostName()) == '172.16.0.108'){
        $globalHostname = "172.16.0.108";
        $globalUsername = "pde_tester";
        $globalPassword = "rsud656#KANUJOSO";
        $globalDatabase = "pderskd";
        $_SESSION['server'] = 'tester';
    }else{
        $globalHostname = "localhost";
        $globalUsername = "root";
        $globalPassword = "";
        $globalDatabase = "pderskd";
        $_SESSION['server'] = 'local';
    }

    $connection = mysqli_connect($globalHostname,$globalUsername,$globalPassword, $globalDatabase);
?>