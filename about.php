<?php
require_once 'includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1>Tentang Bandara Internasional Jawa Barat Kertajati</h1>
        <p>Mengenal lebih dekat bandara modern terbesar di Jawa Barat</p>
    </div>
    
    <div class="about-content">
        <div class="about-section">
            <h2>Sejarah dan Pembangunan</h2>
            <p>Bandara Internasional Jawa Barat (BIJB) Kertajati adalah proyek strategis pemerintah Provinsi Jawa Barat untuk mendukung pertumbuhan ekonomi di wilayah tersebut. Pembangunan bandara dimulai pada tahun 2015 dan secara resmi beroperasi pada tahun 2019.</p>
            <p>Terletak di Kecamatan Kertajati, Kabupaten Majalengka, bandara ini dibangun di atas lahan seluas 1.800 hektar dengan investasi total mencapai Rp 2,6 triliun. Bandara ini dirancang untuk menjadi bandara terbesar kedua di Indonesia setelah Bandara Soekarno-Hatta.</p>
        </div>
        
        <div class="about-section">
            <h2>Visi dan Misi</h2>
            <div class="vision-mission">
                <div class="vision">
                    <h3>Visi</h3>
                    <p>Menjadi bandara internasional kelas dunia yang menjadi gerbang utama perekonomian Jawa Barat dan Indonesia bagian barat.</p>
                </div>
                <div class="mission">
                    <h3>Misi</h3>
                    <ul>
                        <li>Menyediakan layanan penerbangan yang aman, nyaman, dan efisien</li>
                        <li>Mendukung pengembangan pariwisata dan ekonomi kreatif Jawa Barat</li>
                        <li>Menjadi hub kargo terintegrasi untuk wilayah Indonesia bagian barat</li>
                        <li>Mengembangkan ekosistem aviasi yang berkelanjutan</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="about-section">
            <h2>Strategi Pengembangan</h2>
            <p>Bandara Kertajati dikembangkan dengan konsep "Aerotropolis" yang mengintegrasikan bandara dengan kawasan ekonomi di sekitarnya. Rencana pengembangan meliputi:</p>
            <ul>
                <li>Kawasan industri dan logistik seluas 2.000 hektar</li>
                <li>Kawasan perdagangan dan jasa</li>
                <li>Pusat konvensi dan pameran internasional</li>
                <li>Hotel-hotel berbintang dan fasilitas akomodasi</li>
                <li>Kawasan rekreasi dan hiburan</li>
            </ul>
        </div>
        
        <div class="about-section">
            <h2>Pencapaian</h2>
            <div class="achievements">
                <div class="achievement-item">
                    <h3>2023</h3>
                    <p>Melayani lebih dari 2 juta penumpang dengan 15 rute penerbangan domestik</p>
                </div>
                <div class="achievement-item">
                    <h3>2022</h3>
                    <p>Menjadi bandara dengan pertumbuhan penumpang tercepat di Indonesia</p>
                </div>
                <div class="achievement-item">
                    <h3>2021</h3>
                    <p>Mendapatkan sertifikasi bandara ramah lingkungan dari Kementerian LH</p>
                </div>
                <div class="achievement-item">
                    <h3>2020</h3>
                    <p>Menjadi hub kargo untuk distribusi vaksin COVID-19 di Jawa Barat</p>
                </div>
            </div>
        </div>
        
        <div class="about-section">
            <h2>Manajemen Bandara</h2>
            <p>Bandara Internasional Jawa Barat Kertajati dikelola oleh PT Bandarudara Internasional Jawa Barat (BIJB), sebuah BUMD milik Pemerintah Provinsi Jawa Barat. PT BIJB bertanggung jawab atas operasional, pengembangan, dan komersialisasi bandara.</p>
            <p>Dengan dukungan dari berbagai maskapai penerbangan nasional dan internasional, Bandara Kertajati terus berinovasi untuk memberikan layanan terbaik bagi seluruh penumpang.</p>
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
    
    .about-content {
        max-width: 900px;
        margin: 0 auto;
    }
    
    .about-section {
        background-color: white;
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 30px;
    }
    
    .about-section h2 {
        font-size: 24px;
        color: #0056b3;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }
    
    .about-section p {
        color: #666;
        line-height: 1.8;
        margin-bottom: 15px;
    }
    
    .about-section ul {
        padding-left: 20px;
        color: #666;
        line-height: 1.8;
    }
    
    .about-section li {
        margin-bottom: 8px;
    }
    
    .vision-mission {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
    }
    
    .vision, .mission {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
    }
    
    .vision h3, .mission h3 {
        font-size: 20px;
        color: #0056b3;
        margin-bottom: 15px;
    }
    
    .achievements {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }
    
    .achievement-item {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
    }
    
    .achievement-item h3 {
        font-size: 20px;
        color: #0056b3;
        margin-bottom: 10px;
    }
    
    .achievement-item p {
        font-size: 14px;
        color: #666;
    }
    
    @media (max-width: 768px) {
        .vision-mission {
            grid-template-columns: 1fr;
        }
        
        .achievements {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php require_once 'includes/footer.php'; ?>