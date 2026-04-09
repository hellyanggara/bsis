<?php
@session_start();
class USER
{
    private $documentRoot;
    function __construct() 
    {
        $codeSource = 'bsis';
        $documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
        signInCheck();
        $this->documentRoot = $documentRoot;
    }

    public function cek_table($table, $id, $slug = false, $primaryId = false, $whereDeletedNull =false)
    {   
        require $this->documentRoot.'config/connection.config.php';
        require $this->documentRoot.'config/utility.config.php'; 
        if($slug){
            $whereId = "WHERE slug = '$slug'";
        }else{
            if($primaryId){
                $whereId = "WHERE $primaryId = '$id'";
            }else{
                $whereId = "WHERE id = '$id'";
            }
        }
        if($whereDeletedNull){
            $whereDeletedNull = "";
        }else{
            $whereDeletedNull = "AND $table.deleted_at IS NULL";
        }
        $sql = "SELECT * FROM $table $whereId $whereDeletedNull";
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

    public function get_data_user_jenis_akses($id = false)
    {   
        require $this->documentRoot.'config/connection.config.php';
        require $this->documentRoot.'config/utility.config.php'; 
        if($id){
            $whereId = "WHERE id_pegawai = '$id'";
        }else{
            $whereId = "";
        }
        $sql = "SELECT bsis_mapping_user.id_pegawai as primary_id, bsis_mapping_user.id_jenis_user, pegawai.namapegawai, bsis_jenis_user.nama as addcol_value FROM bsis_mapping_user LEFT JOIN pegawai ON pegawai.idpegawai = bsis_mapping_user.id_pegawai LEFT JOIN bsis_jenis_user ON bsis_jenis_user.id = bsis_mapping_user.id_jenis_user $whereId";
        if($id){
            $data = $connection->query($sql)->fetch_object();
            return $data;
        }else{
            $query = $connection->query($sql);
            return $query;
        }
    }

    public function get_data_user_bagian($id = false)
    {   
        require $this->documentRoot.'config/connection.config.php';
        require $this->documentRoot.'config/utility.config.php'; 
        if($id){
            $whereId = "WHERE id_pegawai = '$id'";
        }else{
            $whereId = "";
        }
        $sql = "SELECT
            bsis_mapping_bagian.idmapping as primary_id,
            bsis_mapping_bagian.id_pegawai,
            bsis_mapping_bagian.kd_bagian,
            pegawai.namapegawai,
            bagian.namabagian as addcol_value
        FROM
            bsis_mapping_bagian
            INNER JOIN pegawai ON pegawai.idpegawai = bsis_mapping_bagian.id_pegawai
            INNER JOIN bagian ON bagian.kdbagian = bsis_mapping_bagian.kd_bagian $whereId";
        if($id){
            $data = $connection->query($sql)->fetch_object();
            return $data;
        }else{
            $query = $connection->query($sql);
            return $query;
        }
    }

    public function get_pegawai($slug)
    {   
        require $this->documentRoot.'config/connection.config.php';
        require $this->documentRoot.'config/utility.config.php';
        if($slug == 'mapping_user'){
            $whereNot = "WHERE pegawai.idpegawai NOT IN (SELECT id_pegawai FROM bsis_mapping_user)";
        }else{
            $whereNot = "";
        }
        $sql = "SELECT
            pegawai.idpegawai,
            pegawai.namapegawai
        FROM
            pegawai
            INNER JOIN aksespegawai ON aksespegawai.idpegawai = pegawai.idpegawai AND aksespegawai.idjenisakses IN ('BSIS1', 'BSIS2') $whereNot";
        $query = $connection->query($sql);
        return $query;
    }

    public function get_jenis_user()
    {   
        require $this->documentRoot.'config/connection.config.php';
        require $this->documentRoot.'config/utility.config.php';
        $sql = "SELECT id, nama FROM bsis_jenis_user WHERE deleted_at IS NULL AND id <> 1";
        $query = $connection->query($sql);
        return $query;
    }

    public function get_bagian_keuangan()
    {   
        require $this->documentRoot.'config/connection.config.php';
        require $this->documentRoot.'config/utility.config.php';
        $sql = "SELECT bsis_struktur_organisasi.kd_bagian as id, bagian.namabagian as nama FROM bsis_struktur_organisasi INNER JOIN bagian ON bagian.kdbagian = bsis_struktur_organisasi.kd_bagian WHERE deleted_at IS NULL";
        $query = $connection->query($sql);
        return $query;
    }

}
?>