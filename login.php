<?php

// mulai session buat simpen data login user
session_start();

// panggil file koneksi buat akses database
include 'config/koneksi.php';

// cek apakah tombol login sudah ditekan
if (isset($_POST['masuk'])) {

    // ambil data dari inputan form
    $username = $_POST['user'];
    $password = md5($_POST['pass']);

    // cari data user di database yang cocok dengan inputan
    $query = "SELECT * FROM user WHERE username='$username' AND password='$password'";
    $hasil = mysqli_query($conn, $query);
    $cek   = mysqli_num_rows($hasil);

    // jika data user ditemukan
    if ($cek > 0) {
        $data = mysqli_fetch_assoc($hasil);

        // simpan identitas penting user ke dalam session
        $_SESSION['id_user']  = $data['id_user'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['role']     = $data['role'];
        $_SESSION['id_stand']  = $data['id_stand'];
        $_SESSION['status']   = "login";

        // arahkan user sesuai denganrole nya
        // seleksi jalur login buat tiga role berbeda
        if ($data['role'] == "super_admin") {
            // arahkan super admin ke folder khusus manajemen seluruh kantin
            echo "<script>alert('selamat datang super admin!'); window.location='super_admin/dashboard.php';</script>";
        } else if ($data['role'] == "admin") {
            echo "<script>alert('selamat datang admin!'); window.location='admin/dashboard.php';</script>";
        } else {
            echo "<script>alert('selamat datang siswa!'); window.location='index.php';</script>";
        }
    } else {

        // kasih peringatan kalau username atau password salah
        echo "<script>alert('login gagal! periksa kembali akun anda');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kantin Pre-Order Skomda</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="login-page">

    <div class="login-container">
        <img src="assets/img/logo 1.svg" alt="Logo Skomda" class="login-logo">

        <h2 class="login-title">Kantin Pre-Order</h2>

        <form action="" method="POST" class="login-form">
            <div class="input-group">
                <input type="text" name="user" placeholder="Username" required>
            </div>
            <div class="input-group">
                <input type="password" name="pass" placeholder="Password" required>
            </div>
            <button type="submit" name="masuk" class="btn-login">Login</button>
            <p style="margin-top: 20px; font-size: 0.9rem; color: #666; text-align: center;">
                Belum punya akun? <a href="register.php" style="color: #ce1212; text-decoration: none; font-weight: bold;">Daftar di sini</a>
            </p>
        </form> 
        
    </div>

</body>

</html>