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

// menyusun instruksi query untuk menghitung total data stand
$q_stand = mysqli_query($conn, "SELECT COUNT(*) as total FROM stand");

// mengambil hasil query penghitungan total stand
$t_stand = mysqli_fetch_assoc($q_stand)['total'];

// menyusun instruksi query untuk menghitung total data user dengan role siswa
$q_siswa = mysqli_query($conn, "SELECT COUNT(*) as total FROM user WHERE role='siswa'");

// mengambil hasil query penghitungan total siswa
$t_siswa = mysqli_fetch_assoc($q_siswa)['total'];

// menyusun instruksi query JOIN multi-tabel untuk menghitung total omzet per stand
$q_omzet_per_stand = mysqli_query($conn, "
    SELECT 
        s.nama_stand, 
        SUM(t.total_bayar) as total_omzet 
    FROM stand s
    LEFT JOIN menu m ON s.id_stand = m.id_stand
    LEFT JOIN detail_transaksi dt ON m.id_menu = dt.id_menu
    LEFT JOIN transaksi t ON dt.id_transaksi = t.id_transaksi
    GROUP BY s.id_stand
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Super Admin - Skomda</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 20px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .stat-card h3 {
            color: #666;
            font-size: 0.9rem;
        }

        .stat-card p {
            font-size: 1.8rem;
            font-weight: bold;
            color: #ce1212;
            margin-top: 10px;
        }

        .omzet-container {
            margin-top: 20px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .omzet-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }

        .omzet-item:last-child {
            border-bottom: none;
        }

        .stand-name {
            font-weight: bold;
            color: #333;
        }

        .stand-amount {
            font-weight: bold;
            color: #ce1212;
        }
    </style>
</head>

<body>
    <nav class="main-nav">
        <div class="nav-brand">
            <h1>SUPER ADMIN SKOMDA</h1>
        </div>
        <div class="nav-menu">
            <a href="dashboard.php" class="nav-link">Dashboard</a>
            <a href="manage_stand.php" class="nav-link">Manage Stand</a>
            <a href="manage_user.php" class="nav-link">Manage User</a>
            <a href="../logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>

    <div class="admin-container">
        <h2>Selamat Datang, <?php echo $_SESSION['username']; ?></h2>
        <p>Ringkasan performa kantin hari ini.</p>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Stand Terdaftar</h3>
                <p><?php echo $t_stand; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Siswa Aktif</h3>
                <p><?php echo $t_siswa; ?></p>
            </div>
        </div>

        <div class="omzet-container">
            <h3 style="color: #666; font-size: 1rem; margin-bottom: 15px;">Daftar Omzet per Stand</h3>
            <?php
            // memvalidasi apakah instruksi query omzet berhasil dijalankan
            if ($q_omzet_per_stand) {

                // melakukan perulangan untuk mengambil data omzet hasil query menjadi array
                while ($row = mysqli_fetch_assoc($q_omzet_per_stand)) {
            ?>
                    <div class="omzet-item">
                        <span class="stand-name"><?php echo $row['nama_stand']; ?></span>

                        <span class="stand-amount">Rp <?php echo number_format($row['total_omzet'] ?? 0, 0, ',', '.'); ?></span>
                    </div>
            <?php
                }
            } else {
                // menampilkan pesan error jika query gagal dijalankan
                echo "<p style='color:red;'>Gagal memuat data: " . mysqli_error($conn) . "</p>";
            }
            ?>
        </div>
    </div>
</body>

</html>

<?php
// menghubungkan file ke komponen footer
include '../includes/footer.php';
?>