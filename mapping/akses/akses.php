<?php
@session_start();
class AKSES
{
    private $documentRoot;
    function __construct() 
    {
        $codeSource = 'bsis';
        $documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
        signInCheck();
        $this->documentRoot = $documentRoot;
    }

    public function cek_table($table, $id, $slug = false)
    {   
        require $this->documentRoot.'config/connection.config.php';
        require $this->documentRoot.'config/utility.config.php'; 
        if($id != '' && $slug == ''){
            $sql = "SELECT * FROM $table WHERE deleted_at IS NULL AND id = '$id'";
        }elseif($id == '' && $slug != ''){
            $sql = "SELECT * FROM $table WHERE deleted_at IS NULL AND slug = '$slug'";
        }elseif($id != '' && $slug != ''){
            if($slug == 'akses_jenis_user'){
                $sql = "SELECT * FROM $table WHERE deleted_at IS NULL AND id = '$id'";
            }elseif($slug == 'akses_user'){
                $sql = "SELECT * FROM $table WHERE id_pegawai = '$id'";
            }
        }
        $jml = $connection->query($sql)->num_rows;
        return $jml;
    }
    
    public function get_data($table, $id = false, $join = false, $tableJoin = false, $primaryId = false, $foreignId = false, $select = false, $where = false)
    {   
        require $this->documentRoot.'config/connection.config.php';
        require $this->documentRoot.'config/utility.config.php'; 
        if (strpos($id, '_') !== false) {
            $whereId = "AND slug = '$id'";
        }else{
            $whereId = "AND id = '$id'";
        }
        if($select == ''){
            $select = '*';
        }
        if($join != ''){
            $joined = $join.' '.$tableJoin.' on '.$tableJoin.'.'.$primaryId.' = '.$table.'.'.$foreignId;
        }else{
            $joined = '';
        }
        $sql = "SELECT * FROM $table $joined WHERE deleted_at IS NULL $whereId $where";
        $query = $connection->query($sql);
        $data = $query->fetch_object();
        return $data;
    }

    public function get_list($table, $join = false, $tableJoin = false, $primaryId = false, $foreignId = false, $select = false, $whereNull, $where = false, $group = false, $order = false)
    {   
        require $this->documentRoot.'config/connection.config.php';
        require $this->documentRoot.'config/utility.config.php'; 
        if($select == ''){
            $select = '*';
        }
        if($join != ''){
            $joined = $join.' '.$tableJoin.' on '.$tableJoin.'.'.$primaryId.' = '.$table.'.'.$foreignId;
        }else{
            $joined = '';
        }
        $whereNull = $whereNull ? "WHERE $table.deleted_at IS NULL" : '';
        $orderBy = $order ? "ORDER BY $order" : '';
        $groupBy = $group ? "GROUP BY $group" : '';
        $sql = "SELECT $select FROM $table $joined $whereNull $where $groupBy $orderBy";
        $query = $connection->query($sql);
        return $query;
    }

    public function get_additional_column($table)
    {
        switch ($table) {
            case 'bsis_mapping_hak_akses_jenis_user':
                $arr = array(array("nama"=>"id_jenis_user","jenis"=>"dropdown", "tipe"=>"", "wajib"=>"1"));
                break;
            case 'bsis_mapping_hak_akses_user':
                $arr = array(array("nama"=>"id_pegawai","jenis"=>"dropdown", "tipe"=>"", "wajib"=>"1"));
                break;
            default:
                $arr= array();
                break;
        }
        return $arr;
    }

