<?php
require_once 'includes/header.php';
require_once 'config/db.php';

// Mendapatkan daftar bandara untuk dropdown
 $airports_query = "SELECT * FROM airports ORDER BY city";
 $airports_result = $conn->query($airports_query);
 $airports = [];

if ($airports_result->num_rows > 0) {
    while ($row = $airports_result->fetch_assoc()) {
        $airports[] = $row;
    }
}

// Mendapatkan statistik penerbangan dari Kertajati
 $stats_query = "SELECT 
                COUNT(DISTINCT destination_code) as destination_count,
                COUNT(flight_id) as flight_count,
                MIN(price) as min_price,
                MAX(price) as max_price
                FROM flights 
                WHERE origin_code = 'KJT'";
 $stats_result = $conn->query($stats_query);
 $stats = $stats_result->fetch_assoc();
?>

<section class="hero">
    <div class="container">
        <h2>Selamat Datang di Bandara Internasional Jawa Barat Kertajati</h2>
        <p>Bandara modern terbesar di Jawa Barat yang menghubungkan Anda ke berbagai destinasi penting di Indonesia dengan fasilitas kelas dunia</p>
        <div class="hero-stats">
            <div class="stat-item">
                <h2><?php echo $stats['destination_count']; ?>+</h2>
                <p>Destinasi</p>
            </div>
            <div class="stat-item">
                <h2><?php echo $stats['flight_count']; ?>+</h2>
                <p>Penerbangan/Hari</p>
            </div>
            <div class="stat-item">
                <h2>Mulai Rp <?php echo number_format($stats['min_price'], 0, ',', '.'); ?></h2>
                <p>Harga Terjangkau</p>
            </div>
        </div>
    </div>
</section>

<div class="container">
    <div class="search-form">
        <h3>Cari Penerbangan dari Bandara Kertajati</h3>
        <form action="search.php" method="GET" class="needs-validation" novalidate>
            <div class="form-row">
                <div class="form-group">
                    <label for="trip-type">Jenis Perjalanan</label>
                    <select id="trip-type" class="form-control" name="trip_type">
                        <option value="one-way">Sekali Jalan</option>
                        <option value="round-trip">Pulang Pergi</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="origin">Dari</label>
                    <select id="origin" class="form-control" name="origin" required>
                        <option value="">Pilih Bandara Asal</option>
                        <?php foreach ($airports as $airport): ?>
                            <option value="<?php echo $airport['airport_code']; ?>" <?php echo ($airport['airport_code'] == 'KJT') ? 'selected' : ''; ?>>
                                <?php echo $airport['airport_name'] . ' (' . $airport['airport_code'] . ')'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">
                        Silakan pilih bandara asal.
                    </div>
                </div>
                <div class="form-group">
                    <label for="destination">Ke</label>
                    <select id="destination" class="form-control" name="destination" required>
                        <option value="">Pilih Bandara Tujuan</option>
                        <?php foreach ($airports as $airport): ?>
                            <option value="<?php echo $airport['airport_code']; ?>" <?php echo ($airport['airport_code'] == 'KJT') ? 'disabled' : ''; ?>>
                                <?php echo $airport['airport_name'] . ' (' . $airport['airport_code'] . ')'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">
                        Silakan pilih bandara tujuan.
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="departure-date">Tanggal Keberangkatan</label>
                    <input type="date" id="departure-date" class="form-control" name="departure_date" required>
                    <div class="invalid-feedback">
                        Silakan pilih tanggal keberangkatan.
                    </div>
                </div>
                <div class="form-group">
                    <label for="return-date">Tanggal Kembali</label>
                    <input type="date" id="return-date" class="form-control" name="return_date" disabled>
                </div>
                <div class="form-group">
                    <label for="passengers">Jumlah Penumpang</label>
                    <select id="passengers" class="form-control" name="passengers">
                        <option value="1">1 Penumpang</option>
                        <option value="2">2 Penumpang</option>
                        <option value="3">3 Penumpang</option>
                        <option value="4">4 Penumpang</option>
                        <option value="5">5 Penumpang</option>
                        <option value="6">6 Penumpang</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Cari Penerbangan</button>
        </form>
    </div>
</div>

<div class="container">
    <h2 class="section-title">Mengapa Memilih Bandara Kertajati?</h2>
    <div class="features">
        <div class="feature-card">
            <i class="fas fa-plane"></i>
            <h3>Fasilitas Modern</h3>
            <p>Bandara dengan teknologi terkini dan fasilitas kelas dunia untuk kenyamanan penumpang.</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-map-marked-alt"></i>
            <h3>Lokasi Strategis</h3>
            <p>Terletak di tengah Jawa Barat, mudah diakses dari berbagai kota besar seperti Bandung, Cirebon, dan Jakarta.</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-shuttle-van"></i>
            <h3>Aksesibilitas Tinggi</h3>
            <p>Tersedia berbagai pilihan transportasi darat untuk menuju bandara dengan mudah.</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-concierge-bell"></i>
            <h3>Pelayanan Prima</h3>
            <p>Staf profesional yang siap membantu kebutuhan perjalanan Anda dengan pelayanan terbaik.</p>
        </div>
    </div>
