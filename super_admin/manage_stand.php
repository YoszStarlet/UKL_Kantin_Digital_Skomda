<?php
session_start();
include '../config/koneksi.php';

// Proteksi Super Admin
if ($_SESSION['role'] != "super_admin") {
    header("location:../login.php");
    exit;
}

// LOGIKA TAMBAH STAND
if (isset($_POST['simpan_stand'])) {
    $nama_stand = $_POST['nama_stand'];
    $username   = $_POST['username'];
    $password   = md5($_POST['password']);
    
    // Upload Foto QRIS
    $foto_qris = $_FILES['foto_qris']['name'];
    $tmp       = $_FILES['foto_qris']['tmp_name'];
    $path      = "../assets/img/" . $foto_qris;

    if (move_uploaded_file($tmp, $path)) {
        // 1. Masukkan ke tabel stand
        $query_stand = mysqli_query($conn, "INSERT INTO stand (nama_stand, foto_qris) VALUES ('$nama_stand', '$foto_qris')");
        
        // 2. Ambil ID_STAND yang barusan dibuat (karena A_I)
        $id_baru = mysqli_insert_id($conn);

        // 3. Buat Akun Login di tabel user
        $query_user = mysqli_query($conn, "INSERT INTO user (username, password, role, id_stand) VALUES ('$username', '$password', 'admin', '$id_baru')");

        if ($query_user) {
            echo "<script>alert('Stand dan Akun Admin berhasil dibuat!'); window.location='manage_stand.php';</script>";
        }
    }
}

// Ambil data untuk tabel
$data_stand = mysqli_query($conn, "SELECT * FROM stand");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manage Stand - Super Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="main-nav">
        <div class="nav-brand"><h1>SUPER ADMIN SKOMDA</h1></div>
        <div class="nav-menu">
            <a href="dashboard.php" class="nav-link">Dashboard</a>
            <a href="manage_stand.php" class="nav-link">Manage Stand</a>
            <a href="../logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>

    <div class="admin-container">
        <h2>Kelola Stand Kantin</h2>
        
        <div class="form-card" style="margin-top: 20px; background: #f9f9f9; padding: 20px; border-radius: 10px;">
            <h3>Tambah Stand & Akun Baru</h3>
            <form method="POST" enctype="multipart/form-data" style="margin-top: 15px;">
                <input type="text" name="nama_stand" placeholder="Nama Stand (Contoh: Stand Jus Buah)" required style="width: 100%; margin-bottom: 10px; padding: 8px;">
                <input type="text" name="username" placeholder="Username untuk Login Admin" required style="width: 100%; margin-bottom: 10px; padding: 8px;">
                <input type="password" name="password" placeholder="Password Admin" required style="width: 100%; margin-bottom: 10px; padding: 8px;">
                <label>Foto QRIS:</label>
                <input type="file" name="foto_qris" required style="margin-bottom: 10px;">
                <button type="submit" name="simpan_stand" class="btn-simpan">Simpan Stand & Akun</button>
            </form>
        </div>

        <div style="margin-top: 30px;">
            <h3>Daftar Stand Saat Ini</h3>
            <table border="1" style="width: 100%; border-collapse: collapse; margin-top: 10px; background: white;">
                <tr style="background: #eee;">
                    <th>ID</th>
                    <th>Nama Stand</th>
                    <th>Foto QRIS</th>
                </tr>
                <?php while($s = mysqli_fetch_assoc($data_stand)) : ?>
                <tr>
                    <td align="center"><?php echo $s['id_stand']; ?></td>
                    <td><?php echo $s['nama_stand']; ?></td>
                    <td align="center"><img src="../assets/img/<?php echo $s['foto_qris']; ?>" width="50"></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>