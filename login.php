<?php
session_start();

// PASSWORD RAHASIA (Ganti sesuka hati)
$password_benar = "kelinci123";

if (isset($_POST['masuk'])) {
    $input_pass = $_POST['password'];
    if ($input_pass == $password_benar) {
        // Simpan tiket masuk (Session)
        $_SESSION['sudah_login'] = true;
        header("Location: kelinci.php"); // Pindah ke kandang
        exit;
    } else {
        $error = "Password Salah!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Peternakan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #e9ecef; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .card { width: 100%; max-width: 350px; border: none; shadow: 0 4px 8px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="card p-4">
        <h3 class="text-center text-success mb-4">🔒 Login Area</h3>

        <?php if(isset($error)) { echo "<div class='alert alert-danger py-2'>$error</div>"; } ?>

        <form method="post">
            <div class="mb-3">
                <label>Masukkan Password:</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" name="masuk" class="btn btn-success w-100">MASUK</button>
        </form>
    </div>
</body>
</html>