</div>

<div class="container">
    <h2 class="section-title">Destinasi Populer dari Kertajati</h2>
    <div class="destinations-grid">
        <?php
        // Mendapatkan destinasi populer
        $destinations_query = "SELECT a.airport_code, a.airport_name, a.city, COUNT(f.flight_id) as flight_count 
                              FROM airports a 
                              JOIN flights f ON a.airport_code = f.destination_code 
                              WHERE f.origin_code = 'KJT' 
                              GROUP BY a.airport_code 
                              ORDER BY flight_count DESC 
                              LIMIT 6";
        $destinations_result = $conn->query($destinations_query);
        
        if ($destinations_result->num_rows > 0) {
            while ($row = $destinations_result->fetch_assoc()) {
                echo '<div class="destination-card">';
                echo '<div class="destination-image-placeholder">';
                // Anda bisa menambahkan gambar di sini jika ada
                echo '<i class="fas fa-map-marker-alt"></i>';
                echo '</div>';
                echo '<div class="destination-info">';
                echo '<h3>' . htmlspecialchars($row['city']) . '</h3>';
                echo '<p class="airport-name">' . htmlspecialchars($row['airport_name']) . ' (' . htmlspecialchars($row['airport_code']) . ')</p>';
                echo '<p class="flight-count"><i class="fas fa-plane"></i> ' . $row['flight_count'] . ' penerbangan/hari</p>';
                echo '</div>';
                echo '<a href="search.php?origin=KJT&destination=' . $row['airport_code'] . '" class="btn btn-primary">Lihat Penerbangan</a>';
                echo '</div>';
            }
        } else {
            echo '<p class="no-destinations">Belum ada destinasi tersedia.</p>';
        }
        ?>
    </div>
</div>

<div class="container">
    <h2 class="section-title">Informasi Bandara Kertajati</h2>
    <div class="airport-info">
        <div class="info-card">
            <h3>Tentang Bandara</h3>
            <p>Bandara Internasional Jawa Barat (BIJB) Kertajati adalah bandara terbesar di Jawa Barat yang dibangun di atas lahan seluas 1.800 hektar. Bandara ini dirancang untuk menjadi bandara terbesar kedua di Indonesia setelah Soekarno-Hatta.</p>
            <p>Dengan kapasitas penumpang hingga 29 juta per tahun, Bandara Kertajati siap menjadi hub penerbangan baru di wilayah Indonesia bagian barat.</p>
        </div>
        <div class="info-card">
            <h3>Fasilitas</h3>
            <ul>
                <li>Terminal penumpang seluas 240.000 m²</li>
                <li>Runway sepanjang 3.500 meter</li>
                <li>54 check-in counters</li>
                <li>8 conveyor belt baggage</li>
                <li>Aviation security dan customs</li>
                <li>Lounge bisnis kelas dunia</li>
                <li>Fasilitas ibadah yang nyaman</li>
                <li>Ruang bermain anak</li>
                <li>Berbagai pilihan restoran dan toko</li>
            </ul>
        </div>
        <div class="info-card">
            <h3>Akses Transportasi</h3>
            <p>Bandara Kertajati dapat diakses melalui:</p>
            <ul>
                <li>Kendaraan pribadi (±2 jam dari Bandung)</li>
                <li>Bus DAMRI dari berbagai kota</li>
                <li>Taksi dan layanan transportasi online</li>
                <li>Shuttle bus dari hotel-hotel utama</li>
            </ul>
            <p>Rencana pengembangan transportasi massal seperti kereta api dan LRT sedang dalam tahap perencanaan untuk meningkatkan konektivitas bandara.</p>
        </div>
    </div>
</div>

<style>
/* GANTI BAGIAN INI DI index.php */

/* ----- TAMBAHKAN CSS BARU INI ----- */
.hero {
    /* Ganti 'url_gambar_anda.jpg' dengan path ke gambar Anda */
    /* Contoh: 'assets/images/kertajati_hero.jpg' */
    background-image: url('assets/images/bg1.jpg'); 
    background-size: cover; /* Agar gambar menutupi seluruh area section */
    background-position: center center; /* Posisikan gambar di tengah */
    background-repeat: no-repeat;
    position: relative; /* Diperlukan untuk overlay */
    color: #ffffff; /* Pastikan teks tetap putih */
}

/* OPSIONAL: Tambahkan overlay gelap agar teks lebih mudah dibaca 
  di atas gambar yang mungkin terang.
*/
.hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    /* 40% hitam transparan. Sesuaikan (0,0,0, 0.4) jika ingin lebih gelap/terang */
    background-color: rgba(0, 0, 0, 0.4); 
    z-index: 1; /* Overlay di bawah konten */
}

