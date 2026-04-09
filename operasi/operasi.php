<?php
$codeSource = 'bsis';
$documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
require $documentRoot.'config/mssql.config.php';

class OPERASI
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
    public function getDokter()
    {
        $sql = "SELECT * FROM V_DaftarDokter";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function get_data()
    {   
        require $this->documentRoot.'config/connection.config.php';
        require $this->documentRoot.'config/utility.config.php'; 
        $sql = "SELECT f.*, p.NamaLengkap, 
            dbo.S_HitungUmur (
                    p.TglLahir,
            getdate()) AS Umur,
            r.NamaRuangan ,
            dp.NamaLengkap as dokter_operator,
            CASE
                WHEN id_dokter_anestesi = '0000000000' THEN
                'Anestesi Lokal' ELSE dp1.NamaLengkap 
            END AS dokter_anestesi 
            FROM FormJadwalOperasi f 
            JOIN Pasien p ON p.NoCM = f.NoCM
            JOIN Ruangan r ON r.KdRuangan = f.KdRuangan
            LEFT JOIN DataPegawai dp ON dp.IdPegawai = f.id_dokter
            LEFT JOIN DataPegawai dp1 ON dp1.IdPegawai = f.id_dokter_anestesi
            WHERE status = 1 ORDER BY f.TglInput DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

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

    public function getListDokter() {
        $query = "SELECT KodeDokter, NamaDokter FROM V_DaftarDokterSpesialis ORDER BY NamaDokter";
        $stmt = $this->db->prepare($query);
        $stmt->execute(); 
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public function simpanJam($data)
    {
        try {
            $this->db->beginTransaction();

            $id  = $data['id'] ?? null;
            $jam = $data['jam_mulai_op'] ?? null;

            if (!$id || !$jam) {
                throw new Exception('Data tidak lengkap');
            }

            $sql = "UPDATE FormJadwalOperasi
                    SET jam_mulai_op = :jam, jam_mulai_op_pengisian = GETDATE()
                    WHERE id = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':jam', $jam);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if (!$stmt->execute()) {
                throw new Exception(
                    'Gagal simpan jam: ' . implode(' | ', $stmt->errorInfo())
                );
            }

            $this->db->commit();

            return [
                'status'  => 'success',
                'message' => '<span class="badge badge-success">Jam Mulai Op berhasil disimpan.</span>'
            ];

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

    public function simpanMskKamar($data)
    {
        try {
            $this->db->beginTransaction();

            $id  = $data['id'] ?? null;
            $jam = $data['jam_masuk_kamar'] ?? null;

            if (!$id || !$jam) {
                throw new Exception('Data tidak lengkap');
            }

            $sql = "UPDATE FormJadwalOperasi
                    SET jam_masuk_kamar = :jam, jam_masuk_kamar_pengisian = GETDATE()
                    WHERE id = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':jam', $jam);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if (!$stmt->execute()) {
                throw new Exception(
                    'Gagal simpan jam: ' . implode(' | ', $stmt->errorInfo())
                );
            }

            $this->db->commit();

            return [
                'status'  => 'success',
                'message' => '<span class="badge badge-success">Jam Mulai Op berhasil disimpan.</span>'
            ];

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

    public function simpanOperator($data)
    {
        try {
            $this->db->beginTransaction();

            $id  = $data['id'] ?? null;
            $id_dokter = $data['id_dokter'] ?? null;

            if (!$id || !$id_dokter) {
                throw new Exception('Data tidak lengkap');
            }

            $sql = "UPDATE FormJadwalOperasi
                    SET id_dokter = :id_dokter
                    WHERE id = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_dokter', $id_dokter);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if (!$stmt->execute()) {
                throw new Exception(
                    'Gagal simpan Dokter Operator: ' . implode(' | ', $stmt->errorInfo())
                );
            }

            $this->db->commit();

            return [
                'status'  => 'success',
                'message' => '<span class="badge badge-success">Dokter Operator berhasil disimpan.</span>'
            ];

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

    public function simpanAnestesi($data)
    {
        try {
            $this->db->beginTransaction();

            $id  = $data['id'] ?? null;
            $id_dokter_anestesi = $data['id_dokter_anestesi'] ?? null;

            if (!$id || !$id_dokter_anestesi) {
                throw new Exception('Data tidak lengkap');
            }

            $sql = "UPDATE FormJadwalOperasi
                    SET id_dokter_anestesi = :id_dokter_anestesi, id_dokter_anestesi_pengisian = GETDATE()
                    WHERE id = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_dokter_anestesi', $id_dokter_anestesi);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if (!$stmt->execute()) {
                throw new Exception(
                    'Gagal simpan Dokter Anestesi: ' . implode(' | ', $stmt->errorInfo())
                );
            }

            $this->db->commit();

            return [
                'status'  => 'success',
                'message' => '<span class="badge badge-success">Dokter Anestesi berhasil disimpan.</span>'
            ];

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
