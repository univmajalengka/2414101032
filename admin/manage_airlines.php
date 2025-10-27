<?php
// Cek apakah user sudah login dan memiliki role admin
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/db.php';

// Mendapatkan data maskapai
 $airlines_query = "SELECT * FROM airlines ORDER BY airline_name";
 $airlines_result = $conn->query($airlines_query);

 $airlines = [];
if ($airlines_result->num_rows > 0) {
    while ($row = $airlines_result->fetch_assoc()) {
        $airlines[] = $row;
    }
}

// Proses tambah/edit/hapus maskapai
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = isset($_POST['action']) ? clean_input($_POST['action']) : '';
    
    if ($action == 'add') {
        $airline_name = clean_input($_POST['airline_name']);
        $airline_code = clean_input($_POST['airline_code']);
        
        // Validasi input
        $errors = [];
        
        if (empty($airline_name)) {
            $errors[] = "Nama maskapai harus diisi";
        }
        if (empty($airline_code)) {
            $errors[] = "Kode maskapai harus diisi";
        } elseif (strlen($airline_code) != 2) {
            $errors[] = "Kode maskapai harus 2 karakter";
        }
        
        // Cek apakah kode maskapai sudah ada
        if (empty($errors)) {
            $check_query = "SELECT airline_id FROM airlines WHERE airline_code = ?";
            $check_stmt = $conn->prepare($check_query);
            $check_stmt->bind_param("s", $airline_code);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                $errors[] = "Kode maskapai sudah digunakan";
            }
        }
        
        // Jika tidak ada error, simpan data
        if (empty($errors)) {
            $insert_query = "INSERT INTO airlines (airline_name, airline_code) VALUES (?, ?)";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param("ss", $airline_name, $airline_code);
            
            if ($insert_stmt->execute()) {
                $success = "Maskapai berhasil ditambahkan!";
                header("Location: manage_airlines.php?success=" . urlencode($success));
                exit();
            } else {
                $error = "Terjadi kesalahan. Silakan coba lagi.";
            }
        }
    } elseif ($action == 'edit') {
        $airline_id = (int)clean_input($_POST['airline_id']);
        $airline_name = clean_input($_POST['airline_name']);
        $airline_code = clean_input($_POST['airline_code']);
        
        // Validasi input
        $errors = [];
        
        if (empty($airline_id)) {
            $errors[] = "ID maskapai tidak valid";
        }
        if (empty($airline_name)) {
            $errors[] = "Nama maskapai harus diisi";
        }
        if (empty($airline_code)) {
            $errors[] = "Kode maskapai harus diisi";
        } elseif (strlen($airline_code) != 2) {
            $errors[] = "Kode maskapai harus 2 karakter";
        }
        
        // Cek apakah kode maskapai sudah ada (kecuali untuk maskapai yang sedang diedit)
        if (empty($errors)) {
            $check_query = "SELECT airline_id FROM airlines WHERE airline_code = ? AND airline_id != ?";
            $check_stmt = $conn->prepare($check_query);
            $check_stmt->bind_param("si", $airline_code, $airline_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                $errors[] = "Kode maskapai sudah digunakan";
            }
        }
        
        // Jika tidak ada error, update data
        if (empty($errors)) {
            $update_query = "UPDATE airlines SET airline_name = ?, airline_code = ? WHERE airline_id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("ssi", $airline_name, $airline_code, $airline_id);
            
            if ($update_stmt->execute()) {
                $success = "Maskapai berhasil diperbarui!";
                header("Location: manage_airlines.php?success=" . urlencode($success));
                exit();
            } else {
                $error = "Terjadi kesalahan. Silakan coba lagi.";
            }
        }
    } elseif ($action == 'delete') {
        $airline_id = (int)clean_input($_POST['airline_id']);
        
        if (empty($airline_id)) {
            $error = "ID maskapai tidak valid";
        } else {
            // Cek apakah ada jadwal penerbangan yang terkait dengan maskapai ini
            $check_query = "SELECT COUNT(*) as count FROM flights WHERE airline_id = ?";
            $check_stmt = $conn->prepare($check_query);
            $check_stmt->bind_param("i", $airline_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            $flight_count = $check_result->fetch_assoc()['count'];
            
            if ($flight_count > 0) {
                $error = "Tidak dapat menghapus maskapai yang sudah memiliki jadwal penerbangan";
            } else {
                $delete_query = "DELETE FROM airlines WHERE airline_id = ?";
                $delete_stmt = $conn->prepare($delete_query);
                $delete_stmt->bind_param("i", $airline_id);
                
                if ($delete_stmt->execute()) {
                    $success = "Maskapai berhasil dihapus!";
                    header("Location: manage_airlines.php?success=" . urlencode($success));
                    exit();
                } else {
                    $error = "Terjadi kesalahan. Silakan coba lagi.";
                }
            }
        }
    }
}

