<?php
    ob_start();
    @session_start();
    $codeSource = 'bsis';
    $httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
    $documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
    date_default_timezone_set('Asia/Singapore');

    if (!function_exists('sliceToStripe')) {
        function sliceToStripe($value)
        {
            global $notification;
            global $httpHost;
            global $documentRoot;
            
            return str_replace('/', '-', $value);
        }
    }

    if (!function_exists('dateNormalize')) {
        function dateNormalize($date)
        {
            global $notification;
            global $httpHost;
            global $documentRoot;
            
            if ($date instanceof DateTime) {
                return $date->getTimestamp();
            } else {
                return strtotime($date);
            }
        }
    }

    if (!function_exists('dayIndonesianConvert')) {
        function dayIndonesianConvert($date, $withDay = false, $withTime = false) {
            global $notification;
            global $httpHost;
            global $documentRoot;
            $date = strtotime($date);
            
            if (date('m', $date) == '1') {
                $month = 'Januari';
            } elseif (date('m', $date) == '2') {
                $month = 'Februari';
            } elseif (date('m', $date) == '3') {
                $month = 'Maret';
            } elseif (date('m', $date) == '4') {
                $month = 'April';
            } elseif (date('m', $date) == '5') {
                $month = 'Mei';
            } elseif (date('m', $date) == '6') {
                $month = 'Juni';
            } elseif (date('m', $date) == '7') {
                $month = 'Juli';
            } elseif (date('m', $date) == '8') {
                $month = 'Agustus';
            } elseif (date('m', $date) == '9') {
                $month = 'September';
            } elseif (date('m', $date) == '10') {
                $month = 'Oktober';
            } elseif (date('m', $date) == '11') {
                $month = 'November';
            } elseif (date('m', $date) == '12') {
                $month = 'Desember';
            }
            if ($withDay) {
                if (date('l', $date) == 'Monday') {
                    $day = 'Senin, ';
                } elseif (date('l', $date) == 'Tuesday') {
                    $day = 'Selasa, ';
                } elseif (date('l', $date) == 'Wednesday') {
                    $day = 'Rabu, ';
                } elseif (date('l', $date) == 'Thursday') {
                    $day = 'Kamis, ';
                } elseif (date('l', $date) == 'Friday') {
                    $day = 'Jumat, ';
                } elseif (date('l', $date) == 'Saturday') {
                    $day = 'Sabtu, ';
                } elseif (date('l', $date) == 'Sunday') {
                    $day = 'Minggu, ';
                }
            } else {
                $day = '';
            }
            if ($withTime) {
                $time = ' ' . date('H:i', $date);
            } else {
                $time = '';
            }
            return $day . date('d', $date) . ' ' . $month . ' ' . date('Y', $date) . $time;
        }
    }

    if (!function_exists('getIndonesianDay')) {
        function getIndonesianDay($date) {
            global $notification;
            global $httpHost;
            global $documentRoot;
            
            $day = date('l', strtotime($date));
            switch ($day) {
                case 'Monday':
                    return 'Senin';
                case 'Tuesday':
                    return 'Selasa';
                case 'Wednesday':
                    return 'Rabu';
                case 'Thursday':
                    return 'Kamis';
                case 'Friday':
                    return 'Jumat';
                case 'Saturday':
                    return 'Sabtu';
                case 'Sunday':
                    return 'Minggu';
                default:
                    return null;
            }
        }
    }

    if (!function_exists('newline')) {
        function newline($value, $direction, $type) {
            global $notification;
            global $httpHost;
            global $documentRoot;
            
            if ($direction == 'backward') {
                if ($type == 'br') {
                    return str_replace('^', '<br>', $value);
                } elseif ($type == 'n') {
                    return str_replace('^', '\n', $value);
                } elseif ($type == '13') {
                    return str_replace('^', '&#13;', $value);
                }
            } elseif ($direction == 'forward') {
                if ($type == 'br') {
                    return str_replace('<br>', '^', $value);
                } elseif ($type == 'n') {
                    return preg_replace('/\n/', '^', (str_replace('\'', '`', $value)));
                } elseif ($type == '13') {
                    return preg_replace('/&#13;/', '^', (str_replace('\'', '`', $value)));
                }
            }
        }
    }

    if (!function_exists('removeQuote')) {
        function removeQuote($value) {
            global $notification;
            global $httpHost;
            global $documentRoot;
            
            $result = str_replace("'", "`", $value);
            $result = str_replace('"', '`', $result);
            return $result;
        }
    }

    if (!function_exists('insertSingleQuote')) {
        function insertSingleQuote($value) {
            global $notification;
            global $httpHost;
            global $documentRoot;
            
            $result = htmlspecialchars(str_replace("'", "''", $value), ENT_COMPAT);
            return $result;
        }
    }

    if (!function_exists('removeTag')) {
        function removeTag($value) {
            global $notification;
            global $httpHost;
            global $documentRoot;
            
            $result = str_replace('<', '< ', $value);
            $result = str_replace('>', ' >', $result);
            return $result;
        }
    }

    if (!function_exists('removeSymbol')) {
        function removeSymbol($value) {
            global $notification;
            global $httpHost;
            global $documentRoot;
            
            $result = preg_replace('/[^\p{L}\p{N}\s]/u', '-s-y-m-', $value);
            return $result;
        }
    }

    if (!function_exists('clearNotification')) {
        function clearNotification() {
            global $notification;
            global $httpHost;
            global $documentRoot;
            
            unset($_SESSION['V1c1T2NHTjNQVDA9__notification']);
        }
    }

    if (!function_exists('clearSession')) {
        function clearSession() {
            global $notification;
            global $httpHost;
            global $documentRoot;
            
            $session = array_keys($_SESSION);
            foreach ($session as $row) {
                if ($row != 'V1c1T2NHTjNQVDA9__notification') {
                    unset($_SESSION[$row]);
                }
            }
        }
    }

    if (!function_exists('redirectUrl')) {
        function redirectUrl($url) {
            global $notification;
            global $httpHost;
            global $documentRoot;
            
            // echo '<script>window.location.href=\''.$httpHost.$url.'\';</script>';
            // exit;
            header('Location: '.$httpHost.$url);
            die();
        }
    }
    
    if (!function_exists('namaBulan')) {
        function namaBulan($value) {
            $bulan = [
                1  => 'Januari',
                2  => 'Februari',
                3  => 'Maret',
                4  => 'April',
                5  => 'Mei',
                6  => 'Juni',
                7  => 'Juli',
                8  => 'Agustus',
                9  => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember'
            ];
            return $bulan[(int)$value] ?? null; 
        }
    }

    if (!function_exists('signInCheck')) {
        function signInCheck() {
            if(!$_SESSION['V1c1T2NHTjNQVDA9_id_pegawai']){
                redirectUrl('../../portal');
            }
        }
    }

    if (!function_exists('getRomawi')) {
        function getRomawi($bln) {
            switch ($bln){
                case 1: 
                    return "I";
                    break;
                case 2:
                    return "II";
                    break;
                case 3:
                    return "III";
                    break;
                case 4:
                    return "IV";
                    break;
                case 5:
                    return "V";
                    break;
                case 6:
                    return "VI";
                    break;
                case 7:
                    return "VII";
                    break;
                case 8:
                    return "VIII";
                    break;
                case 9:
                    return "IX";
                    break;
                case 10:
                    return "X";
                    break;
                case 11:
                    return "XI";
                    break;
                case 12:
                    return "XII";
                    break;
            }
        }
    }

    if (!function_exists('getListBulan')) {
        function getListBulan() {
            $bulan = array(1,2,3,4,5,6,7,8,9,10,11,12);
            return $bulan;
        }
    }

    if (!function_exists('get_object')) {
        function get_object($jenis, $nama, $tipe, $wajib, $value = false, $sql, $label = false, $div = false, $additional = false)
        {
            global $documentRoot;
            
            $required = $wajib == '1' ? 'wajib' : '';
            if($label){
                $nama_value = $label;
            }else{
                $nama_value = str_replace('_', ' ', $nama);
            }
            if($value != null){
                $dataValue = $value;
                $color = 'style="color:'.$dataValue.'"';
            }else{
                $dataValue = '';
                $color = '';
            }
            $item = '';
            switch ($jenis) {
                case 'input':
                    $item = '
                    <div class="form-group">
                        <label>'.ucwords($nama_value).'</label>
                        <input name="'.$nama.'" id="'.$nama.'" type="'.$tipe.'" class="form-control form-control-sm '.$required.'" placeholder="Masukkan '.ucwords($nama_value).'" value="'.$dataValue.'" autocomplete="off">
                        <span class="error invalid-feedback">'.ucwords($nama_value).' harus diisi</span>
                    </div>';
                break;
                case 'textarea':
                    $item = '
                    <div class="form-group">
                        <label>'.ucwords($nama_value).'</label>
                        <textarea name="'.$nama.'" id="'.$nama.'" class="form-control form-control-sm '.$required.'" placeholder="Masukkan '.ucwords($nama_value).'">'.$dataValue.'</textarea>
                        <span class="error invalid-feedback">'.ucwords($nama_value).' harus diisi</span>
                    </div>';
                break;
                case 'colorpicker':
                    $item = '
                    <div class="form-group">
                        <label>'.ucwords($nama_value).'</label>
                        <div class="input-group my-colorpicker2">
                            <input type="text" class="form-control form-control-sm '.$required.'" name="'.$nama.'" id="'.$nama.'" value="'.$dataValue.'">
                            <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-square" '.$color.'></i></span>
                            </div>
                            <span class="error invalid-feedback">'.ucwords($nama_value).' harus dipilih</span>
                        </div>
                    </div>';
                break;
                case 'datalist':
                    require $documentRoot.'config/connection.config.php';
                    require $documentRoot.'config/utility.config.php'; 
                    $query = $connection->query($sql);
                    $list = '';
                    while($dt = $query->fetch_object()){
                        $list .= '<option value="'.$dt->nama.'">';
                    }
                    $item .= '
                    <div class="form-group">
                        <label>'.ucwords($nama_value).'</label>
                        <input name="'.$nama.'" list="'.$nama.'s" id="'.$nama.'" type="text" class="form-control form-control-sm '.$required.'" placeholder="Masukkan '.ucwords($nama_value).'" autocomplete="off" value="'.$dataValue.'">
                        <datalist id="'.$nama.'s">
                            '.$list.'
                        </datalist>
                        <span class="error invalid-feedback">'.ucwords($nama_value).' harus diisi</span>
                    </div>';
                break;
                case 'checkbox':
                    $check = $dataValue == '1' ? 'checked' : '';
                    $item = '
                    <div class="form-group">
                        <div class="form-check">
                        <input id="'.$nama.'" name="'.$nama.'" class="form-check-input" type="checkbox" value="1" '.$check.'>
                        <label for="'.$nama.'" class="form-check-label">'.ucwords($nama_value).'</label>
                        </div>
                    </div>';
                break;
                case 'dropdown':
                    require $documentRoot.'config/connection.config.php';
                    require $documentRoot.'config/utility.config.php'; 
                    $list = '';
                    if(is_string($sql)){
                        $query = $connection->query($sql);
                        while($dt = $query->fetch_object()){
                            $selList = $dt->id == $dataValue ? 'selected' : '';
                            $list .= '<option value="'.$dt->id.'" '.$selList.'>'.$dt->nama.'</option>';
                        }
                    }else{
                        foreach ($sql as $key => $value) {
                            $selList = $key == $dataValue ? 'selected' : '';
                            $list .= '<option value="'.$key.'" '.$selList.'>'.$value.'</option>';
                        }
                    }
                    $item .= '
                    <div class="form-group">
                        <label>'.ucwords($nama_value).'</label>
                        <select name="'.$nama.'" id="'.$nama.'" class="form-control select2 '.$required.'" '.$additional.'>
                            <option></option>
                            '.$list.'
                        </select>
                        <span class="error invalid-feedback">'.ucwords($nama_value).' harus diisi</span>
                    </div>';
                break;
                case 'datepicker':
                    $item = '
                    <div class="form-group">
                        <label>'.ucwords($nama_value).'</label>
                        <div class="input-group date" id="datepicker-'.$nama.'" data-target-input="nearest">
                            <input type="text" name="'.$nama.'" id="'.$nama.'" class="form-control form-control-sm datetimepicker-input '.$required.'" data-target="#datepicker-'.$nama.'" value="'.$dataValue.'" placeholder="Silahkan Pilih" autocomplete="off" '.$additional.'/>
                            <div class="input-group-append" data-target="#datepicker-'.$nama.'" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </div>';
                break;
            }
            return $item;
        }
    }

    if (!function_exists('cek_akses')) {
        function cek_akses($id_jenis, $id_pegawai, $id_menu, $group, $variable = false) {
            require $_SERVER['DOCUMENT_ROOT'].'/keuangan/config/connection.config.php';

            $var = $variable ? $variable : "<>";
            $sqlPerUser = "SELECT `create`, `update`, `delete` FROM bsis_mapping_hak_akses_user WHERE id_pegawai = '$id_pegawai' AND id_menu_aplikasi = '$id_menu' AND `group` $var '$group'";
            $qPerUser = $connection->query($sqlPerUser);
            $jml = $qPerUser->num_rows;
            if($jml > 0){
                $data = $qPerUser->fetch_object();
            }else{
                $sql = "SELECT * FROM bsis_mapping_hak_akses_jenis_user WHERE id_jenis_user = '$id_jenis' AND id_menu_aplikasi = '$id_menu' AND `group` $var '$group'";
                $qPerJenisUser = $connection->query($sql);
                $data = $qPerJenisUser->fetch_object();
                $jml = $qPerJenisUser->num_rows;
            }
            return array($data, $jml);
        }
    }
    
    if (!function_exists('get_max_tanggal')) {
        function get_max_tanggal($bln, $thn) {
            require $_SERVER['DOCUMENT_ROOT'].'/keuangan/config/connection.config.php';
            if ($bln == "1" or $bln == "3" or $bln == "5" or $bln == "7" or $bln == "8" or $bln == "10" or $bln == "12") {
                $maxTgl = '31';
            }elseif($bln == '2'){
                $leap = date('L', mktime(0, 0, 0, 1, 1, $thn));
                $maxTgl = $leap ? '29' : '28';
            }else{
                $maxTgl = '30';
            }
            return $maxTgl;
        }
    }

    if (!function_exists('post')) {
        function post($post, $type)
        {
            if($type == 'text'){
                $post_value = insertSingleQuote($post);
            }elseif($type == 'tanggal'){
                $post_value = insertSingleQuote(date("Y-m-d H:i:s",strtotime(str_replace("/","-",$post))));
            }elseif($type == 'implode'){
                $post_value = implode (",", $post);
            }
            $value = "'$post_value'";
            return $value;
        }
    }
    
    if (!function_exists('penyebut')) {
        function penyebut($nilai) {
            $nilai = abs($nilai);
            $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
            $temp = "";
            if ($nilai < 12) {
                $temp = " ". $huruf[$nilai];
            } else if ($nilai <20) {
                $temp = penyebut($nilai - 10). " belas";
            } else if ($nilai < 100) {
                $temp = penyebut($nilai/10)." puluh". penyebut($nilai % 10);
            } else if ($nilai < 200) {
                $temp = " seratus" . penyebut($nilai - 100);
            } else if ($nilai < 1000) {
                $temp = penyebut($nilai/100) . " ratus" . penyebut($nilai % 100);
            } else if ($nilai < 2000) {
                $temp = " seribu" . penyebut($nilai - 1000);
            } else if ($nilai < 1000000) {
                $temp = penyebut($nilai/1000) . " ribu" . penyebut($nilai % 1000);
            } else if ($nilai < 1000000000) {
                $temp = penyebut($nilai/1000000) . " juta" . penyebut($nilai % 1000000);
            } else if ($nilai < 1000000000000) {
                $temp = penyebut($nilai/1000000000) . " milyar" . penyebut(fmod($nilai,1000000000));
            } else if ($nilai < 1000000000000000) {
                $temp = penyebut($nilai/1000000000000) . " trilyun" . penyebut(fmod($nilai,1000000000000));
            }     
            return $temp;
        }
    }

    if (!function_exists('terbilang')) {
        function terbilang($nilai) {
            if($nilai<0) {
                $hasil = "minus ". trim(penyebut($nilai));
            } else {
                $hasil = trim(penyebut($nilai));
            }     		
            return $hasil;
        }
    }
    
    if (!function_exists('hari')) {
        function hari($value) {
            switch ($value){
                case 'Monday':
                    return 'Senin';
                    break;
                case 'Tuesday':
                    return 'Selasa';
                    break;
                case 'Wednesday':
                    return 'Rabu';
                    break;
                case 'Thursday':
                    return 'Kamis';
                    break;
                case 'Friday':
                    return 'Jumat';
                    break;
                case 'Saturday':
                    return 'Sabtu';
                    break;
                case 'Sunday':
                    return 'Minggu';
                    break;
            }
        }
    }
    
    if (!function_exists('bulanIntKeString')) {
        function bulanIntKeString($value) {
            switch ($value){
                case '1': 
                    return "Januari";
                    break;
                case '2':
                    return "Februari";
                    break;
                case '3':
                    return "Maret";
                    break;
                case '4':
                    return "April";
                    break;
                case '5':
                    return "Mei";
                    break;
                case '6':
                    return "Juni";
                    break;
                case '7':
                    return "Juli";
                    break;
                case '8':
                    return "Agustus";
                    break;
                case '9':
                    return "September";
                    break;
                case '10':
                    return "Oktober";
                    break;
                case '11':
                    return "November";
                    break;
                case '12':
                    return "Desember";
                    break;
            }
        }
    }

    if (!function_exists('cleanInput')) {
        function cleanInput($value) {
            return isset($value) && trim($value) !== "" ? $value : null;
        }
    }
?>