    public function get_object($jenis, $nama, $tipe, $wajib, $value)
    {
        $required = $wajib == '1' ? 'wajib' : '';
        $nama = str_replace('_', ' ', $nama);
        if($value != null){
            $dataValue = $value;
            $color = 'style="color:'.$dataValue.'"';
        }else{
            $dataValue = '';
            $color = '';
        }
        switch ($jenis) {
            case 'input':
                $item = '
                <div class="form-group">
                    <label>'.ucwords($nama).'</label>
                    <input name="'.$nama.'" id="'.$nama.'" type="'.$tipe.'" class="form-control form-control-sm '.$required.'" placeholder="Masukkan '.ucwords($nama).'" value="'.$dataValue.'">
                    <span class="error invalid-feedback">'.ucwords($nama).' harus diisi</span>
                </div>';
            break;
            case 'textarea':
                $item = '
                <div class="form-group">
                    <label>'.ucwords($nama).'</label>
                    <textarea name="'.$nama.'" id="'.$nama.'" class="form-control form-control-sm '.$required.'" placeholder="Masukkan '.ucwords($nama).'">'.$dataValue.'</textarea>
                    <span class="error invalid-feedback">'.ucwords($nama).' harus diisi</span>
                </div>';
            break;
            case 'colorpicker':
                $item = '
                <div class="form-group">
                    <label>'.ucwords($nama).'</label>
                    <div class="input-group my-colorpicker2">
                        <input type="text" class="form-control form-control-sm '.$required.'" name="'.$nama.'" id="'.$nama.'" value="'.$dataValue.'">
                        <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-square" '.$color.'></i></span>
                        </div>
                        <span class="error invalid-feedback">'.ucwords($nama).' harus dipilih</span>
                    </div>
                </div>';
                break;
        }
        return $item;
    }
    
    public function get_jenis_user($id, $slug)
    {   
        require $this->documentRoot.'config/connection.config.php';
        require $this->documentRoot.'config/utility.config.php'; 
        switch ($slug) {
            case 'akses_jenis_user':
                $sql = "SELECT * FROM bsis_jenis_user WHERE id = '$id'";
                break;
            case 'akses_user':
                $sql = "SELECT namapegawai as nama FROM pegawai WHERE idpegawai = '$id'";
                break;
        }
        $data = $connection->query($sql)->fetch_object();
        return $data;
    }

    public function get_group_menu()
    {   
        require $this->documentRoot.'config/connection.config.php';
        require $this->documentRoot.'config/utility.config.php'; 
        $sql = "SELECT * FROM bsis_group_menu_aplikasi WHERE parent_id IS NULL AND deleted_at IS NULL AND nama <> 'Super Admin'";
        $query = $connection->query($sql);
        return $query;
    }

    public function get_menu($id)
    {   
        require $this->documentRoot.'config/connection.config.php';
        require $this->documentRoot.'config/utility.config.php'; 
        $sql = "SELECT * FROM bsis_menu_aplikasi WHERE id_group = '$id' AND deleted_at IS NULL";
        $query = $connection->query($sql);
        return $query;
    }

    public function cek_child($id)
    {   
        require $this->documentRoot.'config/connection.config.php';
        require $this->documentRoot.'config/utility.config.php'; 
        $sql = "SELECT * FROM bsis_group_menu_aplikasi WHERE parent_id = '$id' AND deleted_at IS NULL";
        $jml = $connection->query($sql)->num_rows;
        return $jml;
    }

    public function cek_group($id)
    {   
        require $this->documentRoot.'config/connection.config.php';
        require $this->documentRoot.'config/utility.config.php'; 
        $sql = "SELECT * FROM bsis_group_menu_aplikasi WHERE id = '$id' AND deleted_at IS NULL";
        $data = $connection->query($sql)->fetch_object();
        return $data;
    }

    public function get_sub_group_menu($id)
    {   
        require $this->documentRoot.'config/connection.config.php';
        require $this->documentRoot.'config/utility.config.php'; 
        $sql = "SELECT * FROM bsis_group_menu_aplikasi WHERE parent_id = '$id' AND deleted_at IS NULL";
        $query = $connection->query($sql);
        return $query;
    }

