<?php

// menghubungkan file ke komponen header khusus siswa
include 'includes/header_siswa.php';

// menghubungkan file ke konfigurasi database
include 'config/koneksi.php';

// mengambil id user dari data session yang aktif
$id_user = $_SESSION['id_user'];
?>

<style>
    /* styling wadah melayang box notifikasi masakan matang di layar siswa */
    .notif-siswa-box {
        position: fixed; bottom: 20px; left: 20px; background: #28a745; color: white; padding: 15px 25px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); z-index: 9999; display: none; font-weight: bold; animation: slideUp 0.5s ease;
    }
    /* animasi transisi memunculkan kotak pemberitahuan dari arah bawah layar */
    @keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
</style>

<div id="alertSiswa" class="notif-siswa-box">🎉 Makananmu sudah matang! Silakan langsung ambil di stand kantin.</div>

<section style="padding: 40px 5%; min-height: 70vh; background-color: #f9f9f9;">
    <div class="container">
        <h2 style="color: #333;">Riwayat Pesanan Kamu (Hari Ini)</h2>
        <p style="color: #666;">Pantau status pesananmu di sini secara real-time.</p><br>

        <table border="0" width="100%" style="border-collapse: collapse; background: white; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-radius: 10px; overflow: hidden;">
            <thead>
                <tr style="background-color: #333; color: white; text-align: left;">
                    <th style="padding: 15px;">ID Transaksi</th>
                    <th style="padding: 15px;">Detail Menu</th>
                    <th style="padding: 15px;">Total Bayar</th>
                    <th style="padding: 15px; text-align: center;">Status Pesanan</th>
                </tr>
            </thead>
            <tbody>
                <?php

                // menyusun instruksi query untuk mengambil data transaksi milik user khusus hari ini saja menggunakan curdate
                $q_trans = mysqli_query($conn, "SELECT * FROM transaksi WHERE id_user = '$id_user' AND DATE(tgl_transaksi) = CURDATE() ORDER BY id_transaksi DESC");

                // memvalidasi apakah terdapat data transaksi dalam database
                if (mysqli_num_rows($q_trans) == 0) {
                    echo "<tr><td colspan='4' align='center' style='padding: 40px; color: #999;'>Belum ada riwayat pesanan untuk hari ini.</td></tr>";
                }

                // melakukan perulangan untuk mengambil data transaksi hasil query menjadi array
                while ($t = mysqli_fetch_array($q_trans)) {

                    // inisialisasi variabel id transaksi
                    $id_t = $t['id_transaksi'];
                ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td align="center" style="padding: 15px;"><strong>#TRX-<?php echo $id_t; ?></strong></td>
                        
                        <td style="padding: 15px;">
                            <ul style="margin: 0; padding-left: 15px; font-size: 0.9rem;">
                                <?php

                                // menyusun instruksi query JOIN untuk mengambil detail menu dalam satu transaksi
                                $q_detail = mysqli_query($conn, "SELECT detail_transaksi.*, menu.nama_menu 
                                                                 FROM detail_transaksi 
                                                                 JOIN menu ON detail_transaksi.id_menu = menu.id_menu 
                                                                 WHERE id_transaksi = '$id_t'");

                                // melakukan perulangan untuk menampilkan daftar item dan jumlah qty beli
                                while ($d = mysqli_fetch_array($q_detail)) {
                                    echo "<li>" . $d['nama_menu'] . " (x" . $d['qty'] . ")</li>";
                                }
                                ?>
                            </ul>
                        </td>

                        <td style="padding: 15px; font-weight: bold; color: #ce1212;">Rp <?php echo number_format($t['total_bayar'], 0, ',', '.'); ?></td>
                        
                        <td align="center" style="padding: 15px;">
                            <?php if ($t['status'] == 'Pending') : ?>
                                <div style="background: #fff3cd; color: #856404; padding: 10px 15px; border-radius: 8px; display: inline-block; border: 1px solid #ffeeba; min-width: 160px;">
                                    <span style="font-weight: bold; font-size: 0.85rem;">⏳ Sedang Dimasak</span>
                                </div>
                            <?php else : ?>
                                <div style="background: #d4edda; color: #155724; padding: 10px 15px; border-radius: 8px; display: inline-block; border: 1px solid #c3e6cb; min-width: 160px;">
                                    <span style="font-weight: bold; font-size: 0.85rem;">✅ Siap Diambil!</span>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</section>

<script>
    // mendeklarasikan variabel array kontrol untuk memantau daftar id selesai sebelumnya
    let daftarSelesaiLama = null;

    // membuat fungsi otomasi monitoring status pesanan matang di database
    function cekStatusMasakanSiswa() {
        // mengirim request background fetch ke file cek_notif_siswa.php yang murni json
        fetch('cek_notif_siswa.php')
            // mengubah data mentahan string respon menjadi format json objek
            .then(response => response.json())
            // mengeksekusi objek variabel hasil kembalian dari database server
            .then(data => {
                // jika status isi variabel array kontrol data lama masih kosong
                if (daftarSelesaiLama === null) {
                    // mengisi array dengan data transaksi selesai yang sudah ada saat halaman dibuka
                    daftarSelesaiLama = data.ids_selesai;
                } else {
                    // mencari apakah ada id transaksi baru di database yang tidak ada di data lama siswa
                    let adaYangBaruSelesai = data.ids_selesai.some(id => !daftarSelesaiLama.includes(id));
                    
                    // jika didapatkan ada id transaksi baru yang berstatus selesai
                    if (adaYangBaruSelesai) {
                        // menampilkan kotak notifikasi hijau pemberitahuan makanan siap diambil
                        document.getElementById('alertSiswa').style.display = 'block';
                        // memperbarui isi array kontrol lama dengan daftar id yang baru
                        daftarSelesaiLama = data.ids_selesai;
                        // memisalkan reload halaman otomatis setelah jeda 2.5 detik berjalan
                        setTimeout(() => { window.location.reload(); }, 2500);
                    }
                }
            });
    }
    // menyalakan fungsi pelacakan data real-time berkala di latar belakang setiap 3 detik
    setInterval(cekStatusMasakanSiswa, 3000);
</script>

<?php 

// menghubungkan file ke komponen footer
include 'includes/footer.php'; ?>