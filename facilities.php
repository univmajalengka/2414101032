<?php
require_once 'includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1>Fasilitas Bandara Kertajati</h1>
        <p>Nikmati berbagai fasilitas kelas dunia untuk kenyamanan perjalanan Anda</p>
    </div>
    
    <div class="facilities-content">
        <div class="facility-category">
            <h2>Fasilitas Penumpang</h2>
            <div class="facilities-grid">
                <div class="facility-card">
                    <i class="fas fa-luggage-cart"></i>
                    <h3>Baggage Services</h3>
                    <p>Layanan bagasi yang aman dan efisien dengan 8 conveyor belt untuk mempercepat proses pengambilan bagasi.</p>
                </div>
                <div class="facility-card">
                    <i class="fas fa-concierge-bell"></i>
                    <h3>Lounge Bisnis</h3>
                    <p>Lounge eksklusif dengan fasilitas lengkap untuk penumpang kelas bisnis dan first class.</p>
                </div>
                <div class="facility-card">
                    <i class="fas fa-pray"></i>
                    <h3>Ruang Ibadah</h3>
                    <p>Musholla yang nyaman dan bersih untuk menunjang kebutuhan spiritual penumpang.</p>
                </div>
                <div class="facility-card">
                    <i class="fas fa-child"></i>
                    <h3>Ruang Bermain Anak</h3>
                    <p>Area bermain yang aman dan menyenangkan untuk anak-anak menunggu keberangkatan.</p>
                </div>
                <div class="facility-card">
                    <i class="fas fa-wheelchair"></i>
                    <h3>Fasilitas Disabilitas</h3>
                    <p>Aksesibilitas penuh untuk penumpang dengan kebutuhan khusus.</p>
                </div>
                <div class="facility-card">
                    <i class="fas fa-info-circle"></i>
                    <h3>Information Center</h3>
                    <p>Pusat informasi yang siap membantu segala kebutuhan informasi penumpang.</p>
                </div>
            </div>
        </div>
        
        <div class="facility-category">
            <h2>Fasilitas Komersial</h2>
            <div class="facilities-grid">
                <div class="facility-card">
                    <i class="fas fa-utensils"></i>
                    <h3>Restoran & Kafe</h3>
                    <p>Berbagai pilihan kuliner dari makanan tradisional hingga internasional.</p>
                </div>
                <div class="facility-card">
                    <i class="fas fa-shopping-bag"></i>
                    <h3>Toko Bebas Bea</h3>
                    <p>Belanja berbagai produk dengan harga bebas pajak sebelum keberangkatan.</p>
                </div>
                <div class="facility-card">
                    <i class="fas fa-gift"></i>
                    <h3>Souvenir Shop</h3>
                    <p>Berbagai oleh-oleh khas Jawa Barat dan suvenir menarik lainnya.</p>
                </div>
                <div class="facility-card">
                    <i class="fas fa-credit-card"></i>
                    <h3>Money Changer</h3>
                    <p>Layanan penukaran mata uang asing dengan kurs kompetitif.</p>
                </div>
                <div class="facility-card">
                    <i class="fas fa-wifi"></i>
                    <h3>WiFi Gratis</h3>
                    <p>Akses internet gratis berkecepatan tinggi di seluruh area terminal.</p>
                </div>
                <div class="facility-card">
                    <i class="fas fa-charging-station"></i>
                    <h3>Charging Station</h3>
                    <p>Stasiun pengisian daya untuk berbagai jenis perangkat elektronik.</p>
                </div>
            </div>
        </div>
        
        <div class="facility-category">
            <h2>Fasilitas Transportasi</h2>
            <div class="facilities-grid">
                <div class="facility-card">
                    <i class="fas fa-bus"></i>
                    <h3>Bus DAMRI</h3>
                    <p>Transportasi darat menuju berbagai kota di Jawa Barat dan Jakarta.</p>
                </div>
                <div class="facility-card">
                    <i class="fas fa-taxi"></i>
                    <h3>Taksi</h3>
                    <p>Layanan taksi resmi dengan tarif yang transparan dan sopir yang berpengalaman.</p>
                </div>
                <div class="facility-card">
                    <i class="fas fa-car"></i>
                    <h3>Rental Car</h3>
                    <p>Penyewaan mobil dari berbagai merek ternama untuk kenyamanan perjalanan.</p>
                </div>
                <div class="facility-card">
                    <i class="fas fa-parking"></i>
                    <h3>Area Parkir</h3>
                    <p>Kapasitas parkir hingga 3.000 kendaraan dengan sistem parkir modern.</p>
                </div>
            </div>
        </div>
        
        <div class="facility-category">
            <h2>Fasilitas Kargo</h2>
            <div class="facilities-grid">
                <div class="facility-card">
                    <i class="fas fa-box"></i>
                    <h3>Cargo Terminal</h3>
                    <p>Terminal kargo modern dengan kapasitas 600.000 ton per tahun.</p>
                </div>
                <div class="facility-card">
                    <i class="fas fa-warehouse"></i>
                    <h3>Gudang Pendingin</h3>
                    <p>Fasilitas cold chain untuk produk farmasi dan makanan beku.</p>
                </div>
                <div class="facility-card">
                    <i class="fas fa-shipping-fast"></i>
                    <h3>Logistics Center</h3>
                    <p>Pusat logistik terintegrasi untuk mendukung distribusi barang.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .page-header {
        text-align: center;
        margin-bottom: 40px;
        padding: 40px 0;
        background: linear-gradient(rgba(0, 86, 179, 0.1), rgba(0, 86, 179, 0.1));
        border-radius: 8px;
    }
    
    .page-header h1 {
        font-size: 36px;
        color: #0056b3;
        margin-bottom: 10px;
    }
    
    .page-header p {
        font-size: 18px;
        color: #666;
    }
    
    .facility-category {
        margin-bottom: 50px;
    }
    
    .facility-category h2 {
        font-size: 28px;
        color: #0056b3;
        margin-bottom: 30px;
        text-align: center;
    }
    
    .facilities-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
    }
    
    .facility-card {
        background-color: white;
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        text-align: center;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .facility-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .facility-card i {
        font-size: 48px;
        color: #0056b3;
        margin-bottom: 20px;
    }
    
    .facility-card h3 {
        font-size: 20px;
        margin-bottom: 15px;
        color: #0056b3;
    }
    
    .facility-card p {
        color: #666;
        line-height: 1.6;
    }
</style>

<?php require_once 'includes/footer.php'; ?>