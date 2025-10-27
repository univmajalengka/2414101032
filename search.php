<?php
require_once 'includes/header.php';
require_once 'config/db.php';

// Mendapatkan parameter dari URL
 $origin = isset($_GET['origin']) ? clean_input($_GET['origin']) : '';
 $destination = isset($_GET['destination']) ? clean_input($_GET['destination']) : '';
 $departure_date = isset($_GET['departure_date']) ? clean_input($_GET['departure_date']) : '';
 $return_date = isset($_GET['return_date']) ? clean_input($_GET['return_date']) : '';
 $passengers = isset($_GET['passengers']) ? (int)clean_input($_GET['passengers']) : 1;
 $trip_type = isset($_GET['trip_type']) ? clean_input($_GET['trip_type']) : 'one-way';

// Validasi input
 $errors = [];
if (empty($origin)) {
    $errors[] = "Bandara asal harus dipilih";
}
if (empty($destination)) {
    $errors[] = "Bandara tujuan harus dipilih";
}
if (empty($departure_date)) {
    $errors[] = "Tanggal keberangkatan harus dipilih";
}
if ($trip_type === 'round-trip' && empty($return_date)) {
    $errors[] = "Tanggal kembali harus dipilih untuk perjalanan pulang-pergi";
}

// Mendapatkan informasi bandara
 $origin_query = "SELECT * FROM airports WHERE airport_code = ?";
 $origin_stmt = $conn->prepare($origin_query);
 $origin_stmt->bind_param("s", $origin);
 $origin_stmt->execute();
 $origin_result = $origin_stmt->get_result();
 $origin_airport = $origin_result->fetch_assoc();

 $destination_query = "SELECT * FROM airports WHERE airport_code = ?";
 $destination_stmt = $conn->prepare($destination_query);
 $destination_stmt->bind_param("s", $destination);
 $destination_stmt->execute();
 $destination_result = $destination_stmt->get_result();
 $destination_airport = $destination_result->fetch_assoc();

// Mendapatkan penerbangan yang tersedia
 $flights = [];
if (empty($errors)) {
    $flights_query = "SELECT f.*, a.airline_name, a.airline_code 
                     FROM flights f 
                     JOIN airlines a ON f.airline_id = a.airline_id 
                     WHERE f.origin_code = ? AND f.destination_code = ? 
                     AND DATE(f.departure_time) = ? 
                     AND f.available_seats >= ? 
                     ORDER BY f.departure_time";
    
    $flights_stmt = $conn->prepare($flights_query);
    $flights_stmt->bind_param("sssi", $origin, $destination, $departure_date, $passengers);
    $flights_stmt->execute();
    $flights_result = $flights_stmt->get_result();
    
    if ($flights_result->num_rows > 0) {
        while ($row = $flights_result->fetch_assoc()) {
            $flights[] = $row;
        }
    }
}
?>

<div class="container">
    <div class="search-header">
        <h2>Hasil Pencarian Penerbangan</h2>
        <div class="search-info">
            <p>
                <?php echo $origin_airport['airport_name'] . ' (' . $origin . ')'; ?> 
                <i class="fas fa-arrow-right"></i> 
                <?php echo $destination_airport['airport_name'] . ' (' . $destination . ')'; ?>
            </p>
            <p>
                <?php echo date('d F Y', strtotime($departure_date)); ?>
                <?php if ($trip_type === 'round-trip'): ?>
                    <i class="fas fa-arrow-left"></i> <?php echo date('d F Y', strtotime($return_date)); ?>
                <?php endif; ?>
                | <?php echo $passengers; ?> Penumpang
            </p>
        </div>
        <a href="index.php" class="btn btn-secondary">Ubah Pencarian</a>
    </div>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php elseif (empty($flights)): ?>
        <div class="no-results">
            <i class="fas fa-search"></i>
            <h3>Tidak Ada Penerbangan Ditemukan</h3>
            <p>Tidak ada penerbangan yang tersedia untuk rute dan tanggal yang dipilih. Silakan coba dengan tanggal atau rute yang berbeda.</p>
            <a href="index.php" class="btn btn-primary">Cari Lagi</a>
        </div>
    <?php else: ?>
        <div class="filter-options">
            <button class="filter-btn active" data-filter="all">Semua</button>
            <button class="filter-btn" data-filter="morning">Pagi</button>
            <button class="filter-btn" data-filter="afternoon">Siang</button>
            <button class="filter-btn" data-filter="evening">Malam</button>
            <button class="filter-btn" data-filter="price-low">Harga Terendah</button>
            <button class="filter-btn" data-filter="price-high">Harga Tertinggi</button>
        </div>
        
        <div class="flight-results">
            <?php foreach ($flights as $flight): ?>
                <div class="flight-card">
                    <div class="flight-header">
                        <div class="airline-info">
                            <div class="airline-logo"><?php echo $flight['airline_code']; ?></div>
                            <div>
                                <h4><?php echo $flight['airline_name']; ?></h4>
                                <p><?php echo $flight['flight_number']; ?></p>
                            </div>
                        </div>
                        <div class="flight-price">
                            Rp <?php echo number_format($flight['price'], 0, ',', '.'); ?>
                            <p class="price-info">per penumpang</p>
                        </div>
                    </div>
                    <div class="flight-details">
                        <div class="flight-time">
                            <div class="time"><?php echo date('H:i', strtotime($flight['departure_time'])); ?></div>
                            <div class="location"><?php echo $origin_airport['city']; ?></div>
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
                            <div class="location"><?php echo $destination_airport['city']; ?></div>
                            <div class="date"><?php echo date('d M Y', strtotime($flight['arrival_time'])); ?></div>
                        </div>
                        <div class="flight-seats">
                            <p><?php echo $flight['available_seats']; ?> kursi tersedia</p>
                        </div>
                        <div class="flight-action">
                            <a href="booking.php?flight_id=<?php echo $flight['flight_id']; ?>&passengers=<?php echo $passengers; ?>" class="btn btn-primary">Pilih</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
    .search-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
        flex-wrap: wrap;
        gap: 20px;
    }
    
    .search-header h2 {
        font-size: 28px;
        color: #0056b3;
    }
    
    .search-info p {
        margin-bottom: 5px;
        color: #666;
    }
    
    .filter-options {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
    }
    
    .filter-btn {
        padding: 8px 15px;
        background-color: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    
    .filter-btn:hover, .filter-btn.active {
        background-color: #0056b3;
        color: white;
        border-color: #0056b3;
    }
    
    .no-results {
        text-align: center;
        padding: 50px 0;
        color: #666;
    }
    
    .no-results i {
        font-size: 48px;
        margin-bottom: 20px;
        color: #ccc;
    }
    
    .no-results h3 {
        font-size: 24px;
        margin-bottom: 15px;
    }
    
    .price-info {
        font-size: 14px;
        color: #666;
        font-weight: normal;
    }
    
    .flight-seats {
        text-align: center;
    }
    
    .flight-seats p {
        color: #666;
        font-size: 14px;
    }
    
    @media (max-width: 768px) {
        .search-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .flight-details {
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
    }
</style>

<?php require_once 'includes/footer.php'; ?>