<?php
// Cek apakah user sudah login dan memiliki role admin
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/db.php';

// Mendapatkan data jadwal penerbangan
 $flights_query = "SELECT f.*, a.airline_name, a.airline_code,
                 o.airport_name as origin_name, o.city as origin_city,
                 d.airport_name as destination_name, d.city as destination_city
                 FROM flights f 
                 JOIN airlines a ON f.airline_id = a.airline_id 
                 JOIN airports o ON f.origin_code = o.airport_code
                 JOIN airports d ON f.destination_code = d.airport_code
                 ORDER BY f.departure_time";
 $flights_result = $conn->query($flights_query);

 $flights = [];
if ($flights_result->num_rows > 0) {
    while ($row = $flights_result->fetch_assoc()) {
        $flights[] = $row;
    }
}

// Mendapatkan data maskapai untuk dropdown
 $airlines_query = "SELECT * FROM airlines ORDER BY airline_name";
 $airlines_result = $conn->query($airlines_query);
 $airlines = [];

if ($airlines_result->num_rows > 0) {
    while ($row = $airlines_result->fetch_assoc()) {
        $airlines[] = $row;
    }
}

// Mendapatkan data bandara untuk dropdown
 $airports_query = "SELECT * FROM airports ORDER BY city";
 $airports_result = $conn->query($airports_query);
 $airports = [];

if ($airports_result->num_rows > 0) {
    while ($row = $airports_result->fetch_assoc()) {
        $airports[] = $row;
    }
}

// Proses tambah/edit/hapus jadwal penerbangan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = isset($_POST['action']) ? clean_input($_POST['action']) : '';
    
    if ($action == 'add') {
        $airline_id = (int)clean_input($_POST['airline_id']);
        $flight_number = clean_input($_POST['flight_number']);
        $origin_code = clean_input($_POST['origin_code']);
        $destination_code = clean_input($_POST['destination_code']);
        $departure_time = clean_input($_POST['departure_time']);
        $arrival_time = clean_input($_POST['arrival_time']);
        $price = (float)clean_input($_POST['price']);
        $available_seats = (int)clean_input($_POST['available_seats']);
        
        // Validasi input
        $errors = [];
        
        if (empty($airline_id)) {
            $errors[] = "Maskapai harus dipilih";
        }
        if (empty($flight_number)) {
            $errors[] = "Nomor penerbangan harus diisi";
        }
        if (empty($origin_code)) {
            $errors[] = "Bandara asal harus dipilih";
        }
        if (empty($destination_code)) {
            $errors[] = "Bandara tujuan harus dipilih";
        }
        if (empty($departure_time)) {
            $errors[] = "Waktu keberangkatan harus diisi";
        }
        if (empty($arrival_time)) {
            $errors[] = "Waktu kedatangan harus diisi";
        }
        if (empty($price) || $price <= 0) {
            $errors[] = "Harga harus diisi dan lebih dari 0";
        }
        if (empty($available_seats) || $available_seats <= 0) {
            $errors[] = "Jumlah kursi harus diisi dan lebih dari 0";
        }
        
        // Jika tidak ada error, simpan data
        if (empty($errors)) {
            $insert_query = "INSERT INTO flights (airline_id, flight_number, origin_code, destination_code, departure_time, arrival_time, price, available_seats) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param("issssddi", $airline_id, $flight_number, $origin_code, $destination_code, $departure_time, $arrival_time, $price, $available_seats);
            
            if ($insert_stmt->execute()) {
                $success = "Jadwal penerbangan berhasil ditambahkan!";
                header("Location: manage_flights.php?success=" . urlencode($success));
                exit();
            } else {
                $error = "Terjadi kesalahan. Silakan coba lagi.";
            }
        }
    } elseif ($action == 'edit') {
        $flight_id = (int)clean_input($_POST['flight_id']);
        $airline_id = (int)clean_input($_POST['airline_id']);
        $flight_number = clean_input($_POST['flight_number']);
        $origin_code = clean_input($_POST['origin_code']);
        $destination_code = clean_input($_POST['destination_code']);
        $departure_time = clean_input($_POST['departure_time']);
        $arrival_time = clean_input($_POST['arrival_time']);
        $price = (float)clean_input($_POST['price']);
        $available_seats = (int)clean_input($_POST['available_seats']);
        
        // Validasi input
        $errors = [];
        
        if (empty($flight_id)) {
            $errors[] = "ID penerbangan tidak valid";
        }
        if (empty($airline_id)) {
            $errors[] = "Maskapai harus dipilih";
        }
        if (empty($flight_number)) {
            $errors[] = "Nomor penerbangan harus diisi";
        }
        if (empty($origin_code)) {
            $errors[] = "Bandara asal harus dipilih";
        }
        if (empty($destination_code)) {
            $errors[] = "Bandara tujuan harus dipilih";
        }
        if (empty($departure_time)) {
            $errors[] = "Waktu keberangkatan harus diisi";
        }
        if (empty($arrival_time)) {
            $errors[] = "Waktu kedatangan harus diisi";
        }
        if (empty($price) || $price <= 0) {
            $errors[] = "Harga harus diisi dan lebih dari 0";
        }
        if (empty($available_seats) || $available_seats <= 0) {
            $errors[] = "Jumlah kursi harus diisi dan lebih dari 0";
        }
        
        // Jika tidak ada error, update data
        if (empty($errors)) {
            $update_query = "UPDATE flights SET airline_id = ?, flight_number = ?, origin_code = ?, destination_code = ?, departure_time = ?, arrival_time = ?, price = ?, available_seats = ? WHERE flight_id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("issssddii", $airline_id, $flight_number, $origin_code, $destination_code, $departure_time, $arrival_time, $price, $available_seats, $flight_id);
            
            if ($update_stmt->execute()) {
                $success = "Jadwal penerbangan berhasil diperbarui!";
                header("Location: manage_flights.php?success=" . urlencode($success));
                exit();
            } else {
                $error = "Terjadi kesalahan. Silakan coba lagi.";
            }
        }
    } elseif ($action == 'delete') {
        $flight_id = (int)clean_input($_POST['flight_id']);
        
        if (empty($flight_id)) {
            $error = "ID penerbangan tidak valid";
        } else {
            // Cek apakah ada booking yang terkait dengan penerbangan ini
            $check_query = "SELECT COUNT(*) as count FROM bookings WHERE flight_id = ?";
            $check_stmt = $conn->prepare($check_query);
            $check_stmt->bind_param("i", $flight_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            $booking_count = $check_result->fetch_assoc()['count'];
            
            if ($booking_count > 0) {
                $error = "Tidak dapat menghapus jadwal penerbangan yang sudah memiliki pesanan";
            } else {
                $delete_query = "DELETE FROM flights WHERE flight_id = ?";
                $delete_stmt = $conn->prepare($delete_query);
                $delete_stmt->bind_param("i", $flight_id);
                
                if ($delete_stmt->execute()) {
                    $success = "Jadwal penerbangan berhasil dihapus!";
                    header("Location: manage_flights.php?success=" . urlencode($success));
                    exit();
                } else {
                    $error = "Terjadi kesalahan. Silakan coba lagi.";
                }
            }
        }
    }
}

