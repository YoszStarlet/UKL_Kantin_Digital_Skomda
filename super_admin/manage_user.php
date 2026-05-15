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

// memvalidasi pengiriman data melalui metode GET untuk hapus user
if (isset($_GET['hapus'])) {

    // inisialisasi variabel id user yang akan dihapus
    $id_hapus = $_GET['hapus'];

    // menjalankan instruksi query untuk menghapus data di tabel detail_transaksi terlebih dahulu
    mysqli_query($conn, "DELETE FROM detail_transaksi WHERE id_transaksi IN (SELECT id_transaksi FROM transaksi WHERE id_user = '$id_hapus')");

    // menjalankan instruksi query untuk menghapus data transaksi milik user tersebut
    mysqli_query($conn, "DELETE FROM transaksi WHERE id_user = '$id_hapus'");

    // menjalankan instruksi query untuk menghapus data user dengan role siswa di database
    $query_hapus = mysqli_query($conn, "DELETE FROM user WHERE id_user = '$id_hapus' AND role = 'siswa'");
    
    // validasi jika instruksi query hapus berhasil dijalankan
    if ($query_hapus) {

        // menampilkan notifikasi pesan melalui javascript dan mengalihkan halaman
        echo "<script>alert('User dan seluruh riwayat transaksinya berhasil dihapus!'); window.location='manage_user.php';</script>";
    } else {

        // inisialisasi variabel pesan error dari sistem database
        $pesan_error = mysqli_error($conn);

        // menampilkan notifikasi kegagalan hapus beserta pesan error database
        echo "<script>alert('Gagal menghapus user! Error: $pesan_error');</script>";
    }
}

// menyusun instruksi query untuk mengambil data seluruh user dengan role siswa
$data_siswa = mysqli_query($conn, "SELECT * FROM user WHERE role = 'siswa' ORDER BY id_user DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manage User - Super Admin</title>
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
        <h2>Manajemen Data Siswa</h2>
        <p>Daftar seluruh siswa yang terdaftar di aplikasi Kantin Skomda.</p>

        <div style="margin-top: 20px;">
            <table border="1" style="width: 100%; border-collapse: collapse; background: white;">
                <thead>
                    <tr style="background: #eee;">
                        <th style="padding: 10px;">ID User</th>
                        <th style="padding: 10px;">Username</th>
                        <th style="padding: 10px;">Role</th>
                        <th style="padding: 10px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    
                    // memvalidasi apakah terdapat data siswa dalam database
                    if(mysqli_num_rows($data_siswa) > 0) : ?>
                        <?php 
                        
                        // melakukan perulangan untuk mengambil data siswa hasil query menjadi array
                        while($u = mysqli_fetch_assoc($data_siswa)) : ?>
                        <tr>
                            <td align="center"><?php echo $u['id_user']; ?></td>

                            <td style="padding: 10px;"><?php echo $u['username']; ?></td>

                            <td align="center" style="padding: 10px;">
                                <span class="badge-siswa" style="background: #e3f2fd; color: #1976d2; padding: 5px 10px; border-radius: 5px;">Siswa</span>
                            </td>

                            <td align="center" style="padding: 10px;">
                                <a href="manage_user.php?hapus=<?php echo $u['id_user']; ?>" 
                                   onclick="return confirm('Yakin ingin menghapus user ini? Perhatian: Semua riwayat transaksi siswa ini juga akan dihapus permanen!')" 
                                   style="color: white; background: #ce1212; padding: 5px 10px; text-decoration: none; border-radius: 5px; font-size: 0.8rem;">
                                   Hapus Akun
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="4" align="center" style="padding: 20px;">Belum ada siswa yang mendaftar.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<?php
// menghubungkan file ke komponen footer
include '../includes/footer.php';
?>