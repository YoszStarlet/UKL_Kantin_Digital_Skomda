<?php
// memulai session untuk menyimpan data login pengguna
session_start();

// mengatur zona waktu sistem ke waktu indonesia barat agar jam tidak ngaco
date_default_timezone_set('Asia/Jakarta');

// menghubungkan file ke konfigurasi database
include 'config/koneksi.php';

// 1. Ambil ID User dari Session
$id_user = $_SESSION['id_user'];

// menambahkan format jam menit detik agar tgl_transaksi menjadi dinamis real-time
$tgl_transaksi = date("Y-m-d H:i:s");

// 2. Tarik data keranjang
$query_keranjang = "SELECT keranjang.*, menu.harga 
                    FROM keranjang 
                    JOIN menu ON keranjang.id_menu = menu.id_menu 
                    WHERE keranjang.id_user = '$id_user'";
$sql_keranjang = mysqli_query($conn, $query_keranjang);

if (mysqli_num_rows($sql_keranjang) == 0) {
    echo "<script>alert('Keranjang kosong!'); window.location='index.php';</script>";
    exit;
}

// 3. Hitung Total dan Tampung ke Array
$total_bayar = 0;
$items = [];
while ($row = mysqli_fetch_assoc($sql_keranjang)) {
    $total_bayar += ($row['harga'] * $row['qty']);
    $items[] = $row;
}

// 4. INSERT ke Tabel Transaksi (Kolom: total_bayar, metode_pembayaran)
// Status default: 'Pending'
$q_transaksi = "INSERT INTO transaksi (id_user, tgl_transaksi, total_bayar, status, metode_pembayaran) 
                VALUES ('$id_user', '$tgl_transaksi', '$total_bayar', 'Pending', NULL)";

if (mysqli_query($conn, $q_transaksi)) {
    $id_transaksi_baru = mysqli_insert_id($conn);

    // 5. INSERT ke Detail_Transaksi (SESUAI KOLOM DB LU: qty, subtotal)
    foreach ($items as $item) {
        $id_m = $item['id_menu'];
        $q   = $item['qty'];
        $sub = $item['harga'] * $q;

        // PERHATIKAN: Gue pake nama kolom 'qty' dan 'subtotal' sesuai list kolom lu barusan
        $q_detail = "INSERT INTO detail_transaksi (id_transaksi, id_menu, qty, subtotal) 
                     VALUES ('$id_transaksi_baru', '$id_m', '$q', '$sub')";
        mysqli_query($conn, $q_detail);

        // Update Stok
        mysqli_query($conn, "UPDATE menu SET stok = stok - $q WHERE id_menu = '$id_m'");
    }

    // 6. Kosongkan Keranjang
    mysqli_query($conn, "DELETE FROM keranjang WHERE id_user = '$id_user'");

    // 7. Lanjut ke Pembayaran
    header("location:pembayaran.php?id=$id_transaksi_baru");
} else {
    die("Gagal Simpan Transaksi: " . mysqli_error($conn));
}
?>