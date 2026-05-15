<?php

// memulai session untuk menyimpan data login pengguna
session_start();

// menghubungkan file ke database atau komponen lain
include '../config/koneksi.php';

// jika konfigurasi koneksi menggunakan variabel lain, gunakan sebagai fallback
if (!isset($conn)) {
    if (isset($koneksi)) {
        $conn = $koneksi;
    } else {
        die("Koneksi database tidak tersedia.");
    }
}

// memeriksa hak akses pengguna apakah sebagai admin
if ($_SESSION['role'] != "admin") {

    // mengalihkan halaman ke lokasi yang ditentukan
    header("location:../login.php");

    // menghentikan eksekusi script
    exit;
}

// memeriksa apakah form tombol simpan telah ditekan
if (isset($_POST['simpan'])) {

    // mengambil input teks nama menu dari form
    $nama   = $_POST['nama'];

    // mengambil input harga dari form
    $harga  = $_POST['harga'];

    // mengambil input stok baru dari form
    $stok   = $_POST['stok'];

    // mengambil id stand milik admin dari data session
    $id_stand = $_SESSION['id_stand'];

    // mengambil nama file gambar yang diunggah
    $gambar = $_FILES['foto']['name'];

    // mengambil lokasi sementara file yang diunggah
    $tmp    = $_FILES['foto']['tmp_name'];

    // memindahkan file gambar dari lokasi sementara ke direktori tujuan
    if (move_uploaded_file($tmp, "../assets/img/" . $gambar)) {

        // menjalankan instruksi query ke database untuk memasukkan data menu baru dengan prepared statement
        $stmt = $conn->prepare("INSERT INTO menu (nama_menu, harga, stok, gambar, id_stand) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("siiis", $nama, $harga, $stok, $gambar, $id_stand);
        $query = $stmt->execute();

        // validasi keberhasilan proses penyimpanan data ke database
        if ($query) {
            echo "<script>alert('menu berhasil ditambah dengan stok!'); window.location='dashboard.php';</script>";
        } else {
            echo "<script>alert('gagal simpan ke database!');</script>";
        }
    } else {

        // validasi jika proses unggah file gambar ke server gagal
        echo "<script>alert('gagal upload gambar!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Menu Baru - Admin Skomda</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <nav class="main-nav">
        <div class="nav-brand">
            <h1>SKOMDA KANTIN</h1>
        </div>
        <div class="nav-menu">
            <a href="dashboard.php" class="nav-link">Kelola Menu</a>
            <span class="admin-name">Admin: <strong><?php echo $_SESSION['username']; ?></strong></span>
            <a href="../logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>

    <div class="admin-container">
        <div class="form-card">
            <div class="form-header">
                <h2>Tambah Menu Baru (Opsi Stok Aktif)</h2>
                <p>Input jumlah porsi yang tersedia hari ini.</p>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <div class="input-group">
                    <label>Nama Menu</label>
                    <input type="text" name="nama" placeholder="Misal: Kentung Goreng Spesial" required>
                </div>

                <div class="input-group">
                    <label>Harga (Rp)</label>
                    <input type="number" name="harga" placeholder="Contoh: 15000" required>
                </div>

                <div class="input-group">
                    <label>Stok Awal (Porsi)</label>
                    <input type="number" name="stok" placeholder="Contoh: 20" min="0" required>
                </div>

                <div class="input-group">
                    <label>Foto Menu</label>
                    <input type="file" name="foto" required>
                </div>

                <div class="form-actions">
                    <button type="submit" name="simpan" class="btn-simpan">Simpan Menu & Stok</button>
                    <a href="dashboard.php" class="btn-kembali">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
<?php
// menghubungkan file ke database
include '../includes/footer.php';
?>