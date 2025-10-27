<?php
// Cek apakah user sudah login dan memiliki role admin
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/db.php';

// Mendapatkan data pesanan terbaru
 $bookings_query = "SELECT b.*, u.username, u.full_name,
                  f.flight_number, f.departure_time,
                  a.airline_name,
                  o.city as origin_city,
                  d.city as destination_city
                  FROM bookings b 
                  JOIN users u ON b.user_id = u.user_id
                  JOIN flights f ON b.flight_id = f.flight_id 
                  JOIN airlines a ON f.airline_id = a.airline_id 
                  JOIN airports o ON f.origin_code = o.airport_code
                  JOIN airports d ON f.destination_code = d.airport_code
                  ORDER BY b.booking_date DESC
                  LIMIT 10";
 $bookings_result = $conn->query($bookings_query);

 $bookings = [];
if ($bookings_result->num_rows > 0) {
    while ($row = $bookings_result->fetch_assoc()) {
        $bookings[] = $row;
    }
}

// Mendapatkan statistik
 $total_bookings_query = "SELECT COUNT(*) as total FROM bookings";
 $total_bookings_result = $conn->query($total_bookings_query);
 $total_bookings = $total_bookings_result->fetch_assoc()['total'];

 $pending_bookings_query = "SELECT COUNT(*) as total FROM bookings WHERE status = 'pending'";
 $pending_bookings_result = $conn->query($pending_bookings_query);
 $pending_bookings = $pending_bookings_result->fetch_assoc()['total'];

 $confirmed_bookings_query = "SELECT COUNT(*) as total FROM bookings WHERE status = 'confirmed'";
 $confirmed_bookings_result = $conn->query($confirmed_bookings_query);
 $confirmed_bookings = $confirmed_bookings_result->fetch_assoc()['total'];

 $total_flights_query = "SELECT COUNT(*) as total FROM flights";
// Pastikan baris ini ada SEBELUM baris 48
$_flights_query = "SELECT * FROM flights";

// Sekarang baris 48 akan berfungsi
$result = $conn->query($_flights_query);
$sql_count = "SELECT COUNT(*) AS total FROM flights";
$total_flights_result = $conn->query($sql_count); // $conn bisa jadi $db
 $total_flights = $total_flights_result->fetch_assoc()['total'];
 $total_airlines_query = "SELECT COUNT(*) as total FROM airlines";
 $total_airlines_result = $conn->query($total_airlines_query);
 $total_airlines = $total_airlines_result->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Bandara BIJB Kertajati</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            text-align: center;
        }
        
        .stat-card i {
            font-size: 36px;
            margin-bottom: 15px;
            color: #0056b3;
        }
        
        .stat-card h3 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .stat-card p {
            color: #666;
        }
        
        .recent-bookings {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .recent-bookings h3 {
            font-size: 20px;
            margin-bottom: 20px;
            color: #0056b3;
        }
        
        .booking-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .booking-table th, .booking-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .booking-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        .booking-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
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
        
        .view-all {
            text-align: right;
            margin-top: 15px;
        }
        
        .view-all a {
            color: #0056b3;
            text-decoration: none;
            font-weight: 500;
        }
        
        .view-all a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-sidebar">
            <h3>Admin Panel</h3>
            <ul>
                <li><a href="index.php" class="active">Dashboard</a></li>
                <li><a href="manage_flights.php">Kelola Jadwal Penerbangan</a></li>
                <li><a href="manage_airlines.php">Kelola Maskapai</a></li>
                <li><a href="../index.php">Lihat Website</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
        
        <div class="admin-content">
            <div class="admin-header">
                <h2>Dashboard Admin</h2>
                <p>Selamat datang, <?php echo $_SESSION['full_name']; ?>!</p>
            </div>
            
            <div class="admin-dashboard">
                <div class="stat-card">
                    <i class="fas fa-ticket-alt"></i>
                    <h3><?php echo $total_bookings; ?></h3>
                    <p>Total Pesanan</p>
                </div>
                
                <div class="stat-card">
                    <i class="fas fa-clock"></i>
                    <h3><?php echo $pending_bookings; ?></h3>
                    <p>Pesanan Menunggu Pembayaran</p>
                </div>
                
                <div class="stat-card">
                    <i class="fas fa-check-circle"></i>
                    <h3><?php echo $confirmed_bookings; ?></h3>
                    <p>Pesanan Terkonfirmasi</p>
                </div>
                
                <div class="stat-card">
                    <i class="fas fa-plane"></i>
                    <h3><?php echo $total_flights; ?></h3>
                    <p>Total Jadwal Penerbangan</p>
                </div>
                
                <div class="stat-card">
                    <i class="fas fa-building"></i>
                    <h3><?php echo $total_airlines; ?></h3>
                    <p>Total Maskapai</p>
                </div>
            </div>
            
            <div class="recent-bookings">
                <h3>Pesanan Terbaru</h3>
                
                <?php if (empty($bookings)): ?>
                    <p>Belum ada pesanan.</p>
                <?php else: ?>
                    <table class="booking-table">
                        <thead>
                            <tr>
                                <th>Kode Booking</th>
                                <th>Nama Penumpang</th>
                                <th>Penerbangan</th>
                                <th>Tanggal Keberangkatan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td><?php echo $booking['booking_code']; ?></td>
                                    <td><?php echo $booking['full_name']; ?></td>
                                    <td><?php echo $booking['airline_name'] . ' ' . $booking['flight_number']; ?></td>
                                    <td><?php echo date('d M Y', strtotime($booking['departure_time'])); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $booking['status']; ?>">
                                            <?php 
                                            if ($booking['status'] == 'pending') {
                                                echo 'Menunggu Pembayaran';
                                            } elseif ($booking['status'] == 'confirmed') {
                                                echo 'Terkonfirmasi';
                                            } else {
                                                echo 'Dibatalkan';
                                            }
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="view_booking.php?id=<?php echo $booking['booking_id']; ?>" class="btn btn-sm btn-primary">Lihat</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <div class="view-all">
                        <a href="view_all_bookings.php">Lihat Semua Pesanan</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>