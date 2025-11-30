<?php
session_start();
// Koneksi ke Database
$koneksi = new mysqli("localhost", "admin_yusuf", "rahasia", "peternakan_yusuf");

if (isset($_POST['masuk'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];

// --- KODINGAN AMAN (PREPARED STATEMENT) ---
        // 1. Siapkan kerangkanya dulu (pakai tanda tanya ?)
        $stmt = $koneksi->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
        
        // 2. Masukkan data user ke dalam kerangka (s = string/teks)
        $stmt->bind_param("ss", $user, $pass);
        
        // 3. Jalankan
        $stmt->execute();
        
        // 4. Ambil hasilnya
        $hasil = $stmt->get_result();

    // Cek apakah ada data yang cocok?
    if ($hasil->num_rows > 0) {
        $_SESSION['sudah_login'] = true;
        header("Location: kelinci.php");
        exit;
    } else {
        $error = "Username atau Password Salah!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Database</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow" style="width: 350px;">
        <h3 class="text-center text-primary">Login Admin</h3>
        <?php if(isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>

        <form method="post">
            <label>Username</label>
            <input type="text" name="username" class="form-control mb-2" required>

            <label>Password</label>
            <input type="password" name="password" class="form-control mb-3" required>

            <button type="submit" name="masuk" class="btn btn-primary w-100">LOGIN</button>
        </form>
    </div>
</body>
</html>
