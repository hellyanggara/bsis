<?php
$codeSource = 'bsis';
$documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
require $documentRoot.'config/mssql.config.php';

class REGISTRASI
{
    private $documentRoot;
    private $db;
    
    function __construct() 
    {
        $codeSource = 'bsis';
        $documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
        signInCheck();
        $this->documentRoot = $documentRoot;
        $this->db = Database::getInstance()->getConnection();
    }

    public function getRuangan()
    {   
        require $this->documentRoot.'config/connection.config.php';
        require $this->documentRoot.'config/utility.config.php'; 
        $sql = "SELECT * FROM Ruangan WHERE KdInstalasi = '03'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function get_data_Ruangan($kdRuangan)
    {   
        require $this->documentRoot.'config/connection.config.php';
        require $this->documentRoot.'config/utility.config.php'; 
        $sql = "SELECT * FROM Ruangan WHERE KdRuangan = :kdRuangan ORDER BY NamaRuangan";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':kdRuangan', $kdRuangan);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function get_data($kdRuangan = null, $status = null, $tglAwal = null, $tglAkhir = null)
    {   
        require $this->documentRoot.'config/connection.config.php';
        require $this->documentRoot.'config/utility.config.php'; 
        
        $sql = "SELECT f.*, p.NamaLengkap, 
            dbo.S_HitungUmur (
                    p.TglLahir,
            getdate()) AS Umur,
            r.NamaRuangan ,
            dp.NamaLengkap as dokter_operator,
            dp1.NamaLengkap as dokter_anestesi
            FROM FormJadwalOperasi f 
            JOIN Pasien p ON p.NoCM = f.NoCM
            JOIN Ruangan r ON r.KdRuangan = f.KdRuangan
            LEFT JOIN DataPegawai dp ON dp.IdPegawai = f.id_dokter
            LEFT JOIN DataPegawai dp1 ON dp1.IdPegawai = f.id_dokter_anestesi
            WHERE 1=1
        ";

        $params = [];

        // ================= FILTER RUANGAN
        if (!empty($kdRuangan)) {
            $sql .= " AND f.KdRuangan = :kdRuangan";
            $params[':kdRuangan'] = $kdRuangan;
        }

        // ================= FILTER STATUS
        if (!empty($status) || $status === '0') {
            $sql .= " AND f.status = :status";
            $params[':status'] = $status;
        }

        // ================= FILTER TANGGAL =================
        if (empty($tglAwal) && empty($tglAkhir)) {

            // default hari ini (SERVER TIME)
            $sql .= " 
                AND f.tanggal >= CAST(GETDATE() AS DATE)
                AND f.tanggal <  DATEADD(DAY,1,CAST(GETDATE() AS DATE))
            ";

        } else {

            // convert dd/mm/YYYY -> YYYY-mm-dd
            if (!empty($tglAwal)) {
                $d = DateTime::createFromFormat('d/m/Y', $tglAwal);
                $tglAwalDB = $d->format('Y-m-d');
            }

            if (!empty($tglAkhir)) {
                $d = DateTime::createFromFormat('d/m/Y', $tglAkhir);
                $tglAkhirDB = $d->format('Y-m-d');
            }

            // hanya satu tanggal
            if (!empty($tglAwal) && empty($tglAkhir)) {
                $tglAkhirDB = $tglAwalDB;
            }

            if (empty($tglAwal) && !empty($tglAkhir)) {
                $tglAwalDB = $tglAkhirDB;
            }

            $sql .= "
                AND f.tanggal >= :tglAwal
                AND f.tanggal < DATEADD(DAY,1,:tglAkhir)
            ";

            $params[':tglAwal']  = $tglAwalDB;
            $params[':tglAkhir'] = $tglAkhirDB;
        }

        // ==================================================

        $sql .= " ORDER BY f.TglInput DESC";
        // echo $sql;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }



    public function get_detail($id)
    {   
        require $this->documentRoot.'config/connection.config.php';
        require $this->documentRoot.'config/utility.config.php'; 
        $sql = "SELECT TOP 1
                f.*,

                -- Pasien
                p.NamaLengkap,
                p.Title,
                p.NoCM,
                p.JenisKelamin,
                p.TglLahir,
                dbo.S_HitungUmur(p.TglLahir, GETDATE()) AS Umur,

                -- Pendaftaran
                pd.NoPendaftaran,
                pd.TglPendaftaran,
                pd.KdRuanganAkhir,

                -- Ruangan
                r.NamaRuangan,

                -- Kelas & Penjamin
                kp.DeskKelas,
                pj.NamaPenjamin

            FROM FormJadwalOperasi f
            JOIN Pasien p 
                ON p.NoCM = f.NoCM

            LEFT JOIN PasienDaftar pd 
                ON pd.NoPendaftaran = f.NoPendaftaran

            LEFT JOIN Ruangan r 
                ON r.KdRuangan = pd.KdRuanganAkhir

            LEFT JOIN KelasPelayanan kp 
                ON kp.KdKelas = pd.KdKelasAkhir

            LEFT JOIN Penjamin pj 
                ON pj.IdPenjamin = pd.IdPenjamin

            WHERE f.id = :id
            ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function simpanStatus($data)
    {
        try {
            $this->db->beginTransaction();

            $id  = $data['id'] ?? null;
            $status = $data['status'] ?? null;
            $alasan = $data['alasan'] ?? null;

            if (!$id || !$status) {
                throw new Exception('Data tidak lengkap');
            }

            $sql = "UPDATE FormJadwalOperasi
                    SET status = :status, alasan = :alasan
                    WHERE id = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':alasan', $alasan);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if (!$stmt->execute()) {
                throw new Exception(
                    'Gagal melakukan verifikasi: ' . implode(' | ', $stmt->errorInfo())
                );
            }

            $this->db->commit();
            if($status == 1){
                return [
                    'status'  => 'success',
                    'message' => '<span class="badge badge-success">Registrasi berhasil disetujui.</span>'
                ];
            }elseif($status == 2){
                return [
                    'status'  => 'success',
                    'message' => '<span class="badge badge-success">Registrasi berhasil ditolak.</span>'
                ];
            }

        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            return [
                'status'  => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

}
