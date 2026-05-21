<?php
// memulai session untuk menyimpan data login pengguna
session_start();

// menghubungkan file ke konfigurasi database
include '../config/koneksi.php';

// mengambil id stand milik admin dari data session
$id_s = $_SESSION['id_stand'];

// query menghitung jumlah pesanan masuk yang berstatus pending khusus stan ini
$res = mysqli_query($conn, "SELECT COUNT(DISTINCT t.id_transaksi) as total FROM transaksi t JOIN detail_transaksi dt ON t.id_transaksi = dt.id_transaksi JOIN menu m ON dt.id_menu = m.id_menu WHERE m.id_stand = '$id_s' AND t.status = 'Pending'");

// memecah hasil hitungan query menjadi data array
$row = mysqli_fetch_assoc($res);

// mengatur header agar browser tahu ini adalah data json murni
header('Content-Type: application/json');

// mengirimkan respon berformat json berisi total pesanan pending ke browser
echo json_encode(['total' => $row['total']]);

// menghentikan proses php agar tidak bocor
exit;
?>