// Mendapatkan data jadwal penerbangan untuk edit
 $edit_flight = null;
if (isset($_GET['edit_id'])) {
    $edit_id = (int)clean_input($_GET['edit_id']);
    
    $flight_query = "SELECT * FROM flights WHERE flight_id = ?";
    $flight_stmt = $conn->prepare($flight_query);
    $flight_stmt->bind_param("i", $edit_id);
    $flight_stmt->execute();
    $flight_result = $flight_stmt->get_result();
    
    if ($flight_result->num_rows > 0) {
        $edit_flight = $flight_result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Jadwal Penerbangan - Bandara BIJB Kertajati</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .admin-actions {
            margin-bottom: 20px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .data-table th, .data-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .data-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        .data-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .table-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 14px;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
            overflow: auto;
        }
        
        .modal-content {
            background-color: white;
            margin: 50px auto;
            padding: 30px;
            width: 90%;
            max-width: 600px;
            border-radius: 8px;
            position: relative;
        }
        
        .modal-header {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .modal-header h3 {
            font-size: 22px;
            color: #333;
        }
        
        .close-modal {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            color: #999;
        }
        
        .close-modal:hover {
            color: #333;
        }
        
        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            flex: 1;
            min-width: 200px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        .form-control:focus {
            border-color: #0056b3;
            outline: none;
        }
        
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-sidebar">
            <h3>Admin Panel</h3>
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="manage_flights.php" class="active">Kelola Jadwal Penerbangan</a></li>
                <li><a href="manage_airlines.php">Kelola Maskapai</a></li>
                <li><a href="../index.php">Lihat Website</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
        
        <div class="admin-content">
            <div class="admin-header">
                <h2>Kelola Jadwal Penerbangan</h2>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addFlightModal">
                    <i class="fas fa-plus"></i> Tambah Jadwal
                </button>
            </div>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <div class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Maskapai</th>
                            <th>Nomor Penerbangan</th>
                            <th>Rute</th>
                            <th>Waktu Keberangkatan</th>
                            <th>Waktu Kedatangan</th>
                            <th>Harga</th>
                            <th>Kursi Tersedia</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($flights)): ?>
                            <tr>
                                <td colspan="9" style="text-align: center;">Tidak ada jadwal penerbangan</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($flights as $flight): ?>
                                <tr id="flight-row-<?php echo $flight['flight_id']; ?>">
                                    <td><?php echo $flight['flight_id']; ?></td>
                                    <td><?php echo $flight['airline_name']; ?></td>
                                    <td><?php echo $flight['flight_number']; ?></td>
                                    <td><?php echo $flight['origin_city'] . ' - ' . $flight['destination_city']; ?></td>
                                    <td><?php echo date('d M Y H:i', strtotime($flight['departure_time'])); ?></td>
                                    <td><?php echo date('d M Y H:i', strtotime($flight['arrival_time'])); ?></td>
                                    <td>Rp <?php echo number_format($flight['price'], 0, ',', '.'); ?></td>
                                    <td><?php echo $flight['available_seats']; ?></td>
                                    <td>
                                        <div class="table-actions">
                                            <a href="manage_flights.php?edit_id=<?php echo $flight['flight_id']; ?>" class="btn btn-sm btn-edit">Edit</a>
                                            <button type="button" class="btn btn-sm btn-delete" onclick="deleteFlight(<?php echo $flight['flight_id']; ?>)">Hapus</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Modal Tambah/Edit Jadwal Penerbangan -->
    <div id="flightModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Tambah Jadwal Penerbangan</h3>
                <span class="close-modal">&times;</span>
            </div>
            
            <form id="flightForm" method="post">
                <input type="hidden" id="flightId" name="flight_id">
                <input type="hidden" id="formAction" name="action" value="add">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="airline_id">Maskapai</label>
                        <select id="airline_id" name="airline_id" class="form-control" required>
                            <option value="">Pilih Maskapai</option>
                            <?php foreach ($airlines as $airline): ?>
                                <option value="<?php echo $airline['airline_id']; ?>" <?php echo (isset($edit_flight) && $edit_flight['airline_id'] == $airline['airline_id']) ? 'selected' : ''; ?>>
                                    <?php echo $airline['airline_name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="flight_number">Nomor Penerbangan</label>
                        <input type="text" id="flight_number" name="flight_number" class="form-control" value="<?php echo (isset($edit_flight)) ? $edit_flight['flight_number'] : ''; ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="origin_code">Bandara Asal</label>
                        <select id="origin_code" name="origin_code" class="form-control" required>
                            <option value="">Pilih Bandara Asal</option>
                            <?php foreach ($airports as $airport): ?>
                                <option value="<?php echo $airport['airport_code']; ?>" <?php echo (isset($edit_flight) && $edit_flight['origin_code'] == $airport['airport_code']) ? 'selected' : ''; ?>>
                                    <?php echo $airport['airport_name'] . ' (' . $airport['airport_code'] . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="destination_code">Bandara Tujuan</label>
                        <select id="destination_code" name="destination_code" class="form-control" required>
                            <option value="">Pilih Bandara Tujuan</option>
                            <?php foreach ($airports as $airport): ?>
                                <option value="<?php echo $airport['airport_code']; ?>" <?php echo (isset($edit_flight) && $edit_flight['destination_code'] == $airport['airport_code']) ? 'selected' : ''; ?>>
                                    <?php echo $airport['airport_name'] . ' (' . $airport['airport_code'] . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="departure_time">Waktu Keberangkatan</label>
                        <input type="datetime-local" id="departure_time" name="departure_time" class="form-control" value="<?php echo (isset($edit_flight)) ? date('Y-m-d\TH:i', strtotime($edit_flight['departure_time'])) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="arrival_time">Waktu Kedatangan</label>
                        <input type="datetime-local" id="arrival_time" name="arrival_time" class="form-control" value="<?php echo (isset($edit_flight)) ? date('Y-m-d\TH:i', strtotime($edit_flight['arrival_time'])) : ''; ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Harga (Rp)</label>
                        <input type="number" id="price" name="price" class="form-control" value="<?php echo (isset($edit_flight)) ? $edit_flight['price'] : ''; ?>" min="0" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="available_seats">Jumlah Kursi</label>
                        <input type="number" id="available_seats" name="available_seats" class="form-control" value="<?php echo (isset($edit_flight)) ? $edit_flight['available_seats'] : ''; ?>" min="1" required>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal-btn">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Modal handling
            const modalTriggers = document.querySelectorAll('[data-toggle="modal"]');
            const modal = document.getElementById('flightModal');
            const closeButtons = document.querySelectorAll('.close-modal, .close-modal-btn');
            const modalTitle = document.getElementById('modalTitle');
            const formAction = document.getElementById('formAction');
            const flightId = document.getElementById('flightId');
            const flightForm = document.getElementById('flightForm');
            
            modalTriggers.forEach(trigger => {
                trigger.addEventListener('click', function() {
                    modalTitle.textContent = 'Tambah Jadwal Penerbangan';
                    formAction.value = 'add';
                    flightId.value = '';
                    flightForm.reset();
                    modal.style.display = 'block';
                });
            });
            
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    modal.style.display = 'none';
                });
            });
            
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
            
            // Check if we're in edit mode
            <?php if (isset($edit_flight)): ?>
                modalTitle.textContent = 'Edit Jadwal Penerbangan';
                formAction.value = 'edit';
                flightId.value = '<?php echo $edit_flight['flight_id']; ?>';
                modal.style.display = 'block';
            <?php endif; ?>
        });
        
        function deleteFlight(flightId) {
            if (confirm('Apakah Anda yakin ingin menghapus jadwal penerbangan ini?')) {
                const form = document.createElement('form');
                form.method = 'post';
                form.action = 'manage_flights.php';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete';
                
                const flightIdInput = document.createElement('input');
                flightIdInput.type = 'hidden';
                flightIdInput.name = 'flight_id';
                flightIdInput.value = flightId;
                
                form.appendChild(actionInput);
                form.appendChild(flightIdInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>