    public function get_mapping($id, $slug)
    {   
        require $this->documentRoot.'config/connection.config.php';
        require $this->documentRoot.'config/utility.config.php';
        if($slug == 'akses_jenis_user') {
            $sql = "SELECT
                bsis_mapping_hak_akses_jenis_user.id,
                bsis_menu_aplikasi.id as id_menu_aplikasi,
                bsis_menu_aplikasi.nama,
                bsis_group_menu_aplikasi.nama nama_group,
                bsis_mapping_hak_akses_jenis_user.create,
                bsis_mapping_hak_akses_jenis_user.update,
                bsis_mapping_hak_akses_jenis_user.delete
            FROM
                bsis_mapping_hak_akses_jenis_user
                INNER JOIN bsis_menu_aplikasi ON bsis_menu_aplikasi.id = bsis_mapping_hak_akses_jenis_user.id_menu_aplikasi AND bsis_menu_aplikasi.deleted_at IS NULL
                INNER JOIN bsis_group_menu_aplikasi ON bsis_menu_aplikasi.id_group = bsis_group_menu_aplikasi.id AND bsis_group_menu_aplikasi.deleted_at IS NULL
            WHERE
                id_jenis_user = '$id' AND bsis_mapping_hak_akses_jenis_user.`group` <> 0
            UNION ALL
            SELECT
                bsis_mapping_hak_akses_jenis_user.id,
                bsis_group_menu_aplikasi.id as id_menu_aplikasi,
                bsis_group_menu_aplikasi.nama,
                '' nama_group,
                bsis_mapping_hak_akses_jenis_user.create,
                bsis_mapping_hak_akses_jenis_user.update,
                bsis_mapping_hak_akses_jenis_user.delete
            FROM
                bsis_mapping_hak_akses_jenis_user
                INNER JOIN bsis_group_menu_aplikasi ON bsis_group_menu_aplikasi.id = bsis_mapping_hak_akses_jenis_user.id_menu_aplikasi AND bsis_group_menu_aplikasi.deleted_at IS NULL
            WHERE
                id_jenis_user = '$id' AND bsis_mapping_hak_akses_jenis_user.`group` = 0";
        }elseif('akses_user'){
            $sql = "SELECT
                    bsis_mapping_hak_akses_user.id,
                    bsis_menu_aplikasi.id AS id_menu_aplikasi,
                    bsis_menu_aplikasi.nama,
                    bsis_group_menu_aplikasi.nama nama_group,
                    bsis_mapping_hak_akses_user.create,
                    bsis_mapping_hak_akses_user.update,
                    bsis_mapping_hak_akses_user.delete 
            FROM
                bsis_mapping_hak_akses_user
                INNER JOIN bsis_menu_aplikasi ON bsis_menu_aplikasi.id = bsis_mapping_hak_akses_user.id_menu_aplikasi AND bsis_menu_aplikasi.deleted_at IS NULL
                INNER JOIN bsis_group_menu_aplikasi ON bsis_menu_aplikasi.id_group = bsis_group_menu_aplikasi.id AND bsis_group_menu_aplikasi.deleted_at IS NULL
            WHERE
                id_pegawai = '$id' 
                AND bsis_mapping_hak_akses_user.`group` <> 0 UNION ALL
            SELECT
                bsis_mapping_hak_akses_user.id,
                bsis_group_menu_aplikasi.id AS id_menu_aplikasi,
                bsis_group_menu_aplikasi.nama,
                '' nama_group,
                bsis_mapping_hak_akses_user.create,
                bsis_mapping_hak_akses_user.update,
                bsis_mapping_hak_akses_user.delete 
            FROM
                bsis_mapping_hak_akses_user
                INNER JOIN bsis_group_menu_aplikasi ON bsis_group_menu_aplikasi.id = bsis_mapping_hak_akses_user.id_menu_aplikasi  AND bsis_group_menu_aplikasi.deleted_at IS NULL
            WHERE
                id_pegawai = '$id' 
                AND bsis_mapping_hak_akses_user.`group` = 0";
        }
        $query = $connection->query($sql);
        return $query;
    }
}
?>