<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Selamat Datang - Kantin Digital Skomda</title>
    <link rel="stylesheet" href="assets/css/landing.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body>

    <nav
        style="padding: 20px 5%; display: flex; justify-content: space-between; align-items: center; background: white; position: sticky; top: 0; z-index: 1000; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <h2 style="color: #ce1212;">Skomda<span> Kantin</span></h2>
        <div>
            <a href="#about" style="text-decoration: none; color: #333; margin-right: 20px; font-weight: 500;">Tentang
                Saya</a>
            <a href="login.php"
                style="text-decoration: none; background: #ce1212; color: white; padding: 8px 20px; border-radius: 5px;">Login</a>
        </div>
    </nav>

    <section class="hero">
        <h1>Kantin Pre-Order Skomda</h1>
        <p>Solusi pesan makanan tanpa ribet, tanpa antre, tinggal ambil.</p>
        <a href="login.php" class="btn-start">Mulai Pesan Sekarang</a>
    </section>

    <section class="features">
        <div class="feat-box">
            <div class="icon">⚡</div>
            <h3>Pre-Order Kilat</h3>
            <p>Pesan makanan dari kelas, ambil pas bel istirahat bunyi. Gak pake antre!</p>
        </div>
        <div class="feat-box">
            <div class="icon">📊</div>
            <h3>Stok Real-Time</h3>
            <p>Data menu sinkron sama stok kantin. Gak ada lagi drama 'udah bayar tapi abis'.</p>
        </div>
        <div class="feat-box">
            <div class="icon">🔐</div>
            <h3>Keamanan Data</h3>
            <p>Transaksi tercatat rapi di database. Riwayat jajan lu aman dan transparan.</p>
        </div>
    </section>

    <section class="about-section" id="about">
        <div class="profile-card">
            <div class="card-top"></div>
            <img src="assets/img/foto_ade.jpeg" alt="Developer" class="profile-img">
            <h2>Yosua Ade Nugraha</h2>
            <p class="role-badge">Lead Developer | X-SIJA-1</p>
            <div class="bio">
                "Project ini dibuat untuk memenuhi tugas UKL dan sebagai upaya digitalisasi sistem pemesanan makanan di
                SMK Telkom Sidoarjo agar lebih efisien dan terdata."
            </div>
            <div style="padding-bottom: 30px;">
                <span
                    style="background: #f0f0f0; padding: 5px 12px; border-radius: 4px; font-size: 0.7rem; margin: 0 3px;">PHP
                    8</span>
                <span
                    style="background: #f0f0f0; padding: 5px 12px; border-radius: 4px; font-size: 0.7rem; margin: 0 3px;">MySQL</span>
                <span
                    style="background: #f0f0f0; padding: 5px 12px; border-radius: 4px; font-size: 0.7rem; margin: 0 3px;">UI/UX</span>
            </div>
        </div>
    </section>

    <footer>
        &copy; 2026 SMK Telkom Sidoarjo - Dikembangkan oleh Yosua Ade Nugraha
    </footer>

</body>

</html>