/* Pastikan konten (teks, stats) berada DI ATAS overlay
*/
.hero .container {
    position: relative;
    z-index: 2; /* Konten di atas overlay */
}
/* ----- BATAS AKHIR CSS BARU ----- */


/* GANTI BAGIAN INI DI index.php */

.hero-stats {
    display: flex;
    justify-content: center;
    gap: 40px;
    margin-top: 40px;
}

.stat-item {
    display: flex; /* Mengubah menjadi flex */
    align-items: center; /* Menyelaraskan item secara vertikal ke tengah */
    gap: 10px; /* Memberikan jarak antara angka dan teks */
    color: white;
    background-color: rgba(255, 255, 255, 0.1); /* Tambahkan background semi-transparan */
    padding: 10px 20px; /* Tambahkan padding */
    border-radius: 8px; /* Tambahkan sudut melengkung */
}

.stat-item h2 {
    font-size: 36px;
    font-weight: bold;
    margin: 0; /* Hapus margin default */
}

.stat-item p {
    font-size: 18px;
    opacity: 0.9;
    margin: 0; /* Hapus margin default */
}

/* Media query untuk tetap responsif di mobile */
@media (max-width: 768px) {
    .hero-stats {
        flex-direction: column; /* Susun item secara vertikal di mobile */
        align-items: center;
        gap: 15px;
    }

    .stat-item {
        width: 100%; /* Buat item memenuhi lebar di mobile */
        justify-content: center; /* Pusatkan konten di dalam item */
    }
}
/* ----- GANTI/PERBARUI BAGIAN INI DI index.php ----- */

/* Section Title */
.section-title {
    font-size: 32px;
    font-weight: 600;
    color: #0056b3;
    text-align: center;
    margin: 60px 0 40px;
    position: relative;
}

.section-title::after {
    content: '';
    display: block;
    width: 80px;
    height: 4px;
    background-color: #ffcc00;
    margin: 15px auto 0;
    border-radius: 2px;
}

/* Destinations Grid Container */
.destinations-grid {
    display: grid;
    /* Grid responsif: 1 kolom di mobile, 2 di tablet, 3 di desktop */
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
    margin-bottom: 60px;
}

/* Destination Card */
.destination-card {
    background-color: #ffffff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease-in-out;
    display: flex;
    flex-direction: column;
    text-align: center;
    height: 100%;
}

.destination-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
}

/* Placeholder untuk gambar destinasi */
.destination-image-placeholder {
    background-color: #e9f5ff;
    height: 160px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #0056b3;
    font-size: 48px;
}

.destination-info {
    padding: 25px 20px;
    flex-grow: 1; /* Mendorong tombol ke bawah */
    display: flex;
    flex-direction: column;
}

.destination-info h3 {
    font-size: 22px;
    font-weight: 600;
    color: #333;
    margin: 0 0 8px 0;
}

.destination-info .airport-name {
    font-size: 14px;
    color: #666;
    margin: 0 0 15px 0;
}

.destination-info .flight-count {
    font-size: 15px;
    color: #0056b3;
    font-weight: 500;
    margin: auto 0 20px 0; /* 'auto' untuk mendorong ke bawah */
}

.destination-info .flight-count i {
    margin-right: 5px;
}

.destination-card .btn {
    margin: 10px 20px 20px 20px;
    padding: 12px 20px;
    font-weight: 500;
    border-radius: 6px;
    width: calc(100% - 40px); /* Menghitung lebar dikurangi margin */
}

/* Pesan jika tidak ada destinasi */
.no-destinations {
    text-align: center;
    color: #888;
    font-size: 18px;
    grid-column: 1 / -1; /* Membentang di semua kolom grid */
}

/* ----- MEDIA QUERY UNTUK RESPONSIVITAS ----- */

/* Tablet (Layar hingga 768px) */
@media (max-width: 768px) {
    .section-title {
        font-size: 28px;
        margin: 50px 0 30px;
    }

    .destinations-grid {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
    }

    .destination-image-placeholder {
        height: 140px;
        font-size: 42px;
    }

    .destination-info {
        padding: 20px 15px;
    }

    .destination-info h3 {
        font-size: 20px;
    }
}

/* Mobile (Layar hingga 576px) */
@media (max-width: 576px) {
    .section-title {
        font-size: 24px;
        margin: 40px 0 25px;
    }

    .destinations-grid {
        grid-template-columns: 1fr; /* Paksa 1 kolom di layar kecil */
        gap: 20px;
    }

    .destination-card {
        max-width: 350px; /* Maksimal lebar card di mobile */
        margin: 0 auto; /* Pusatkan card */
    }

    .destination-image-placeholder {
        height: 120px;
        font-size: 36px;
    }
    
    .destination-info {
        padding: 20px;
    }

    .destination-info h3 {
        font-size: 18px;
    }
    
    .destination-info .airport-name {
        font-size: 13px;
    }

    .destination-info .flight-count {
        font-size: 14px;
    }
    
    .destination-card .btn {
        padding: 10px 15px;
        font-size: 14px;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>