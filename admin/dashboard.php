<?php

// memulai session untuk menyimpan data login pengguna
session_start();

// menghubungkan file ke database atau komponen lain
include '../config/koneksi.php';

// memeriksa koneksi database
if (!isset($conn) || !$conn) {
    die("Koneksi database gagal!");
}

// memeriksa hak akses pengguna apakah sebagai admin
if ($_SESSION['role'] != "admin") {

    // mengalihkan halaman ke lokasi yang ditentukan
    header("location:../login.php");

    // menghentikan eksekusi script
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Kantin Skomda</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <nav class="main-nav">
        <div class="nav-brand">
            <h1>DASHBOARD - ADMIN</h1>
        </div>
        <div class="nav-menu">
            <a href="dashboard.php" class="nav-link">Kelola Menu</a>
            <a href="pesanan_masuk.php" class="nav-link" style="background-color: #ce1212; padding: 5px 10px; border-radius: 4px;"> Pesanan Masuk</a>
            <a href="laporan.php" class="nav-link">Laporan</a>

            <span class="admin-name">Admin: <strong><?php echo $_SESSION['username']; ?></strong></span>
            <a href="../logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>

    <div class="admin-container">
        <div class="admin-header">
            <h2>Daftar Menu & Stok Stand</h2>
            <a href="tambah_menu.php" class="btn-tambah">+ Tambah Menu Baru</a>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Gambar</th>
                    <th>Nama Menu</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Opsi</th>
                </tr>
            </thead>
            <tbody>
                <?php

                // mengambil id stand milik admin dari data session
                $id_stand_admin = $_SESSION['id_stand'];

                // menyusun instruksi query untuk mengambil data menu berdasarkan id stand
                $query = "SELECT * FROM menu WHERE id_stand = '$id_stand_admin'";

                // menjalankan instruksi query ke database
                $ambildata = mysqli_query($conn, $query);

                // inisialisasi variabel nomor urut tabel
                $no = 1;

                // mengambil data dari hasil query menjadi array
                while ($data = mysqli_fetch_array($ambildata)) {

                    // inisialisasi variabel untuk styling warna teks stok
                    $warna_stok = "";

                    // validasi jika stok habis untuk memberikan warna merah
                    if ($data['stok'] == 0) {
                        $warna_stok = "color: red; font-weight: bold;";

                        // validasi jika stok menipis untuk memberikan warna oranye
                    } elseif ($data['stok'] < 5) {
                        $warna_stok = "color: orange; font-weight: bold;";
                    }
                ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td>
                            <img src="../assets/img/<?php echo $data['gambar']; ?>" alt="gambar menu" width="50">
                        </td>
                        <td><?php echo $data['nama_menu']; ?></td>
                        <td>Rp <?php echo number_format($data['harga'], 0, ',', '.'); ?></td>

                        <td style="<?php echo $warna_stok; ?>">
                            <?php echo $data['stok']; ?> Porsi
                            <?php if ($data['stok'] == 0) echo " (HABIS!)"; ?>
                        </td>

                        <td>
                            <a href="edit_menu.php?id=<?php echo $data['id_menu']; ?>" class="link-edit">Edit</a>
                            <a href="hapus_menu.php?id=<?php echo $data['id_menu']; ?>" class="link-hapus" onclick="return confirm('Yakin ingin hapus?')">Hapus</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <br>
    </div>
</body>

</html>
<?php
// menghubungkan file ke database
include '../includes/footer.php';
?>