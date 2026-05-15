<?php

// memulai session untuk menyimpan data login pengguna
session_start();

// menghubungkan file ke konfigurasi database
include '../config/koneksi.php';

// memeriksa hak akses pengguna apakah sebagai super_admin
if ($_SESSION['role'] != "super_admin") {

    // mengalihkan halaman ke lokasi yang ditentukan
    header("location:../login.php");

    // menghentikan eksekusi script
    exit;
}

// memvalidasi pengiriman data melalui metode GET untuk hapus stand
if (isset($_GET['hapus'])) {

    // inisialisasi variabel id stand yang akan dihapus
    $id_hapus = $_GET['hapus'];

    // menjalankan instruksi query untuk menghapus akun admin yang terikat dengan stand
    mysqli_query($conn, "DELETE FROM user WHERE id_stand = '$id_hapus'");

    // menjalankan instruksi query untuk menghapus data menu milik stand tersebut
    mysqli_query($conn, "DELETE FROM menu WHERE id_stand = '$id_hapus'");

    // menjalankan instruksi query untuk menghapus data stand utama di database
    $query_hapus = mysqli_query($conn, "DELETE FROM stand WHERE id_stand = '$id_hapus'");

    // validasi jika instruksi query hapus berhasil dijalankan
    if ($query_hapus) {

        // menampilkan notifikasi pesan melalui javascript dan mengalihkan halaman
        echo "<script>alert('Stand dan akun admin berhasil dihapus!'); window.location='manage_stand.php';</script>";
    } else {

        // menampilkan notifikasi kegagalan hapus beserta pesan error database
        echo "<script>alert('Gagal menghapus stand: " . mysqli_error($conn) . "');</script>";
    }
}

// memvalidasi pengiriman data melalui metode POST untuk simpan stand baru
if (isset($_POST['simpan_stand'])) {

    // inisialisasi variabel dari data inputan form
    $nama_stand = $_POST['nama_stand'];
    $username   = $_POST['username'];

    // mengenkripsi password menggunakan fungsi md5
    $password   = md5($_POST['password']);
    
    // inisialisasi variabel untuk manajemen file gambar qris
    $foto_qris = $_FILES['foto_qris']['name'];
    $tmp       = $_FILES['foto_qris']['tmp_name'];
    $path      = "../assets/img/" . $foto_qris;

    // menjalankan instruksi untuk memindahkan file gambar ke folder tujuan
    if (move_uploaded_file($tmp, $path)) {

        // menjalankan instruksi query insert untuk menambah data stand
        mysqli_query($conn, "INSERT INTO stand (nama_stand, foto_qris) VALUES ('$nama_stand', '$foto_qris')");
        
        // mengambil id_stand yang baru saja dibuat secara otomatis
        $id_baru = mysqli_insert_id($conn);

        // menjalankan instruksi query insert untuk membuat akun admin stand baru
        mysqli_query($conn, "INSERT INTO user (username, password, role, id_stand) VALUES ('$username', '$password', 'admin', '$id_baru')");

        // menampilkan notifikasi keberhasilan tambah data dan mengalihkan halaman
        echo "<script>alert('Stand berhasil dibuat!'); window.location='manage_stand.php';</script>";
    }
}

// menyusun instruksi query untuk mengambil seluruh data stand
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
        <div class="nav-brand"><h1>SUPER ADMIN</h1></div>
        <div class="nav-menu">
            <a href="dashboard.php" class="nav-link">Dashboard</a>
            <a href="manage_stand.php" class="nav-link">Manage Stand</a>
            <a href="manage_user.php" class="nav-link">Manage User</a>
            <a href="../logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>

    <div class="admin-container">
        <h2>Kelola Stand Kantin</h2>
        
        <div class="form-card" style="margin-top: 20px; background: #f9f9f9; padding: 20px; border-radius: 10px;">
            <h3>Tambah Stand Baru</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="text" name="nama_stand" placeholder="Nama Stand" required style="width: 100%; margin-bottom: 10px; padding: 8px;">
                <input type="text" name="username" placeholder="Username Admin" required style="width: 100%; margin-bottom: 10px; padding: 8px;">
                <input type="password" name="password" placeholder="Password Admin" required style="width: 100%; margin-bottom: 10px; padding: 8px;">
                <input type="file" name="foto_qris" required style="margin-bottom: 10px;">
                <button type="submit" name="simpan_stand" class="btn-simpan">Simpan</button>
            </form>
        </div>

        <div style="margin-top: 30px;">
            <h3>Daftar Stand Saat Ini</h3>
            <table border="1" style="width: 100%; border-collapse: collapse; margin-top: 10px; background: white;">
                <tr style="background: #eee;">
                    <th style="padding:10px;">ID</th>
                    <th>Nama Stand</th>
                    <th>Foto QRIS</th>
                    <th>Aksi</th>
                </tr>
                <?php 
                
                // melakukan perulangan untuk mengambil data stand hasil query menjadi array
                while($s = mysqli_fetch_assoc($data_stand)) : ?>
                <tr>
                    <td align="center"><?php echo $s['id_stand']; ?></td>

                    <td style="padding:10px;"><?php echo $s['nama_stand']; ?></td>

                    <td align="center"><img src="../assets/img/<?php echo $s['foto_qris']; ?>" width="50"></td>

                    <td align="center">
                        <a href="manage_stand.php?hapus=<?php echo $s['id_stand']; ?>" 
                           onclick="return confirm('Peringatan! Menghapus stand ini akan menghapus akun admin dan semua menu terkait. Lanjutkan?')"
                           style="background: #ce1212; color: white; padding: 5px 10px; text-decoration: none; border-radius: 5px; font-size: 0.8rem;">
                           Hapus Stand
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>

<?php
// menghubungkan file ke komponen footer
include '../includes/footer.php';
?>