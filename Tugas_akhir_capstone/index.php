<?php
// index.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wisata Bandung - Beranda</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.php">Beranda</a>
            <a href="pemesanan.php">Dafrta Paket Wisata</a>
            <a href="modifikasi_pesanan.php">Kelola Pesanan</a>
        </div>
    </nav>

    <!-- Banner Promosi -->
    <section class="promo-banner">
        <div class="container">
            <h2>Jelajahi Keindahan Alam Bandung Bersama Kami</h2>
            <p>Dapatkan pengalaman tak terlupakan dengan paket wisata terbaik kami. Pesan sekarang dan nikmati perjalanan yang menyenangkan!</p>
            <a href="#paket-wisata" class="btn">Lihat Semua Paket</a>
        </div>
    </section>

    <main>
        <!-- Section untuk daftar paket (DIKEMBALIKAN KE SEMULA) -->
        <section id="paket-wisata" class="section section-light">
            <div class="container">
                <h2 class="section-title">Pilih Paket Wisata Anda</h2>
                <div class="package-grid">
                    <!-- Paket 1 Hari -->
                    <div class="package-card">
                        <img src="assets/images/bandung1.jpg" alt="Paket 1 Hari">
                        <div class="package-card-content">
                            <h3>Paket Wisata 1 Hari</h3>
                            <p class="price">Mulai dari Rp 600.000/orang</p>
                            <p>
                                <strong>Itinerary:</strong> Eksplorasi Lembang, Ciwidey, atau Kota Bandung dalam satu hari penuh. Pilih salah satu rute terbaik kami!
                            </p>
                            <a href="pemesanan.php?paket=1 Hari" class="btn">Pesan Sekarang</a>
                        </div>
                    </div>

                    <!-- Paket 2 Hari 1 Malam -->
                    <div class="package-card">
                        <img src="assets/images/bandung2.jpg" alt="Paket 2 Hari 1 Malam">
                        <div class="package-card-content">
                            <h3>Paket Wisata 2 Hari 1 Malam</h3>
                            <p class="price">Mulai dari Rp 1.000.000/orang</p>
                            <p>
                                <strong>Itinerary:</strong> Nikmati perjalanan ke Tangkuban Perahu, wisata di Lembang, menginap, dan jelajah pusat kota Bandung.
                            </p>
                            <a href="pemesanan.php?paket=2 Hari 1 Malam" class="btn">Pesan Sekarang</a>
                        </div>
                    </div>

                    <!-- Paket 3 Hari 2 Malam -->
                    <div class="package-card">
                        <img src="assets/images/bandung3.jpg" alt="Paket 3 Hari 2 Malam">
                        <div class="package-card-content">
                            <h3>Paket Wisata 3 Hari 2 Malam</h3>
                            <p class="price">Mulai dari Rp 1.500.000/orang</p>
                            <p>
                                <strong>Itinerary:</strong> Paket lengkap untuk menikmati Lembang, Kawah Putih Ciwidey, dan berbelanja di pusat kota dengan pengalaman menginap 2 malam.
                            </p>
                            <a href="pemesanan.php?paket=3 Hari 2 Malam" class="btn">Pesan Sekarang</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION BARU: GALERI VIDEO PROMOSI -->
        <section class="video-gallery-section">
            <div class="container">
                    <div class="video-gallery-grid">
                    <!-- Video 1 -->
                    <div class="video-gallery-item" data-video-id="FEGRiMaDmmA">
                        <img src="https://img.youtube.com/vi/FEGRiMaDmmA/hqdefault.jpg" alt="Video Promosi Paket 1 Hari">
                        <div class="play-button"></div>
                    </div>

                    <!-- Video 2 -->
                    <div class="video-gallery-item" data-video-id="FEGRiMaDmmA">
                        <img src="https://img.youtube.com/vi/FEGRiMaDmmA/hqdefault.jpg" alt="Video Promosi Paket 2 Hari 1 Malam">
                        <div class="play-button"></div>
                    </div>

                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> Wisata Bandung. All Rights Reserved.</p>
        </div>
    </footer>
    
    <script src="script.js"></script>
</body>
</html>