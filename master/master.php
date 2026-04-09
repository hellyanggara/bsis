<?php
@session_start();
class MASTER
{
    private $documentRoot;
    function __construct() 
    {
        $codeSource = 'bsis';
        $documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
        signInCheck();
        $this->documentRoot = $documentRoot;
    }

    public function cek_table($table, $id, $slug)
    {   
        require $this->documentRoot.'config/connection.config.php';
        require $this->documentRoot.'config/utility.config.php'; 
        if($id != '' && $slug == ''){
            $sql = "SELECT * FROM $table WHERE deleted_at IS NULL AND id = '$id'";
        }elseif($id == '' && $slug != ''){
            $sql = "SELECT * FROM $table WHERE deleted_at IS NULL AND slug = '$slug'";
        }
        $jml = $connection->query($sql)->num_rows;
        return $jml;
    }

    public function get_list($table, $trash = false, $select = false, $join = false)
    {   
        require $this->documentRoot.'config/connection.config.php';
        require $this->documentRoot.'config/utility.config.php'; 
        $colSel = $select ? $select : "*";
        $joined = $join ? $join : "";
        $whereNull = $trash ? "WHERE $table.deleted_at IS NOT NULL" : "WHERE $table.deleted_at IS NULL";
        $sql = "SELECT $colSel FROM $table $joined $whereNull";
        $query = $connection->query($sql);
        return $query;
    }

    public function get_list_menu($table, $trash = false, $join = false, $tableJoin = false, $primaryId = false, $foreignId = false, $select = false, $where = false, $order = false)
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
        if($trash == ''){
            $sql = "SELECT $select FROM $table $joined WHERE $table.deleted_at IS NULL $where $order";
        }else{
            $sql = "SELECT $select FROM $table $joined WHERE $table.deleted_at IS NOT NULL $where $order";
        }
        $query = $connection->query($sql);
        return $query;
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

    public function get_additional_column($table)
    {
        switch ($table) {
            case 'bsis_master_jenis_kegiatan':
                $arr = array(array("nama"=>"direktori","jenis"=>"input", "tipe"=>"text", "wajib"=>"1", "sql"=>""));
                break;
            case 'bsis_master_detail_kegiatan':
                $arr = array(array("nama"=>"direktori","jenis"=>"input", "tipe"=>"text", "wajib"=>"1", "sql"=>""));
                break;
            default:
                $arr= array();
                break;
        }
        return $arr;
    }
}
?>