<?php

// memulai session untuk menyimpan data login pengguna
session_start();

// menghubungkan file ke konfigurasi database
include '/../config/koneksi.php';

// memvalidasi variabel koneksi database
if (!isset($conn) && isset($koneksi)) {
    $conn = $koneksi;
}

// memeriksa ketersediaan koneksi database
if (!isset($conn)) {
    echo "<h1 style='color:red;'>MASALAH KETEMU: Koneksi database tidak tersedia!</h1>";

    // menghentikan eksekusi script
    exit;
}

// memeriksa hak akses pengguna berdasarkan session id_stand
if (!isset($_SESSION['id_stand'])) {
    echo "<h1 style='color:red;'>MASALAH KETEMU: Lu belum login atau session id_stand kosong!</h1>";
    echo "Isi session lu saat ini: <pre>";

    // menampilkan data session untuk kebutuhan debugging
    print_r($_SESSION);
    echo "</pre>";

    // menghentikan eksekusi script
    exit;
}

// mengambil id stand milik admin dari data session
$id_s = $_SESSION['id_stand'];

// menampilkan informasi log id stand yang sedang aktif
echo "<h3>Log: Lu login sebagai Admin Stand ID: " . $id_s . "</h3>";

// menyusun instruksi query JOIN untuk mengambil data transaksi spesifik per stand
$query = "SELECT DISTINCT t.*, u.username 
          FROM transaksi t
          JOIN user u ON t.id_user = u.id_user
          JOIN detail_transaksi dt ON t.id_transaksi = dt.id_transaksi
          JOIN menu m ON dt.id_menu = m.id_menu
          WHERE m.id_stand = '$id_s'";

// menjalankan instruksi query ke database
$sql = mysqli_query($conn, $query);
?>

<table border="1" cellpadding="10">
    <tr style="background: yellow;">
        <th>ID Transaksi</th>
        <th>Nama Siswa</th>
        <th>Total</th>
        <th>Status</th>
    </tr>
    <?php

    // validasi jika hasil query tidak menemukan data transaksi
    if (mysqli_num_rows($sql) == 0) {
        echo "<tr><td colspan='4'>Database ada isinya, tapi query ini nggak nemu hasil yang cocok.</td></tr>";
    }

    // melakukan perulangan untuk mengambil data hasil query menjadi array
    while ($d = mysqli_fetch_array($sql)) {
    ?>
        <tr>
            <td><?php echo $d['id_transaksi']; ?></td>

            <td><?php echo $d['username']; ?></td>

            <td><?php echo $d['total_bayar']; ?></td>

            <td><?php echo $d['status']; ?></td>
        </tr>
    <?php } ?>
</table>