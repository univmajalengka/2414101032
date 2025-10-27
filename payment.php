<?php
require_once 'includes/header.php';
require_once 'config/db.php';

// Cek apakah user sudah login
if (!is_logged_in()) {
    header("Location: login.php");
    exit();
}

// Mendapatkan parameter dari URL
 $booking_id = isset($_GET['booking_id']) ? (int)clean_input($_GET['booking_id']) : 0;

// Validasi input
if ($booking_id <= 0) {
    header("Location: index.php");
    exit();
}

// Mendapatkan informasi booking
 $booking_query = "SELECT b.*, f.flight_number, f.departure_time, f.arrival_time, f.price,
                 a.airline_name, a.airline_code,
                 o.airport_name as origin_name, o.city as origin_city,
                 d.airport_name as destination_name, d.city as destination_city
                 FROM bookings b 
                 JOIN flights f ON b.flight_id = f.flight_id 
                 JOIN airlines a ON f.airline_id = a.airline_id 
                 JOIN airports o ON f.origin_code = o.airport_code
                 JOIN airports d ON f.destination_code = d.airport_code
                 WHERE b.booking_id = ? AND b.user_id = ?";
 $booking_stmt = $conn->prepare($booking_query);
 $booking_stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
 $booking_stmt->execute();
 $booking_result = $booking_stmt->get_result();

if ($booking_result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

 $booking = $booking_result->fetch_assoc();

// Mendapatkan data penumpang
 $passengers_query = "SELECT * FROM passengers WHERE booking_id = ?";
 $passengers_stmt = $conn->prepare($passengers_query);
 $passengers_stmt->bind_param("i", $booking_id);
 $passengers_stmt->execute();
 $passengers_result = $passengers_stmt->get_result();

 $passengers = [];
if ($passengers_result->num_rows > 0) {
    while ($row = $passengers_result->fetch_assoc()) {
        $passengers[] = $row;
    }
}

// Proses pembayaran (mock-up)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $payment_method = clean_input($_POST["payment_method"]);
    
    if (empty($payment_method)) {
        $error = "Metode pembayaran harus dipilih";
    } else {
        // Update status booking menjadi confirmed
        $update_query = "UPDATE bookings SET status = 'confirmed' WHERE booking_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("i", $booking_id);
        
        if ($update_stmt->execute()) {
            $success = "Pembayaran berhasil! Booking Anda telah dikonfirmasi.";
            
            // Refresh data booking
            $booking['status'] = 'confirmed';
        } else {
            $error = "Terjadi kesalahan. Silakan coba lagi.";
        }
    }
}
?>

