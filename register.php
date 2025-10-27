<?php
ini_set('display_errors', 1); // Tambahkan ini
error_reporting(E_ALL); // Tambahkan ini

// ...sisa kode...
session_start(); // HARUS di baris pertama
require_once 'config/db.php'; // Panggil DB untuk koneksi & fungsi

// Proses registrasi HANYA jika ada data POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = clean_input($_POST["username"]);
    $email = clean_input($_POST["email"]);
    $password = clean_input($_POST["password"]);
    $confirm_password = clean_input($_POST["confirm_password"]);
    $full_name = clean_input($_POST["full_name"]);
    $phone_number = clean_input($_POST["phone_number"]);
    
    // Validasi input
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "Username harus diisi";
    } elseif (strlen($username) < 5) {
        $errors[] = "Username minimal 5 karakter";
    }
    
    if (empty($email)) {
        $errors[] = "Email harus diisi";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid";
    }
    
    if (empty($password)) {
        $errors[] = "Password harus diisi";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password minimal 8 karakter";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Konfirmasi password tidak cocok";
    }
    
    if (empty($full_name)) {
        $errors[] = "Nama lengkap harus diisi";
    }
    
    // Cek apakah username atau email sudah terdaftar
    // Cek apakah username atau email sudah terdaftar
    if (empty($errors)) {
        $check_query = "SELECT username, email FROM users WHERE username = ? OR email = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("ss", $username, $email);
        $check_stmt->execute();
        $check_stmt->store_result(); // Simpan hasil
        
        if ($check_stmt->num_rows > 0) {
            
            // --- INI BARIS YANG MUNGKIN HILANG ---
            // Bind hasil query ke variabel PHP
            $check_stmt->bind_result($username_db, $email_db); 
            // ------------------------------------

            while($check_stmt->fetch()) { // Loop dan ambil data
                if ($username_db === $username) { // Cek variabel
                    $errors[] = "Username sudah digunakan";
                }
                if ($email_db === $email) { // Cek variabel
                    $errors[] = "Email sudah digunakan";
                }
            }
        }
        $check_stmt->close(); // Selalu tutup statement
    }
    
    // Jika tidak ada error, simpan data ke database
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $insert_query = "INSERT INTO users (username, email, password_hash, full_name, phone_number) VALUES (?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("sssss", $username, $email, $password_hash, $full_name, $phone_number);
        
        if ($insert_stmt->execute()) {
            // BERHASIL: Set "flash message" untuk ditampilkan di halaman login
            $_SESSION['flash_success'] = "Registrasi berhasil! Silakan login dengan akun Anda.";
            
            // Pindahkan pengguna ke halaman login
            header("Location: login.php");
            exit(); // PENTING: Hentikan eksekusi skrip
        } else {
            $errors[] = "Terjadi kesalahan. Silakan coba lagi.";
        }
    }
} // Akhir dari "if POST"

// ---- BAGIAN HTML DIMULAI DI SINI ----
// Kode ini hanya akan jalan jika BUKAN proses POST, atau jika registrasi GAGAL
require_once 'includes/header.php';
?>

<div class="container">
    <div class="auth-container">
        <div class="auth-header">
            <h2>Daftar Akun</h2>
            <p>Buat akun baru untuk memesan tiket</p>
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
            
        <form action="register.php" method="post" class="auth-form needs-validation" novalidate>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
                <div class="invalid-feedback">
                    Username harus diisi.
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
                <div class="invalid-feedback">
                    Email harus diisi dan format valid.
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-input-container">
                    <input type="password" class="form-control" id="password" name="password" required>
                    <button type="button" class="toggle-password" data-target="#password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="invalid-feedback">
                    Password harus diisi.
                </div>
                <small class="form-text text-muted">Password minimal 8 karakter</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password</label>
                <div class="password-input-container">
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    <button type="button" class="toggle-password" data-target="#confirm_password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="invalid-feedback">
                    Konfirmasi password harus diisi.
                </div>
            </div>
            
            <div class="form-group">
                <label for="full_name">Nama Lengkap</label>
                <input type="text" class="form-control" id="full_name" name="full_name" required>
                <div class="invalid-feedback">
                    Nama lengkap harus diisi.
                </div>
            </div>
            
            <div class="form-group">
                <label for="phone_number">Nomor Telepon</label>
                <input type="tel" class="form-control" id="phone_number" name="phone_number">
            </div>
            
            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                    <label class="form-check-label" for="terms">
                        Saya menyetujui <a href="#">Syarat dan Ketentuan</a>
                    </label>
                    <div class="invalid-feedback">
                        Anda harus menyetujui syarat dan ketentuan.
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Daftar</button>
        </form>
        
        <div class="auth-footer">
            <p>Sudah punya akun? <a href="login.php">Login</a></p>
        </div>
    </div>
</div>

<style>
    .password-input-container {
        position: relative;
    }
    
    .toggle-password {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #666;
        cursor: pointer;
    }
    
    .form-check {
        display: flex;
        align-items: flex-start;
        margin-top: 10px;
    }
    
    .form-check-input {
        margin-right: 10px;
        margin-top: 4px;
    }
    
    .form-text {
        font-size: 14px;
        margin-top: 5px;
    }
    
    .text-center {
        text-align: center;
        margin-top: 20px;
    }
</style>

<?php require_once 'includes/footer.php'; ?>