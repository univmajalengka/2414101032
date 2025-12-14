// script.js - Updated for modern UI

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('bookingForm');
    if (form) {
        const packageSelect = document.getElementById('paket_wisata');
        const participantInput = document.getElementById('jumlah_peserta');
        const priceInput = document.getElementById('harga_paket');
        const totalInput = document.getElementById('jumlah_tagihan');

        // Fungsi untuk menghitung total
        function calculateTotal() {
            const package = packageSelect.value;
            const participants = parseInt(participantInput.value) || 0;
            let price = 0;

            // Tentukan harga berdasarkan paket
            switch(package) {
                case '1 Hari':
                    price = 600000;
                    break;
                case '2 Hari 1 Malam':
                    price = 1000000;
                    break;
                case '3 Hari 2 Malam':
                    price = 1500000;
                    break;
            }

            // Perhitungan total
            const total = participants * price;

            // Update field harga dan total dengan format Rupiah
            priceInput.value = 'Rp ' + price.toLocaleString('id-ID');
            totalInput.value = 'Rp ' + total.toLocaleString('id-ID');
        }

        // Event listener untuk perubahan pada paket atau jumlah peserta
        packageSelect.addEventListener('change', calculateTotal);
        participantInput.addEventListener('input', calculateTotal);

        // Panggil fungsi calculateTotal saat halaman dimuat (untuk mode edit)
        if(packageSelect.value){
            calculateTotal();
        }

        // Validasi form saat submit
        form.addEventListener('submit', function(event) {
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');
            
            // Hapus semua pesan error yang ada
            form.querySelectorAll('.error').forEach(msg => msg.remove());

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = 'red'; // Beri indikasi visual
                    const error = document.createElement('div');
                    error.className = 'error';
                    error.textContent = 'Field ini harus diisi.';
                    field.parentNode.insertBefore(error, field.nextSibling);
                } else {
                    field.style.borderColor = ''; // Hapus indikator jika sudah terisi
                }
            });

            if (!isValid) {
                event.preventDefault(); // Mencegah form dikirim jika tidak valid
            }
        });
    }
});

// ... (kode JavaScript yang sudah ada) ...

document.addEventListener('DOMContentLoaded', function() {
    // ... (kode event listener yang sudah ada) ...

    // --- Logika untuk Galeri Video YouTube ---
    const videoGalleryItems = document.querySelectorAll('.video-gallery-item');

    videoGalleryItems.forEach(item => {
        item.addEventListener('click', function() {
            // Ambil ID video dari atribut data-video-id
            const videoId = this.dataset.videoId;
            
            // Buat elemen iframe
            const iframe = document.createElement('iframe');
            iframe.src = `https://www.youtube.com/embed/${videoId}?autoplay=1&mute=1&rel=0`; // mute=1 penting untuk autoplay
            iframe.frameBorder = '0';
            iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
            iframe.allowFullscreen = true;

            // Ganti konten wrapper dengan iframe
            this.innerHTML = '';
            this.appendChild(iframe);
        });
    });
});