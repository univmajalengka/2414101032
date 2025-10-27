    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Tentang Bandara Kertajati</h3>
                    <p>Bandara Internasional Jawa Barat (BIJB) Kertajati adalah bandara yang terletak di Majalengka, Jawa Barat.</p>
                    <p>Bandara ini dirancang untuk menjadi bandara terbesar di Indonesia dengan luas 1.800 hektar.</p>
                </div>
                <div class="footer-section">
                    <h3>Link Cepat</h3>
                    <ul>
                        <li><a href="index.php">Beranda</a></li>
                        <li><a href="search.php">Cari Penerbangan</a></li>
                        <?php if (is_logged_in()): ?>
                            <li><a href="history.php">Riwayat Pesanan</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Kontak Kami</h3>
                    <p><i class="fas fa-map-marker-alt"></i> Jl. Bandara Kertajati, Kec. Kertajati, Kabupaten Majalengka, Jawa Barat</p>
                    <p><i class="fas fa-phone"></i> (0233) 123456</p>
                    <p><i class="fas fa-envelope"></i> info@kertajatiairport.id</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Bandara BIJB Kertajati. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>
    
    <script src="assets/js/script.js"></script>
</body>
</html>