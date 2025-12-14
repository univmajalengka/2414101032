<?php
// modifikasi_pesanan.php
require 'koneksi.php';

 $pesanans = [];
 $error = '';
 $status = '';

// Cek status dari redirect (misalnya setelah berhasil menambah/mengedit)
if (isset($_GET['status']) && $_GET['status'] == 'success') {
    $status = 'Operasi berhasil dilakukan!';
}

// Proses hapus data
if (isset($_GET['delete_id'])) {
    $id_to_delete = $_GET['delete_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM pesanan WHERE id = ?");
        $stmt->execute([$id_to_delete]);
        // Redirect ke halaman yang sama untuk menghindari pengulangan hapus saat refresh
        header('Location: modifikasi_pesanan.php?status=deleted');
        exit;
    } catch (PDOException $e) {
        $error = "Gagal menghapus data: " . $e->getMessage();
    }
}

if (isset($_GET['status']) && $_GET['status'] == 'deleted') {
    $status = 'Pesanan berhasil dihapus.';
}


// Ambil semua data pesanan dari database
try {
    $stmt = $pdo->query("SELECT * FROM pesanan ORDER BY created_at DESC");
    $pesanans = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Gagal mengambil data: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan - Wisata Bandung</title>
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
        <?php if ($status): ?>
            <p style="color: green; text-align: center; background: #e0ffe0; padding: 10px; border: 1px solid #00a000;"><?php echo $status; ?></p>
        <?php endif; ?>
        <?php if ($error): ?>
            <p style="color: red; text-align: center; background: #ffe0e0; padding: 10px; border: 1px solid #a00000;"><?php echo $error; ?></p>
        <?php endif; ?>

        <h2>Daftar Semua Pesanan</h2>
        <?php if (count($pesanans) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Pemesan</th>
                        <th>No. HP</th>
                        <th>Tanggal Pesan</th>
                        <th>Waktu Pelaksanaan</th>
                        <th>Paket</th>
                        <th>Jumlah Peserta</th>
                        <th>Total Tagihan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pesanans as $pesanan): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($pesanan['id']); ?></td>
                        <td><?php echo htmlspecialchars($pesanan['nama_pemesan']); ?></td>
                        <td><?php echo htmlspecialchars($pesanan['no_hp']); ?></td>
                        <td><?php echo htmlspecialchars($pesanan['tanggal_pesan']); ?></td>
                        <td><?php echo htmlspecialchars($pesanan['waktu_pelaksanaan']); ?></td>
                        <td><?php echo htmlspecialchars($pesanan['paket_wisata']); ?></td>
                        <td><?php echo htmlspecialchars($pesanan['jumlah_peserta']); ?></td>
                        <td>Rp <?php echo number_format($pesanan['jumlah_tagihan'], 0, ',', '.'); ?></td>
                        <td class="actions">
                            <a href="pemesanan.php?id=<?php echo $pesanan['id']; ?>">Edit</a>
                            <a href="modifikasi_pesanan.php?delete_id=<?php echo $pesanan['id']; ?>" class="delete" onclick="return confirm('Apakah Anda yakin ingin menghapus pesanan ini?');">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Belum ada data pesanan.</p>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Wisata Bandung. All Rights Reserved.</p>
    </footer>
</body>
</html>