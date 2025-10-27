<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start(); // HARUS di baris pertama
require_once 'config/db.php'; // Panggil DB untuk koneksi & fungsi

// Proses login HANYA jika ada data POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = clean_input($_POST["username"]);
    $password = clean_input($_POST["password"]);
    
    // Validasi input
    if (empty($username) || empty($password)) {
        $error = "Username dan password harus diisi";
    } else {
        // Query untuk mendapatkan user
        $query = "SELECT user_id, username, password_hash, full_name, role FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows == 1) {
            
            // HAPUS BARIS FATAL ERROR: $user = $result->fetch_assoc();
            
            // Bind hasil ke variabel
            $stmt->bind_result($user_id, $username_db, $password_hash, $full_name, $role);
            $stmt->fetch(); // Ambil data

            // Buat array $user secara manual
            $user = [
                'user_id' => $user_id,
                'username' => $username_db,
                'password_hash' => $password_hash,
                'full_name' => $full_name,
                'role' => $role
            ];
            
            // Verifikasi password (HANYA SATU KALI)
            if (password_verify($password, $user['password_hash'])) {
                // Set session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                
                // Redirect ke halaman yang sesuai
                if ($user['role'] == 'admin') {
                    header("Location: admin/index.php");
                } else {
                    header("Location: index.php");
                }
                exit(); // PENTING
            } else {
                $error = "Password salah";
            }
        } else {
            $error = "Username atau email tidak ditemukan";
        }
        $stmt->close(); // Tambahkan ini untuk menutup statement
    }
} // Akhir dari blok "if POST"

// ---- BAGIAN HTML DIMULAI DI SINI ----
require_once 'includes/header.php';

// Tampilkan pesan sukses dari halaman registrasi (jika ada)
if (isset($_SESSION['flash_success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['flash_success'] . '</div>';
    unset($_SESSION['flash_success']); // Hapus pesan agar tidak muncul lagi
}
?>

<div class="container">
    <div class="auth-container">
        <div class="auth-header">
            <h2>Login</h2>
            <p>Masuk ke akun Anda untuk memesan tiket</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form action="login.php" method="post" class="auth-form needs-validation" novalidate>
            <div class="form-group">
                <label for="username">Username atau Email</label>
                <input type="text" class="form-control" id="username" name="username" required>
                <div class="invalid-feedback">
                    Username atau email harus diisi.
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
            </div>
            
            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Ingat saya</label>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        
        <div class="auth-footer">
            <p>Belum punya akun? <a href="register.php">Daftar sekarang</a></p>
            <p><a href="forgot_password.php">Lupa password?</a></p>
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
        align-items: center;
        margin-top: 10px;
    }
    
    .form-check-input {
        margin-right: 10px;
    }
</style>

<?php require_once 'includes/footer.php'; ?>