// Mendapatkan data maskapai untuk edit
 $edit_airline = null;
if (isset($_GET['edit_id'])) {
    $edit_id = (int)clean_input($_GET['edit_id']);
    
    $airline_query = "SELECT * FROM airlines WHERE airline_id = ?";
    $airline_stmt = $conn->prepare($airline_query);
    $airline_stmt->bind_param("i", $edit_id);
    $airline_stmt->execute();
    $airline_result = $airline_stmt->get_result();
    
    if ($airline_result->num_rows > 0) {
        $edit_airline = $airline_result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Maskapai - Bandara BIJB Kertajati</title>
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
            max-width: 500px;
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
        
        .form-group {
            margin-bottom: 20px;
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
                <li><a href="manage_flights.php">Kelola Jadwal Penerbangan</a></li>
                <li><a href="manage_airlines.php" class="active">Kelola Maskapai</a></li>
                <li><a href="../index.php">Lihat Website</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
        
        <div class="admin-content">
            <div class="admin-header">
                <h2>Kelola Maskapai</h2>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#airlineModal">
                    <i class="fas fa-plus"></i> Tambah Maskapai
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
                            <th>Nama Maskapai</th>
                            <th>Kode Maskapai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($airlines)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center;">Tidak ada maskapai</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($airlines as $airline): ?>
                                <tr id="airline-row-<?php echo $airline['airline_id']; ?>">
                                    <td><?php echo $airline['airline_id']; ?></td>
                                    <td><?php echo $airline['airline_name']; ?></td>
                                    <td><?php echo $airline['airline_code']; ?></td>
                                    <td>
                                        <div class="table-actions">
                                            <a href="manage_airlines.php?edit_id=<?php echo $airline['airline_id']; ?>" class="btn btn-sm btn-edit">Edit</a>
                                            <button type="button" class="btn btn-sm btn-delete" onclick="deleteAirline(<?php echo $airline['airline_id']; ?>)">Hapus</button>
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
    
    <!-- Modal Tambah/Edit Maskapai -->
    <div id="airlineModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Tambah Maskapai</h3>
                <span class="close-modal">&times;</span>
            </div>
            
            <form id="airlineForm" method="post">
                <input type="hidden" id="airlineId" name="airline_id">
                <input type="hidden" id="formAction" name="action" value="add">
                
                <div class="form-group">
                    <label for="airline_name">Nama Maskapai</label>
                    <input type="text" id="airline_name" name="airline_name" class="form-control" value="<?php echo (isset($edit_airline)) ? $edit_airline['airline_name'] : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="airline_code">Kode Maskapai (2 karakter)</label>
                    <input type="text" id="airline_code" name="airline_code" class="form-control" value="<?php echo (isset($edit_airline)) ? $edit_airline['airline_code'] : ''; ?>" maxlength="2" required>
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
            const modal = document.getElementById('airlineModal');
            const closeButtons = document.querySelectorAll('.close-modal, .close-modal-btn');
            const modalTitle = document.getElementById('modalTitle');
            const formAction = document.getElementById('formAction');
            const airlineId = document.getElementById('airlineId');
            const airlineForm = document.getElementById('airlineForm');
            
            modalTriggers.forEach(trigger => {
                trigger.addEventListener('click', function() {
                    modalTitle.textContent = 'Tambah Maskapai';
                    formAction.value = 'add';
                    airlineId.value = '';
                    airlineForm.reset();
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
            <?php if (isset($edit_airline)): ?>
                modalTitle.textContent = 'Edit Maskapai';
                formAction.value = 'edit';
                airlineId.value = '<?php echo $edit_airline['airline_id']; ?>';
                modal.style.display = 'block';
            <?php endif; ?>
        });
        
        function deleteAirline(airlineId) {
            if (confirm('Apakah Anda yakin ingin menghapus maskapai ini?')) {
                const form = document.createElement('form');
                form.method = 'post';
                form.action = 'manage_airlines.php';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete';
                
                const airlineIdInput = document.createElement('input');
                airlineIdInput.type = 'hidden';
                airlineIdInput.name = 'airline_id';
                airlineIdInput.value = airlineId;
                
                form.appendChild(actionInput);
                form.appendChild(airlineIdInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>