<div class="container">
    <div class="payment-container">
        <div class="payment-summary">
            <div class="booking-header">
                <h2>Konfirmasi Pembayaran</h2>
            </div>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <div class="booking-info">
                <h3>Informasi Booking</h3>
                <div class="info-row">
                    <span>Kode Booking:</span>
                    <strong><?php echo $booking['booking_code']; ?></strong>
                </div>
                <div class="info-row">
                    <span>Status:</span>
                    <strong class="status-<?php echo $booking['status']; ?>">
                        <?php 
                        if ($booking['status'] == 'pending') {
                            echo 'Menunggu Pembayaran';
                        } elseif ($booking['status'] == 'confirmed') {
                            echo 'Terkonfirmasi';
                        } else {
                            echo 'Dibatalkan';
                        }
                        ?>
                    </strong>
                </div>
                <div class="info-row">
                    <span>Tanggal Booking:</span>
                    <strong><?php echo date('d F Y H:i', strtotime($booking['booking_date'])); ?></strong>
                </div>
            </div>
            
            <div class="flight-info">
                <h3>Detail Penerbangan</h3>
                <div class="flight-details">
                    <div class="airline-info">
                        <div class="airline-logo"><?php echo $booking['airline_code']; ?></div>
                        <div>
                            <h4><?php echo $booking['airline_name']; ?></h4>
                            <p><?php echo $booking['flight_number']; ?></p>
                        </div>
                    </div>
                    <div class="flight-route">
                        <div class="flight-time">
                            <div class="time"><?php echo date('H:i', strtotime($booking['departure_time'])); ?></div>
                            <div class="location"><?php echo $booking['origin_city']; ?></div>
                            <div class="date"><?php echo date('d M Y', strtotime($booking['departure_time'])); ?></div>
                        </div>
                        <div class="flight-duration">
                            <?php
                            $departure = new DateTime($booking['departure_time']);
                            $arrival = new DateTime($booking['arrival_time']);
                            $duration = $departure->diff($arrival);
                            echo $duration->h . ' jam ' . $duration->i . ' menit';
                            ?>
                        </div>
                        <div class="flight-time">
                            <div class="time"><?php echo date('H:i', strtotime($booking['arrival_time'])); ?></div>
                            <div class="location"><?php echo $booking['destination_city']; ?></div>
                            <div class="date"><?php echo date('d M Y', strtotime($booking['arrival_time'])); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="passenger-info">
                <h3>Data Penumpang</h3>
                <table class="passenger-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>Nomor Identitas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($passengers as $index => $passenger): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo $passenger['full_name']; ?></td>
                                <td><?php echo $passenger['id_number']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="price-info">
                <h3>Rincian Harga</h3>
                <div class="price-row">
                    <span>Harga per penumpang:</span>
                    <span>Rp <?php echo number_format($booking['price'], 0, ',', '.'); ?></span>
                </div>
                <div class="price-row">
                    <span>Jumlah penumpang:</span>
                    <span><?php echo $booking['total_passengers']; ?></span>
                </div>
                <div class="price-row total">
                    <span>Total Pembayaran:</span>
                    <span>Rp <?php echo number_format($booking['total_price'], 0, ',', '.'); ?></span>
                </div>
            </div>
        </div>
        
        <?php if ($booking['status'] == 'pending'): ?>
            <div class="payment-methods">
                <h3>Pilih Metode Pembayaran</h3>
                <form action="payment.php?booking_id=<?php echo $booking_id; ?>" method="post" id="payment-form">
                    <div class="payment-method">
                        <input type="radio" id="transfer" name="payment_method" value="transfer" required>
                        <label for="transfer" class="payment-method-label">
                            <i class="fas fa-university"></i> Transfer Bank
                        </label>
                    </div>
                    <div class="payment-method">
                        <input type="radio" id="credit-card" name="payment_method" value="credit-card" required>
                        <label for="credit-card" class="payment-method-label">
                            <i class="fas fa-credit-card"></i> Kartu Kredit
                        </label>
                    </div>
                    <div class="payment-method">
                        <input type="radio" id="e-wallet" name="payment_method" value="e-wallet" required>
                        <label for="e-wallet" class="payment-method-label">
                            <i class="fas fa-wallet"></i> E-Wallet
                        </label>
                    </div>
                    <div class="payment-method">
                        <input type="radio" id="virtual-account" name="payment_method" value="virtual-account" required>
                        <label for="virtual-account" class="payment-method-label">
                            <i class="fas fa-file-invoice"></i> Virtual Account
                        </label>
                    </div>
                    
                    <div class="payment-actions">
                        <a href="booking.php?flight_id=<?php echo $booking['flight_id']; ?>&passengers=<?php echo $booking['total_passengers']; ?>" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Bayar Sekarang</button>
                    </div>
                </form>
            </div>
            
            <div class="payment-timer" id="payment-timer-container">
                <p>Selesaikan pembayaran dalam:</p>
                <div class="timer" id="payment-timer" data-time="3600">01:00:00</div>
            </div>
            
            <div class="payment-instructions" id="payment-instructions" style="display: none;">
                <h3>Instruksi Pembayaran</h3>
                <div id="instructions-content">
                    <!-- Konten instruksi akan dimuat berdasarkan metode pembayaran yang dipilih -->
                </div>
            </div>
        <?php else: ?>
            <div class="payment-actions">
                <a href="history.php" class="btn btn-primary">Lihat Riwayat Pesanan</a>
                <a href="index.php" class="btn btn-secondary">Kembali ke Beranda</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .payment-summary {
        background-color: white;
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }
    
    .booking-info, .flight-info, .passenger-info, .price-info {
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }
    
    .booking-info:last-child, .flight-info:last-child, .passenger-info:last-child, .price-info:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    .booking-info h3, .flight-info h3, .passenger-info h3, .price-info h3 {
        font-size: 18px;
        margin-bottom: 15px;
        color: #0056b3;
    }
    
    .info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    
    .status-pending {
        color: #856404;
    }
    
    .status-confirmed {
        color: #155724;
    }
    
    .status-cancelled {
        color: #721c24;
    }
    
    .flight-details {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }
    
    .airline-info {
        display: flex;
        align-items: center;
    }
    
    .airline-logo {
        width: 50px;
        height: 50px;
        background-color: #0056b3;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-weight: bold;
    }
    
    .flight-route {
        display: flex;
        align-items: center;
        gap: 30px;
    }
    
    .flight-time {
        text-align: center;
    }
    
    .flight-time .time {
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 5px;
    }
    
    .flight-time .location {
        color: #666;
        margin-bottom: 5px;
    }
    
    .flight-time .date {
        color: #666;
        font-size: 14px;
    }
    
    .flight-duration {
        text-align: center;
        color: #666;
        position: relative;
        padding: 0 20px;
    }
    
    .flight-duration::before {
        content: "";
        position: absolute;
        top: 50%;
        left: 0;
        width: 100%;
        height: 1px;
        background-color: #ddd;
        z-index: 1;
    }
    
    .flight-duration::after {
        content: "✈";
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: white;
        padding: 0 10px;
        z-index: 2;
    }
    
    .passenger-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }
    
    .passenger-table th, .passenger-table td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #eee;
    }
    
    .passenger-table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    
    .price-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    
    .price-row.total {
        font-weight: bold;
        font-size: 18px;
        padding-top: 10px;
        border-top: 1px solid #eee;
    }
    
    .payment-methods {
        background-color: white;
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }
    
    .payment-methods h3 {
        font-size: 18px;
        margin-bottom: 20px;
        color: #0056b3;
    }
    
    .payment-method {
        display: flex;
        align-items: center;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-bottom: 15px;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    
    .payment-method:hover {
        background-color: #f8f9fa;
    }
    
    .payment-method input {
        margin-right: 15px;
    }
    
    .payment-method-label {
        display: flex;
        align-items: center;
        font-weight: 500;
    }
    
    .payment-method-label i {
        margin-right: 10px;
        color: #0056b3;
    }
    
    .payment-actions {
        display: flex;
        justify-content: space-between;
        margin-top: 30px;
    }
    
    .payment-timer {
        background-color: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        margin-bottom: 30px;
        text-align: center;
    }
    
    .payment-timer p {
        margin-bottom: 10px;
        color: #666;
    }
    
    .timer {
        font-size: 24px;
        font-weight: bold;
        color: #dc3545;
    }
    
    .payment-instructions {
        background-color: white;
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }
    
    .payment-instructions h3 {
        font-size: 18px;
        margin-bottom: 20px;
        color: #0056b3;
    }
    
    #payment-timeout {
        display: none;
        background-color: #f8d7da;
        color: #721c24;
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 20px;
        text-align: center;
    }
    
    @media (max-width: 768px) {
        .flight-details {
            flex-direction: column;
            gap: 20px;
        }
        
        .flight-route {
            flex-direction: column;
            gap: 15px;
        }
        
        .flight-duration {
            padding: 15px 0;
        }
        
        .flight-duration::before {
            width: 1px;
            height: 100%;
            left: 50%;
            top: 0;
        }
        
        .flight-duration::after {
            transform: translate(-50%, -50%) rotate(90deg);
        }
        
        .info-row {
            flex-direction: column;
            gap: 5px;
        }
        
        .price-row {
            flex-direction: column;
            gap: 5px;
        }
        
        .payment-actions {
            flex-direction: column;
            gap: 10px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Payment method selection
    const paymentMethods = document.querySelectorAll('.payment-method');
    const paymentForm = document.getElementById('payment-form');
    const instructionsContent = document.getElementById('instructions-content');
    const paymentInstructions = document.getElementById('payment-instructions');
    
    paymentMethods.forEach(method => {
        method.addEventListener('click', function() {
            // Remove active class from all methods
            paymentMethods.forEach(m => m.classList.remove('active'));
            
            // Add active class to selected method
            this.classList.add('active');
            
            // Check the radio button
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;
            
            // Show instructions based on selected method
            showPaymentInstructions(radio.value);
        });
    });
    
    function showPaymentInstructions(method) {
        let instructions = '';
        
        switch(method) {
            case 'transfer':
                instructions = `
                    <h4>Transfer Bank</h4>
                    <p>Silakan transfer ke rekening berikut:</p>
                    <div class="bank-details">
                        <p><strong>Bank:</strong> Bank Central Asia (BCA)</p>
                        <p><strong>No. Rekening:</strong> 1234567890</p>
                        <p><strong>Atas Nama:</strong> PT Bandara Kertajati</p>
                        <p><strong>Jumlah:</strong> Rp <?php echo number_format($booking['total_price'], 0, ',', '.'); ?></p>
                    </div>
                    <p>Setelah melakukan transfer, silakan konfirmasi dengan mengunggah bukti transfer.</p>
                `;
                break;
            case 'credit-card':
                instructions = `
                    <h4>Pembayaran Kartu Kredit</h4>
                    <p>Anda akan diarahkan ke halaman pembayaran yang aman.</p>
                    <p>Masukkan detail kartu kredit Anda untuk menyelesaikan pembayaran.</p>
                    <p>Pastikan data yang Anda masukkan benar dan valid.</p>
                `;
                break;
            case 'e-wallet':
                instructions = `
                    <h4>Pembayaran E-Wallet</h4>
                    <p>Pilih E-Wallet yang Anda gunakan:</p>
                    <div class="e-wallet-options">
                        <div class="e-wallet-option">
                            <img src="assets/images/gopay.png" alt="GoPay">
                            <p>GoPay</p>
                        </div>
                        <div class="e-wallet-option">
                            <img src="assets/images/ovo.png" alt="OVO">
                            <p>OVO</p>
                        </div>
                        <div class="e-wallet-option">
                            <img src="assets/images/dana.png" alt="DANA">
                            <p>DANA</p>
                        </div>
                    </div>
                    <p>Scan QR code atau masukkan nomor telepon yang terhubung dengan E-Wallet Anda.</p>
                `;
                break;
            case 'virtual-account':
                instructions = `
                    <h4>Pembayaran Virtual Account</h4>
                    <p>Silakan transfer ke Virtual Account berikut:</p>
                    <div class="va-details">
                        <p><strong>Bank:</strong> Bank Mandiri</p>
                        <p><strong>No. Virtual Account:</strong> 8870<?php echo $booking['booking_code']; ?></p>
                        <p><strong>Jumlah:</strong> Rp <?php echo number_format($booking['total_price'], 0, ',', '.'); ?></p>
                    </div>
                    <p>Virtual Account khusus dibuat untuk pesanan Anda dan hanya berlaku untuk pembayaran pesanan ini.</p>
                `;
                break;
        }
        
        instructionsContent.innerHTML = instructions;
        paymentInstructions.style.display = 'block';
    }
    
    // Countdown timer
    const paymentTimer = document.getElementById('payment-timer');
    
    if (paymentTimer) {
        let timeLeft = parseInt(paymentTimer.getAttribute('data-time'));
        
        const timerInterval = setInterval(function() {
            timeLeft--;
            
            const hours = Math.floor(timeLeft / 3600);
            const minutes = Math.floor((timeLeft % 3600) / 60);
            const seconds = timeLeft % 60;
            
            paymentTimer.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                paymentTimer.textContent = "Waktu Habis";
                // Redirect or show timeout message
                document.getElementById('payment-timeout').style.display = 'block';
                document.getElementById('payment-form').style.display = 'none';
            }
        }, 1000);
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>