<?php
require_once 'includes/header.php';
require_once 'config/db.php';

// Cek apakah user sudah login
if (!is_logged_in()) {
    header("Location: login.php");
    exit();
}

// Mendapatkan parameter dari URL
 $flight_id = isset($_GET['flight_id']) ? (int)clean_input($_GET['flight_id']) : 0;
 $passengers = isset($_GET['passengers']) ? (int)clean_input($_GET['passengers']) : 1;

// Validasi input
if ($flight_id <= 0 || $passengers <= 0) {
    header("Location: index.php");
    exit();
}

// Mendapatkan informasi penerbangan
 $flight_query = "SELECT f.*, a.airline_name, a.airline_code, 
                 o.airport_name as origin_name, o.city as origin_city,
                 d.airport_name as destination_name, d.city as destination_city
                 FROM flights f 
                 JOIN airlines a ON f.airline_id = a.airline_id 
                 JOIN airports o ON f.origin_code = o.airport_code
                 JOIN airports d ON f.destination_code = d.airport_code
                 WHERE f.flight_id = ?";
 $flight_stmt = $conn->prepare($flight_query);
 $flight_stmt->bind_param("i", $flight_id);
 $flight_stmt->execute();
 $flight_result = $flight_stmt->get_result();

if ($flight_result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

 $flight = $flight_result->fetch_assoc();

// Cek ketersediaan kursi
if ($flight['available_seats'] < $passengers) {
    header("Location: search.php?error=seats");
    exit();
}

// Proses booking
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validasi data penumpang
    $passenger_names = $_POST['passenger_name'];
    $passenger_ids = $_POST['passenger_id'];
    
    $errors = [];
    
    if (count($passenger_names) !== $passengers || count($passenger_ids) !== $passengers) {
        $errors[] = "Jumlah data penumpang tidak sesuai";
    }
    
    for ($i = 0; $i < $passengers; $i++) {
        if (empty($passenger_names[$i])) {
            $errors[] = "Nama penumpang ke-" . ($i + 1) . " harus diisi";
        }
        if (empty($passenger_ids[$i])) {
            $errors[] = "Nomor identitas penumpang ke-" . ($i + 1) . " harus diisi";
        }
    }
    
    // Jika tidak ada error, simpan data booking
    if (empty($errors)) {
        // Generate booking code
        $booking_code = generate_booking_code();
        
        // Hitung total harga
        $total_price = $flight['price'] * $passengers;
        
        // Mulai transaksi
        $conn->begin_transaction();
        
        try {
            // Simpan data booking
            $booking_query = "INSERT INTO bookings (user_id, flight_id, booking_code, total_passengers, total_price, status) VALUES (?, ?, ?, ?, ?, 'pending')";
            $booking_stmt = $conn->prepare($booking_query);
            $booking_stmt->bind_param("iisid", $_SESSION['user_id'], $flight_id, $booking_code, $passengers, $total_price);
            $booking_stmt->execute();
            
            $booking_id = $conn->insert_id;
            
            // Simpan data penumpang
            $passenger_query = "INSERT INTO passengers (booking_id, full_name, id_number) VALUES (?, ?, ?)";
            $passenger_stmt = $conn->prepare($passenger_query);
            
            for ($i = 0; $i < $passengers; $i++) {
                $passenger_stmt->bind_param("iss", $booking_id, $passenger_names[$i], $passenger_ids[$i]);
                $passenger_stmt->execute();
            }
            
            // Update kursi tersedia
            $update_seats_query = "UPDATE flights SET available_seats = available_seats - ? WHERE flight_id = ?";
            $update_seats_stmt = $conn->prepare($update_seats_query);
            $update_seats_stmt->bind_param("ii", $passengers, $flight_id);
            $update_seats_stmt->execute();
            
            // Commit transaksi
            $conn->commit();
            
            // Redirect ke halaman pembayaran
            header("Location: payment.php?booking_id=" . $booking_id);
            exit();
        } catch (Exception $e) {
            // Rollback transaksi
            $conn->rollback();
            $errors[] = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}
?>

<div class="container">
    <div class="booking-container">
        <div class="booking-header">
            <h2>Detail Pemesanan</h2>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="flight-summary">
            <h3>Detail Penerbangan</h3>
            <div class="flight-details">
                <div class="airline-info">
                    <div class="airline-logo"><?php echo $flight['airline_code']; ?></div>
                    <div>
                        <h4><?php echo $flight['airline_name']; ?></h4>
                        <p><?php echo $flight['flight_number']; ?></p>
                    </div>
                </div>
                <div class="flight-route">
                    <div class="flight-time">
                        <div class="time"><?php echo date('H:i', strtotime($flight['departure_time'])); ?></div>
                        <div class="location"><?php echo $flight['origin_city']; ?></div>
                        <div class="date"><?php echo date('d M Y', strtotime($flight['departure_time'])); ?></div>
                    </div>
                    <div class="flight-duration">
                        <?php
                        $departure = new DateTime($flight['departure_time']);
                        $arrival = new DateTime($flight['arrival_time']);
                        $duration = $departure->diff($arrival);
                        echo $duration->h . ' jam ' . $duration->i . ' menit';
                        ?>
                    </div>
                    <div class="flight-time">
                        <div class="time"><?php echo date('H:i', strtotime($flight['arrival_time'])); ?></div>
                        <div class="location"><?php echo $flight['destination_city']; ?></div>
                        <div class="date"><?php echo date('d M Y', strtotime($flight['arrival_time'])); ?></div>
                    </div>
                </div>
                <div class="price-summary">
                    <p>Harga per penumpang: <strong>Rp <?php echo number_format($flight['price'], 0, ',', '.'); ?></strong></p>
                    <p>Jumlah penumpang: <strong><?php echo $passengers; ?></strong></p>
                    <p>Total harga: <strong>Rp <?php echo number_format($flight['price'] * $passengers, 0, ',', '.'); ?></strong></p>
                </div>
            </div>
        </div>
        
        <form action="booking.php?flight_id=<?php echo $flight_id; ?>&passengers=<?php echo $passengers; ?>" method="post" class="needs-validation" novalidate>
            <h3>Data Penumpang</h3>
            <div id="passengers-container">
                <?php for ($i = 1; $i <= $passengers; $i++): ?>
                    <div class="passenger-form">
                        <div class="passenger-header">
                            <h3>Penumpang <?php echo $i; ?></h3>
                            <?php if ($i > 1): ?>
                                <button type="button" class="btn-remove" onclick="removePassenger(this)">Hapus</button>
                            <?php endif; ?>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="passenger-name-<?php echo $i; ?>">Nama Lengkap</label>
                                <input type="text" class="form-control" id="passenger-name-<?php echo $i; ?>" name="passenger_name[]" required>
                                <div class="invalid-feedback">
                                    Nama lengkap harus diisi.
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="passenger-id-<?php echo $i; ?>">Nomor Identitas (KTP/Paspor)</label>
                                <input type="text" class="form-control" id="passenger-id-<?php echo $i; ?>" name="passenger_id[]" required>
                                <div class="invalid-feedback">
                                    Nomor identitas harus diisi.
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
            
            <?php if ($passengers < 6): ?>
                <button type="button" class="btn-add" id="add-passenger">Tambah Penumpang</button>
            <?php endif; ?>
            
            <div class="booking-actions">
                <a href="search.php" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Lanjut ke Pembayaran</button>
            </div>
        </form>
    </div>
</div>

<style>
    .flight-summary {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 30px;
    }
    
    .flight-summary h3 {
        margin-bottom: 15px;
        color: #0056b3;
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
        background-color: #f8f9fa;
        padding: 0 10px;
        z-index: 2;
    }
    
    .price-summary {
        text-align: right;
    }
    
    .price-summary p {
        margin-bottom: 5px;
    }
    
    .booking-actions {
        display: flex;
        justify-content: space-between;
        margin-top: 30px;
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
        
        .price-summary {
            text-align: left;
        }
    }
</style>

<?php require_once 'includes/footer.php'; ?>