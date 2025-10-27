<?php
require_once 'includes/header.php';
require_once 'config/db.php';

// Proses form kontak
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = clean_input($_POST["name"]);
    $email = clean_input($_POST["email"]);
    $subject = clean_input($_POST["subject"]);
    $message = clean_input($_POST["message"]);
    
    // Validasi input
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Nama harus diisi";
    }
    if (empty($email)) {
        $errors[] = "Email harus diisi";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid";
    }
    if (empty($subject)) {
        $errors[] = "Subjek harus diisi";
    }
    if (empty($message)) {
        $errors[] = "Pesan harus diisi";
    }
    
    // Jika tidak ada error, kirim email (simulasi)
    if (empty($errors)) {
        // Dalam implementasi nyata, Anda akan mengirim email di sini
        $success = "Pesan Anda telah terkirim. Kami akan segera menghubungi Anda.";
    }
}
?>

<div class="container">
    <div class="page-header">
        <h1>Hubungi Kami</h1>
        <p>Kami siap membantu kebutuhan perjalanan Anda</p>
    </div>
    
    <div class="contact-content">
        <div class="contact-info">
            <h2>Informasi Kontak</h2>
            <div class="contact-details">
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <h3>Alamat</h3>
                        <p>Jl. Bandara Kertajati, Kec. Kertajati, Kabupaten Majalengka, Jawa Barat 45457</p>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <h3>Telepon</h3>
                        <p>(0233) 123456</p>
                        <p>Hotline: 0800-1234-5678</p>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <h3>Email</h3>
                        <p>info@kertajatiairport.id</p>
                        <p>customercare@kertajatiairport.id</p>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <h3>Jam Operasional</h3>
                        <p>Senin - Jumat: 06:00 - 22:00</p>
                        <p>Sabtu - Minggu: 06:00 - 22:00</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="contact-form">
            <h2>Kirim Pesan</h2>
            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form action="contact.php" method="post" class="needs-validation" novalidate>
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="invalid-feedback">
                            Nama harus diisi.
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="invalid-feedback">
                            Email harus diisi dan format valid.
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="subject">Subjek</label>
                    <input type="text" class="form-control" id="subject" name="subject" required>
                    <div class="invalid-feedback">
                        Subjek harus diisi.
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="message">Pesan</label>
                    <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    <div class="invalid-feedback">
                        Pesan harus diisi.
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Kirim Pesan</button>
            </form>
        </div>
    </div>
    
    <div class="map-section">
        <h2>Lokasi Bandara</h2>
        <div class="map-container">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.7329419413!2d108.15774151477057!3d-6.663866995166819!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6f06c7e8e8e8e8f%3A0x8e8e8e8e8e8e8e8e!2sBandara%20Internasional%20Jawa%20Barat!5e0!3m2!1sid!2sid!4v1621234567890!5m2!1sid!2sid" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
</div>

<style>
    .page-header {
        text-align: center;
        margin-bottom: 40px;
        padding: 40px 0;
        background: linear-gradient(rgba(0, 86, 179, 0.1), rgba(0, 86, 179, 0.1));
        border-radius: 8px;
    }
    
    .page-header h1 {
        font-size: 36px;
        color: #0056b3;
        margin-bottom: 10px;
    }
    
    .page-header p {
        font-size: 18px;
        color: #666;
    }
    
    .contact-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        margin-bottom: 50px;
    }
    
    .contact-info, .contact-form {
        background-color: white;
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .contact-info h2, .contact-form h2 {
        font-size: 24px;
        color: #0056b3;
        margin-bottom: 20px;
    }
    
    .contact-details {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    
    .contact-item {
        display: flex;
        gap: 15px;
    }
    
    .contact-item i {
        font-size: 24px;
        color: #0056b3;
        margin-top: 5px;
    }
    
    .contact-item h3 {
        font-size: 18px;
        margin-bottom: 5px;
        color: #333;
    }
    
    .contact-item p {
        color: #666;
        margin: 0;
    }
    
    .form-row {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
    }
    
    .form-group {
        flex: 1;
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
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
    }
    
    .form-control:focus {
        border-color: #0056b3;
        outline: none;
    }
    
    textarea.form-control {
        resize: vertical;
    }
    
    .map-section {
        background-color: white;
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .map-section h2 {
        font-size: 24px;
        color: #0056b3;
        margin-bottom: 20px;
        text-align: center;
    }
    
    .map-container {
        border-radius: 8px;
        overflow: hidden;
    }
    
    @media (max-width: 768px) {
        .contact-content {
            grid-template-columns: 1fr;
        }
        
        .form-row {
            flex-direction: column;
        }
    }
</style>

<?php require_once 'includes/footer.php'; ?>