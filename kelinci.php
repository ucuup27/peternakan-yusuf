<?php
// --- APLIKASI PETERNAKAN YUSUF V4.0 (STATUS & RIWAYAT) ---
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
// cek apkah punya tiket login?
if (!isset($_SESSION['sudah_login'])) {
	header("Location: login.php"); // tendang ke halaman login
	exit;
}

$koneksi = new mysqli("localhost", "admin_yusuf", "rahasia", "peternakan_yusuf");
if ($koneksi->connect_error) { die("Koneksi Gagal: " . $koneksi->connect_error); }

// --- FUNGSI HITUNG UMUR ---
function hitung_umur($tanggal_lahir) {
    if ($tanggal_lahir == '0000-00-00' || $tanggal_lahir == NULL) return "-";
    $lahir = new DateTime($tanggal_lahir);
    $hari_ini = new DateTime("today");
    if ($lahir > $hari_ini) return "Masa Depan";
    $umur = $hari_ini->diff($lahir);
    
    if ($umur->y > 0) return $umur->y . " Thn " . $umur->m . " Bln";
    if ($umur->m > 0) return $umur->m . " Bln " . $umur->d . " Hari";
    return $umur->d . " Hari";
}

// --- LOGIKA UPDATE STATUS (JUAL / MATI / KEMBALIKAN) ---
if (isset($_GET['aksi']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $aksi = $_GET['aksi'];
    
    // Tentukan status baru berdasarkan tombol yg dipencet
    $status_baru = 'Hidup';
    if ($aksi == 'jual') $status_baru = 'Terjual';
    if ($aksi == 'mati') $status_baru = 'Mati';
    if ($aksi == 'hidup') $status_baru = 'Hidup'; // Restore
    
    $koneksi->query("UPDATE kelinci SET status='$status_baru' WHERE id='$id'");
    echo "<script>window.location='kelinci.php';</script>";
}

// --- LOGIKA TAMBAH DATA ---
if (isset($_POST['simpan'])) {
    $nama = $_POST['nama'];
    $tgl = $_POST['tanggal_lahir'];
    $kelamin = $_POST['kelamin'];
    $warna = $_POST['warna'];
    // Status default otomatis 'Hidup'
    $sql = "INSERT INTO kelinci (nama, tanggal_lahir, kelamin, warna, status) VALUES ('$nama', '$tgl', '$kelamin', '$warna', 'Hidup')";
    if ($koneksi->query($sql) === TRUE) { $pesan = "Data tersimpan!"; }
}

// --- SIAPKAN 2 QUERY (AKTIF & ARSIP) ---
// 1. Ambil yang HIDUP saja
$data_aktif = $koneksi->query("SELECT * FROM kelinci WHERE status='Hidup' ORDER BY id DESC");

// 2. Ambil yang MATI atau TERJUAL
$data_arsip = $koneksi->query("SELECT * FROM kelinci WHERE status!='Hidup' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sistem Ternak V4</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: sans-serif; background: #e9ecef; padding: 10px; margin: 0; }
        .card { background: white; max-width: 600px; margin: 15px auto; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h2, h3 { text-align: center; color: #444; margin-top: 0;}
        
        /* Form & Inputs */
        input, select { width: 100%; padding: 10px; margin: 5px 0 15px 0; border: 1px solid #ced4da; border-radius: 4px; box-sizing: border-box; }
        .btn-simpan { width: 100%; background: #28a745; color: white; border: none; padding: 12px; border-radius: 4px; font-weight: bold; cursor: pointer; }
        
        /* Tabel */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 14px; }
        td { padding: 10px; border-bottom: 1px solid #eee; vertical-align: middle; }
        
        /* Badges & Buttons */
        .badge { padding: 3px 8px; border-radius: 10px; font-size: 11px; color: white; display: inline-block; margin-top:2px;}
        .jantan { background: #007bff; } .betina { background: #e83e8c; }
        .sts-jual { background: #ffc107; color: black; } .sts-mati { background: #343a40; }
        
        .btn-aksi { text-decoration: none; padding: 5px 10px; border-radius: 4px; font-size: 18px; margin-right: 5px; }
        .btn-jual { background: #ffc107; } /* Kuning */
        .btn-mati { background: #343a40; color: white; } /* Hitam */
        .btn-restore { background: #17a2b8; color: white; font-size: 12px; } /* Biru Muda */
    </style>
</head>
<body>

<div class="card">
    <h2>🐰 Tambah Kelinci</h2>
    <?php if(isset($pesan)) { echo "<p style='color:green;text-align:center'>$pesan</p>"; } ?>
    <form method="post">
        <label>Nama & Tanggal Lahir</label>
        <div style="display:flex; gap:5px;">
            <input type="text" name="nama" placeholder="Nama" required style="flex:1">
            <input type="date" name="tanggal_lahir" required style="flex:1">
        </div>
        
        <label>Info Fisik</label>
        <div style="display:flex; gap:5px;">
            <select name="kelamin" style="flex:1">
                <option value="Jantan">Jantan</option>
                <option value="Betina">Betina</option>
            </select>
            <input type="text" name="warna" placeholder="Warna" required style="flex:1">
        </div>

        <button type="submit" name="simpan" class="btn-simpan">+ SIMPAN KE KANDANG</button>
    </form>
</div>

<div class="card">
    <h3>🏡 Kandang Aktif (<?php echo $data_aktif->num_rows; ?> Ekor)</h3>
    <table>
        <?php
        if ($data_aktif->num_rows > 0) {
            while($d = $data_aktif->fetch_assoc()) {
                $bg = ($d['kelamin'] == 'Jantan') ? 'jantan' : 'betina';
                echo "<tr>";
                echo "<td>
                        <b style='font-size:16px'>" . $d['nama'] . "</b><br>
                        <span class='badge $bg'>" . $d['kelamin'] . "</span>
                        <span style='color:gray; font-size:12px'> " . $d['warna'] . "</span><br>
                        <b style='color:#28a745; font-size:12px'>" . hitung_umur($d['tanggal_lahir']) . "</b>
                      </td>";
                
                // Tombol Aksi: Jual & Mati
                echo "<td style='text-align:right; width:100px;'>
                        <a href='?aksi=jual&id=" . $d['id'] . "' class='btn-aksi btn-jual' onclick=\"return confirm('Kelinci terjual?')\">💰</a>
                        <a href='?aksi=mati&id=" . $d['id'] . "' class='btn-aksi btn-mati' onclick=\"return confirm('Kelinci mati?')\">💀</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='2' style='text-align:center; color:gray'>Kandang Kosong</td></tr>";
        }
        ?>
    </table>
</div>

<div class="card" style="background: #f8f9fa; border: 1px dashed #ccc;">
    <h3>📜 Riwayat (Terjual / Mati)</h3>
    <table>
        <?php
        if ($data_arsip->num_rows > 0) {
            while($d = $data_arsip->fetch_assoc()) {
                // Tentukan warna badge status
                $cls = ($d['status'] == 'Terjual') ? 'sts-jual' : 'sts-mati';
                
                echo "<tr>";
                echo "<td>
                        <s style='color:gray'>" . $d['nama'] . "</s><br>
                        <span class='badge $cls'>" . $d['status'] . "</span>
                      </td>";
                
                // Tombol Restore (Kembalikan ke kandang)
                echo "<td style='text-align:right'>
                        <a href='?aksi=hidup&id=" . $d['id'] . "' class='btn-aksi btn-restore' onclick=\"return confirm('Kembalikan ke kandang?')\">♻️ Batal</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='2' style='text-align:center; color:gray'>Belum ada riwayat</td></tr>";
        }
        ?>
    </table>
</div>

<div class="header-app text-center">
    <h1>🐰 Smart Farm Yusuf</h1>
    <p class="mb-0">Sistem Manajemen Peternakan Digital</p>
    <br>
    <a href="logout.php" class="btn btn-sm btn-light text-success fw-bold">🚪 LOGOUT</a>
</div>

</body>
</html>

