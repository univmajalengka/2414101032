<?php
require_once 'includes/header.php';
require_once 'config/db.php';

// Cek apakah user sudah login
if (!is_logged_in()) {
    header("Location: login.php");
    exit();
}

// Mendapatkan data booking user
 $bookings_query = "SELECT b.*, f.flight_number, f.departure_time, f.arrival_time,
                  a.airline_name, a.airline_code,
                  o.airport_name as origin_name, o.city as origin_city,
                  d.airport_name as destination_name, d.city as destination_city
                  FROM bookings b 
                  JOIN flights f ON b.flight_id = f.flight_id 
                  JOIN airlines a ON f.airline_id = a.airline_id 
                  JOIN airports o ON f.origin_code = o.airport_code
                  JOIN airports d ON f.destination_code = d.airport_code
                  WHERE b.user_id = ? 
                  ORDER BY b.booking_date DESC";
 $bookings_stmt = $conn->prepare($bookings_query);
 $bookings_stmt->bind_param("i", $_SESSION['user_id']);
 $bookings_stmt->execute();
 $bookings_result = $bookings_stmt->get_result();

 $bookings = [];
if ($bookings_result->num_rows > 0) {
    while ($row = $bookings_result->fetch_assoc()) {
        $bookings[] = $row;
    }
}
?>

<div class="container">
    <div class="history-container">
        <div class="history-header">
            <h2>Riwayat Pesanan</h2>
            <p>Lihat semua pesanan tiket pesawat yang telah Anda buat</p>
        </div>
        
        <?php if (empty($bookings)): ?>
            <div class="no-bookings">
                <i class="fas fa-ticket-alt"></i>
                <h3>Belum Ada Pesanan</h3>
                <p>Anda belum memiliki pesanan tiket pesawat. Mulai pesan tiket sekarang!</p>
                <a href="index.php" class="btn btn-primary">Cari Penerbangan</a>
            </div>
        <?php else: ?>
            <div class="bookings-list">
                <?php foreach ($bookings as $booking): ?>
                    <div class="booking-item">
                        <div class="booking-header">
                            <div class="booking-info">
                                <h3>Kode Booking: <?php echo $booking['booking_code']; ?></h3>
                                <p>Tanggal Booking: <?php echo date('d F Y H:i', strtotime($booking['booking_date'])); ?></p>
                            </div>
                            <div class="booking-status status-<?php echo $booking['status']; ?>" id="status-<?php echo $booking['booking_id']; ?>">
                                <?php 
                                if ($booking['status'] == 'pending') {
                                    echo 'Menunggu Pembayaran';
                                } elseif ($booking['status'] == 'confirmed') {
                                    echo 'Terkonfirmasi';
                                } else {
                                    echo 'Dibatalkan';
                                }
                                ?>
                            </div>
                        </div>
                        
                        <div class="booking-details">
                            <div class="flight-info">
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
                            
                            <div class="booking-summary">
                                <p><strong>Jumlah Penumpang:</strong> <?php echo $booking['total_passengers']; ?></p>
                                <p><strong>Total Harga:</strong> Rp <?php echo number_format($booking['total_price'], 0, ',', '.'); ?></p>
                            </div>
                        </div>
                        
                        <div class="booking-actions" id="actions-<?php echo $booking['booking_id']; ?>">
                            <?php if ($booking['status'] == 'pending'): ?>
                                <a href="payment.php?booking_id=<?php echo $booking['booking_id']; ?>" class="btn btn-primary">Lanjutkan Pembayaran</a>
                                <button type="button" class="btn btn-secondary" onclick="cancelBooking(<?php echo $booking['booking_id']; ?>)">Batalkan Pesanan</button>
                            <?php elseif ($booking['status'] == 'confirmed'): ?>
                                <button type="button" class="btn btn-primary" onclick="window.print()">Cetak Tiket</button>
                                <button type="button" class="btn btn-secondary" onclick="showPassengers(<?php echo $booking['booking_id']; ?>)">Lihat Detail Penumpang</button>
                            <?php else: ?>
                                <span class="text-muted">Tidak ada tindakan tersedia</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="passenger-details" id="passengers-<?php echo $booking['booking_id']; ?>" style="display: none;">
                            <h4>Detail Penumpang</h4>
                            <?php
                            // Mendapatkan data penumpang untuk booking ini
                            $passengers_query = "SELECT * FROM passengers WHERE booking_id = ?";
                            $passengers_stmt = $conn->prepare($passengers_query);
                            $passengers_stmt->bind_param("i", $booking['booking_id']);
                            $passengers_stmt->execute();
                            $passengers_result = $passengers_stmt->get_result();
                            
                            if ($passengers_result->num_rows > 0):
                            ?>
                                <table class="passenger-table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Lengkap</th>
                                            <th>Nomor Identitas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $no = 1;
                                        while ($passenger = $passengers_result->fetch_assoc()): 
                                        ?>
                                            <tr>
                                                <td><?php echo $no++; ?></td>
                                                <td><?php echo $passenger['full_name']; ?></td>
                                                <td><?php echo $passenger['id_number']; ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .history-container {
        background-color: white;
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .history-header {
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .history-header h2 {
        font-size: 28px;
        color: #0056b3;
        margin-bottom: 10px;
    }
    
    .no-bookings {
        text-align: center;
        padding: 50px 0;
        color: #666;
    }
    
    .no-bookings i {
        font-size: 48px;
        margin-bottom: 20px;
        color: #ccc;
    }
    
    .no-bookings h3 {
        font-size: 24px;
        margin-bottom: 15px;
    }
    
    .booking-item {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .booking-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .booking-info h3 {
        font-size: 18px;
        margin-bottom: 5px;
        color: #0056b3;
    }
    
    .booking-info p {
        margin: 0;
        color: #666;
    }
    
    .booking-status {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
    }
    
    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .status-confirmed {
        background-color: #d4edda;
        color: #155724;
    }
    
    .status-cancelled {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    .booking-details {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        flex-wrap: wrap;
        gap: 20px;
    }
    
    .flight-info {
        flex: 2;
    }
    
    .booking-summary {
        flex: 1;
    }
    
    .airline-info {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .airline-logo {
        width: 40px;
        height: 40px;
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
        font-size: 18px;
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
    
    .booking-summary p {
        margin-bottom: 5px;
    }
    
    .booking-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .passenger-details {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }
    
    .passenger-details h4 {
        font-size: 16px;
        margin-bottom: 10px;
        color: #0056b3;
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
    
    @media (max-width: 768px) {
        .booking-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
        
        .booking-details {
            flex-direction: column;
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
        
        .booking-actions {
            flex-direction: column;
        }
    }
</style>

<script>
function showPassengers(bookingId) {
    const passengerDetails = document.getElementById(`passengers-${bookingId}`);
    
    if (passengerDetails.style.display === 'none') {
        passengerDetails.style.display = 'block';
    } else {
        passengerDetails.style.display = 'none';
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>