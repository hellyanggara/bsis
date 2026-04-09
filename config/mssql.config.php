<?php
class Database {
    private static $instance = null;
    private $conn;

    // Tester
    private $host = "172.16.3.252\SS2008R2";
    private $db = "RSKD_TEST";
    private $user = "sa";
    private $pass = "@dbRSUDk4nuj0s0@";

    
    // Operational
    // private $host = "172.16.0.252\SS2008R2";
    // private $db = "RSKD";
    // private $user = "sa";
    // private $pass = "@dbRSUDk4nuj0s0@";

    private function __construct() {
        try {
            $this->conn = new PDO("sqlsrv:Server=$this->host;Database=$this->db", $this->user, $this->pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Koneksi gagal: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>
