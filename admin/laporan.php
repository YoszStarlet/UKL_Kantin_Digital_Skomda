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
    header("location:../login.php");
    exit;
}

$id_s = $_SESSION['id_stand'];
$hari_ini = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pendapatan - Kantin Skomda</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .stats-grid { display: grid; grid-template-columns: 1fr; gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-left: 8px solid #ce1212; }
        .stat-card h3 { margin: 0; color: #888; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; }
        .stat-card h1 { margin: 10px 0; color: #ce1212; font-size: 3rem; }
        .info-text { color: #666; font-style: italic; }
    </style>
</head>
<body>

    <nav class="main-nav">
        <div class="nav-brand">
            <h1>LAPORAN PENDAPATAN</h1>
        </div>
        <div class="nav-menu">
            <a href="dashboard.php" class="nav-link">Kelola Menu</a>
            <a href="pesanan_masuk.php" class="nav-link">Pesanan Masuk</a>
            <a href="laporan.php" class="nav-link active" style="background-color: #ce1212; padding: 5px 10px; border-radius: 4px;">Laporan</a>
            <a href="../logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>

    <div class="admin-container">
        <div class="admin-header">
            <h2>Rekapitulasi Penjualan Stand</h2>
            <p class="info-text">Menampilkan data pendapatan yang telah berstatus <strong>Selesai</strong>.</p>
        </div>

        <?php
        // Query hitung total hari ini
        $q_total = mysqli_query($conn, "SELECT SUM(t.total_bayar) as total FROM transaksi t 
                                        JOIN detail_transaksi dt ON t.id_transaksi = dt.id_transaksi
                                        JOIN menu m ON dt.id_menu = m.id_menu
                                        WHERE m.id_stand = '$id_s' AND t.status = 'Selesai' AND t.tgl_transaksi = '$hari_ini'");
        $d_total = mysqli_fetch_assoc($q_total);
        $pendapatan = $d_total['total'] ?? 0;
        ?>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Pendapatan Hari Ini</h3>
                <h1>Rp <?php echo number_format($pendapatan, 0, ',', '.'); ?></h1>
                <p>Tanggal: <strong><?php echo date('d F Y'); ?></strong></p>
            </div>
        </div>

        <div class="admin-header">
            <h3>Rincian Transaksi Selesai Hari Ini</h3>
        </div>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>No. Transaksi</th>
                    <th>Nama Pelanggan</th>
                    <th>Waktu</th>
                    <th>Total Bayar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $q_list = mysqli_query($conn, "SELECT DISTINCT t.*, u.username FROM transaksi t 
                                               JOIN user u ON t.id_user = u.id_user
                                               JOIN detail_transaksi dt ON t.id_transaksi = dt.id_transaksi
                                               JOIN menu m ON dt.id_menu = m.id_menu
                                               WHERE m.id_stand = '$id_s' AND t.status = 'Selesai' AND t.tgl_transaksi = '$hari_ini'
                                               ORDER BY t.id_transaksi DESC");
                
                if(mysqli_num_rows($q_list) == 0) {
                    echo "<tr><td colspan='4' style='text-align:center;'>Belum ada transaksi selesai hari ini.</td></tr>";
                }

                while($l = mysqli_fetch_array($q_list)) {
                ?>
                <tr>
                    <td>#<?php echo $l['id_transaksi']; ?></td>
                    <td><?php echo $l['username']; ?></td>
                    <td><?php echo date('H:i', strtotime($l['tgl_transaksi'])); ?> WIB</td>
                    <td>Rp <?php echo number_format($l['total_bayar'], 0, ',', '.'); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php
// menghubungkan file ke database
include '../includes/footer.php';
?>