<?php
// memulai session untuk menyimpan data login pengguna
session_start();

// menghubungkan file ke konfigurasi database
include 'config/koneksi.php';

// mengambil id user dari data session yang aktif
$id_user = $_SESSION['id_user'];

// query memeriksa daftar id transaksi milik siswa yang statusnya sudah selesai khusus hari ini saja
$res = mysqli_query($conn, "SELECT id_transaksi FROM transaksi WHERE id_user = '$id_user' AND status = 'Selesai' AND DATE(tgl_transaksi) = CURDATE()");

// menyiapkan penampung array untuk menyimpan semua id transaksi yang selesai
$ids = [];

// melakukan perulangan untuk memasukkan semua id transaksi ke dalam array
while($row = mysqli_fetch_assoc($res)) {
    // memasukkan data id transaksi ke variabel array penampung
    $ids[] = $row['id_transaksi'];
}

// mengatur header agar browser tahu ini adalah data json murni
header('Content-Type: application/json');

// mengirimkan dokumen data objek berbentuk json kembali ke browser siswa
echo json_encode(['ids_selesai' => $ids]);

// menghentikan proses agar tidak ada teks lain yang bocor
exit;
?>