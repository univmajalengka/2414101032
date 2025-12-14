<?php
// pemesanan.php
require 'koneksi.php';

 $editing = false;
 $pesanan = [
    'nama_pemesan' => '',
    'no_hp' => '',
    'tanggal_pesan' => '',
    'waktu_pelaksanaan' => '',
    'paket_wisata' => '',
    'jumlah_peserta' => 1,
];

// Cek apakah ini adalah permintaan edit (berdasarkan ID di URL)
if (isset($_GET['id'])) {
    $editing = true;
    $id = $_GET['id'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM pesanan WHERE id = ?");
        $stmt->execute([$id]);
        $pesanan = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pesanan) {
            // Jika pesanan tidak ditemukan, redirect ke halaman modifikasi
            header('Location: modifikasi_pesanan.php');
            exit;
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

// Proses form jika disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $nama = $_POST['nama_pemesan'];
    $no_hp = $_POST['no_hp'];
    $tanggal_pesan = $_POST['tanggal_pesan'];
    $waktu_pelaksanaan = $_POST['waktu_pelaksanaan'];
    $paket = $_POST['paket_wisata'];
    $jumlah_peserta = (int)$_POST['jumlah_peserta'];
    
    // Tentukan harga berdasarkan paket
    $harga_paket = 0;
    switch ($paket) {
        case '1 Hari':
            $harga_paket = 600000;
            break;
        case '2 Hari 1 Malam':
            $harga_paket = 1000000;
            break;
        case '3 Hari 2 Malam':
            $harga_paket = 1500000;
            break;
    }

    // Hitung total tagihan
    $jumlah_tagihan = $jumlah_peserta * $harga_paket;

    try {
        if ($editing) {
            // Update data pesanan yang ada
            $sql = "UPDATE pesanan SET nama_pemesan=?, no_hp=?, tanggal_pesan=?, waktu_pelaksanaan=?, paket_wisata=?, jumlah_peserta=?, harga_paket=?, jumlah_tagihan=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nama, $no_hp, $tanggal_pesan, $waktu_pelaksanaan, $paket, $jumlah_peserta, $harga_paket, $jumlah_tagihan, $_POST['id']]);
        } else {
            // Insert data pesanan baru
            $sql = "INSERT INTO pesanan (nama_pemesan, no_hp, tanggal_pesan, waktu_pelaksanaan, paket_wisata, jumlah_peserta, harga_paket, jumlah_tagihan) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nama, $no_hp, $tanggal_pesan, $waktu_pelaksanaan, $paket, $jumlah_peserta, $harga_paket, $jumlah_tagihan]);
        }
        
        // Redirect ke halaman modifikasi pesanan dengan pesan sukses
        header('Location: modifikasi_pesanan.php?status=success');
        exit();

    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

// Jika ini adalah pemesanan baru, cek ada parameter paket di URL atau tidak
if (!$editing && isset($_GET['paket'])) {
    $pesanan['paket_wisata'] = htmlspecialchars($_GET['paket']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $editing ? 'Edit Pesanan' : 'Form Pemesanan'; ?> - Wisata Bandung</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <nav class="navbar">
        <div class="container">
            <a href="index.php">Beranda</a>
            <a href="pemesanan.php">Daftar Paket Wisata</a>
            <a href="modifikasi_pesanan.php">Kelola Pesanan</a>
        </div>
    </nav>

    <main class="container section">
        <form id="bookingForm" action="pemesanan.php<?php echo $editing ? '?id=' . $pesanan['id'] : ''; ?>" method="POST">
            <?php if ($editing): ?>
                <input type="hidden" name="id" value="<?php echo $pesanan['id']; ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="nama_pemesan">Nama Pemesan</label>
                <input type="text" id="nama_pemesan" name="nama_pemesan" value="<?php echo htmlspecialchars($pesanan['nama_pemesan']); ?>" required>
            </div>

            <div class="form-group">
                <label for="no_hp">Nomor HP/Telp</label>
                <input type="text" id="no_hp" name="no_hp" value="<?php echo htmlspecialchars($pesanan['no_hp']); ?>" required>
            </div>

            <div class="form-group">
                <label for="tanggal_pesan">Tanggal Pesan</label>
                <input type="date" id="tanggal_pesan" name="tanggal_pesan" value="<?php echo htmlspecialchars($pesanan['tanggal_pesan']); ?>" required>
            </div>

            <div class="form-group">
                <label for="waktu_pelaksanaan">Waktu Pelaksanaan Perjalanan</label>
                <input type="date" id="waktu_pelaksanaan" name="waktu_pelaksanaan" value="<?php echo htmlspecialchars($pesanan['waktu_pelaksanaan']); ?>" required>
            </div>

            <div class="form-group">
                <label for="paket_wisata">Pilih Paket Wisata</label>
                <select id="paket_wisata" name="paket_wisata" required>
                    <option value="">-- Pilih Paket --</option>
                    <option value="1 Hari" <?php echo ($pesanan['paket_wisata'] == '1 Hari') ? 'selected' : ''; ?>>Paket Wisata Bandung 1 Hari</option>
                    <option value="2 Hari 1 Malam" <?php echo ($pesanan['paket_wisata'] == '2 Hari 1 Malam') ? 'selected' : ''; ?>>Paket Wisata Bandung 2 Hari 1 Malam</option>
                    <option value="3 Hari 2 Malam" <?php echo ($pesanan['paket_wisata'] == '3 Hari 2 Malam') ? 'selected' : ''; ?>>Paket Wisata Bandung 3 Hari 2 Malam</option>
                </select>
            </div>

            <div class="form-group">
                <label for="jumlah_peserta">Jumlah Peserta</label>
                <input type="number" id="jumlah_peserta" name="jumlah_peserta" value="<?php echo htmlspecialchars($pesanan['jumlah_peserta']); ?>" min="1" required>
            </div>

            <div class="form-group">
                <label for="harga_paket">Harga Paket Perjalanan (per orang)</label>
                <input type="text" id="harga_paket" name="harga_paket" value="" readonly>
            </div>

            <div class="form-group">
                <label for="jumlah_tagihan">Jumlah Tagihan Total</label>
                <input type="text" id="jumlah_tagihan" name="jumlah_tagihan" value="" readonly>
            </div>

            <button type="submit" class="btn"><?php echo $editing ? 'Update Pesanan' : 'Pesan Sekarang'; ?></button>
        </form>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Wisata Bandung. All Rights Reserved.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>