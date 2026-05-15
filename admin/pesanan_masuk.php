<?php

// memulai session untuk menyimpan data login pengguna
session_start();

// menghubungkan file ke konfigurasi database
include '../config/koneksi.php';

// memeriksa hak akses pengguna apakah sebagai admin
if ($_SESSION['role'] != "admin") {

    // mengalihkan halaman ke lokasi yang ditentukan
    header("location:../login.php");

    // menghentikan eksekusi script
    exit;
}

// mengambil id stand milik admin dari data session
$id_s = $_SESSION['id_stand'];

// memvalidasi pengiriman data melalui metode GET untuk update status
if (isset($_GET['selesaikan'])) {

    // inisialisasi variabel id transaksi dari parameter URL
    $id_t = $_GET['selesaikan'];

    // menjalankan instruksi query update status transaksi di database
    $update = mysqli_query($conn, "UPDATE transaksi SET status = 'Selesai' WHERE id_transaksi = '$id_t'");

    // validasi jika instruksi query update berhasil dijalankan
    if ($update) {

        // menampilkan notifikasi pesan melalui javascript dan mengalihkan halaman
        echo "<script>alert('Pesanan selesai! Status di siswa otomatis terupdate.'); window.location='pesanan_masuk.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Pesanan Masuk - Kantin Skomda</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background-color: #f4f4f4;
        }

        .admin-table th {
            background-color: #ce1212;
            color: white;
            padding: 12px;
        }

        .admin-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            display: inline-block;
        }

        .status-pending {
            background-color: #ffe5e5;
            color: #d9534f;
            border: 1px solid #d9534f;
        }

        .status-selesai {
            background-color: #e5f9e5;
            color: #28a745;
            border: 1px solid #28a745;
        }

        .btn-selesai {
            background-color: #28a745;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 13px;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-selesai:hover {
            background-color: #218838;
            transform: scale(1.05);
        }
    </style>
</head>

<body>

    <nav class="main-nav">
        <div class="nav-brand">
            <h1>PESANAN MASUK - STAND <?php echo $id_s; ?></h1>
        </div>
        <div class="nav-menu">
            <a href="dashboard.php" class="nav-link">Kelola Menu</a>
            <a href="pesanan_masuk.php" class="nav-link active">Pesanan Masuk</a>
            <a href="laporan.php" class="nav-link">Laporan</a>
            <a href="../logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>

    <div class="admin-container">
        <div class="admin-header">
            <a href="dashboard.php" style="color: #ce1212; text-decoration: none; font-weight: bold;">← Kembali ke Dashboard</a>
            <h2 style="margin-top: 15px;">Daftar Antrean Pesanan</h2>
        </div>

        <table class="admin-table" style="width: 100%; border-collapse: collapse; background: white; margin-top: 20px;">
            <thead>
                <tr>
                    <th>No. Transaksi</th>
                    <th>Nama Pelanggan</th>
                    <th>Total Bayar</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php

                // menyusun instruksi query JOIN untuk mengambil data transaksi spesifik per stand
                $query = "SELECT DISTINCT t.*, u.username 
                          FROM transaksi t
                          JOIN user u ON t.id_user = u.id_user
                          JOIN detail_transaksi dt ON t.id_transaksi = dt.id_transaksi
                          JOIN menu m ON dt.id_menu = m.id_menu
                          WHERE m.id_stand = '$id_s'
                          ORDER BY t.id_transaksi DESC";

                // menjalankan instruksi query ke database
                $sql = mysqli_query($conn, $query);

                // validasi jika hasil query tidak menemukan data pesanan
                if (mysqli_num_rows($sql) == 0) {
                    echo "<tr><td colspan='5' style='text-align:center; padding:30px; color:#888;'>Belum ada pesanan masuk.</td></tr>";
                }

                // melakukan perulangan untuk mengambil data hasil query menjadi array
                while ($d = mysqli_fetch_array($sql)) { ?>
                    <tr>
                        <td align="center"><strong>#<?php echo $d['id_transaksi']; ?></strong></td>

                        <td><?php echo $d['username']; ?></td>

                        <td>Rp <?php echo number_format($d['total_bayar'], 0, ',', '.'); ?></td>

                        <td align="center">
                            <?php if ($d['status'] == 'Pending') : ?>
                                <span class="status-badge status-pending">MENUNGGU</span>
                            <?php else : ?>
                                <span class="status-badge status-selesai">SELESAI</span>
                            <?php endif; ?>
                        </td>

                        <td align="center">
                            <?php if ($d['status'] == 'Pending') : ?>
                                <a href="pesanan_masuk.php?selesaikan=<?php echo $d['id_transaksi']; ?>"
                                    class="btn-selesai"
                                    onclick="return confirm('Yakin pesanan ini sudah selesai?')">Selesaikan</a>
                            <?php else : ?>
                                <span style="color: #28a745; font-weight: bold;">✅ Selesai</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>

</html>

<?php
// menghubungkan file ke komponen footer
include '../includes/footer.php';
?>