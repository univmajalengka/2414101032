// Fungsi untuk menangani form pencarian penerbangan
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');
    
    togglePasswordButtons.forEach(button => {
        button.addEventListener('click', function() {
            const passwordInput = document.querySelector(this.getAttribute('data-target'));
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
    
    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        });
    });
    
    // Dynamic passenger form
    const addPassengerBtn = document.getElementById('add-passenger');
    const passengersContainer = document.getElementById('passengers-container');
    let passengerCount = 1;
    
    if (addPassengerBtn) {
        addPassengerBtn.addEventListener('click', function() {
            passengerCount++;
            const passengerForm = document.createElement('div');
            passengerForm.className = 'passenger-form';
            passengerForm.innerHTML = `
                <div class="passenger-header">
                    <h3>Penumpang ${passengerCount}</h3>
                    <button type="button" class="btn-remove" onclick="removePassenger(this)">Hapus</button>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="passenger-name-${passengerCount}">Nama Lengkap</label>
                        <input type="text" class="form-control" id="passenger-name-${passengerCount}" name="passenger_name[]" required>
                    </div>
                    <div class="form-group">
                        <label for="passenger-id-${passengerCount}">Nomor Identitas (KTP/Paspor)</label>
                        <input type="text" class="form-control" id="passenger-id-${passengerCount}" name="passenger_id[]" required>
                    </div>
                </div>
            `;
            passengersContainer.appendChild(passengerForm);
        });
    }
    
    // Payment method selection
    const paymentMethods = document.querySelectorAll('.payment-method');
    
    paymentMethods.forEach(method => {
        method.addEventListener('click', function() {
            // Remove active class from all methods
            paymentMethods.forEach(m => m.classList.remove('active'));
            
            // Add active class to selected method
            this.classList.add('active');
            
            // Check the radio button
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;
        });
    });
    
    // Modal handling
    const modalTriggers = document.querySelectorAll('[data-toggle="modal"]');
    const modals = document.querySelectorAll('.modal');
    const closeButtons = document.querySelectorAll('.close-modal');
    
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-target');
            const targetModal = document.querySelector(targetId);
            
            if (targetModal) {
                targetModal.style.display = 'block';
                document.body.style.overflow = 'hidden';
            }
        });
    });
    
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        });
    });
    
    // Close modal when clicking outside
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
    });
    
    // Date picker validation
    const departureDateInput = document.getElementById('departure-date');
    const returnDateInput = document.getElementById('return-date');
    const tripTypeSelect = document.getElementById('trip-type');
    
    if (tripTypeSelect) {
        tripTypeSelect.addEventListener('change', function() {
            if (this.value === 'one-way') {
                returnDateInput.disabled = true;
                returnDateInput.required = false;
            } else {
                returnDateInput.disabled = false;
                returnDateInput.required = true;
            }
        });
    }
    
    if (departureDateInput && returnDateInput) {
        const today = new Date().toISOString().split('T')[0];
        departureDateInput.min = today;
        returnDateInput.min = today;
        
        departureDateInput.addEventListener('change', function() {
            returnDateInput.min = this.value;
            
            if (returnDateInput.value && returnDateInput.value < this.value) {
                returnDateInput.value = this.value;
            }
        });
    }
    
    // Auto-fill passenger data from user profile
    const autoFillBtn = document.getElementById('auto-fill-passenger');
    
    if (autoFillBtn) {
        autoFillBtn.addEventListener('click', function() {
            // This would typically fetch user data via AJAX
            // For demo purposes, we'll use static data
            const userName = document.getElementById('user-name').value;
            const userId = document.getElementById('user-id').value;
            
            if (userName && userId) {
                document.getElementById('passenger-name-1').value = userName;
                document.getElementById('passenger-id-1').value = userId;
            }
        });
    }
    
    // Flight search filters
    const filterButtons = document.querySelectorAll('.filter-btn');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Apply filter logic here
            const filterType = this.getAttribute('data-filter');
            filterFlights(filterType);
        });
    });
    
    function filterFlights(filterType) {
        const flightCards = document.querySelectorAll('.flight-card');
        
        flightCards.forEach(card => {
            // Apply filter based on filterType
            // This is a placeholder for actual filtering logic
            if (filterType === 'all') {
                card.style.display = 'block';
            } else if (filterType === 'morning') {
                // Show only morning flights
                const departureTime = card.querySelector('.time').textContent;
                const hour = parseInt(departureTime.split(':')[0]);
                card.style.display = (hour >= 6 && hour < 12) ? 'block' : 'none';
            } else if (filterType === 'afternoon') {
                // Show only afternoon flights
                const departureTime = card.querySelector('.time').textContent;
                const hour = parseInt(departureTime.split(':')[0]);
                card.style.display = (hour >= 12 && hour < 18) ? 'block' : 'none';
            } else if (filterType === 'evening') {
                // Show only evening flights
                const departureTime = card.querySelector('.time').textContent;
                const hour = parseInt(departureTime.split(':')[0]);
                card.style.display = (hour >= 18 || hour < 6) ? 'block' : 'none';
            } else if (filterType === 'price-low') {
                // Sort by price (low to high)
                // This would require more complex logic
            } else if (filterType === 'price-high') {
                // Sort by price (high to low)
                // This would require more complex logic
            }
        });
    }
    
    // Countdown timer for payment
    const paymentTimer = document.getElementById('payment-timer');
    
    if (paymentTimer) {
        let timeLeft = parseInt(paymentTimer.getAttribute('data-time'));
        
        const timerInterval = setInterval(function() {
            timeLeft--;
            
            const hours = Math.floor(timeLeft / 3600);
            const minutes = Math.floor((timeLeft % 3600) / 60);
            const seconds = timeLeft % 60;
            
            paymentTimer.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                paymentTimer.textContent = "Waktu Habis";
                // Redirect or show timeout message
                document.getElementById('payment-timeout').style.display = 'block';
                document.getElementById('payment-form').style.display = 'none';
            }
        }, 1000);
    }
});

// Function to remove passenger form
function removePassenger(button) {
    const passengerForm = button.closest('.passenger-form');
    passengerForm.remove();
    
    // Update passenger count and labels
    const passengerForms = document.querySelectorAll('.passenger-form');
    passengerForms.forEach((form, index) => {
        const header = form.querySelector('.passenger-header h3');
        header.textContent = `Penumpang ${index + 1}`;
    });
}

// Function to confirm booking
function confirmBooking(bookingId) {
    if (confirm('Apakah Anda yakin ingin mengkonfirmasi booking ini?')) {
        // Make AJAX request to confirm booking
        fetch(`/api/confirm_booking.php?id=${bookingId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success';
                alertDiv.textContent = 'Booking berhasil dikonfirmasi!';
                
                const container = document.querySelector('.container');
                container.insertBefore(alertDiv, container.firstChild);
                
                // Update status in UI
                const statusElement = document.getElementById(`status-${bookingId}`);
                statusElement.textContent = 'Confirmed';
                statusElement.className = 'booking-status status-confirmed';
                
                // Remove confirm button
                const confirmButton = document.getElementById(`confirm-${bookingId}`);
                confirmButton.remove();
                
                // Scroll to top
                window.scrollTo(0, 0);
                
                // Remove alert after 5 seconds
                setTimeout(() => {
                    alertDiv.remove();
                }, 5000);
            } else {
                // Show error message
                alert('Gagal mengkonfirmasi booking: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan. Silakan coba lagi.');
        });
    }
}

// Function to cancel booking
function cancelBooking(bookingId) {
    if (confirm('Apakah Anda yakin ingin membatalkan booking ini?')) {
        // Make AJAX request to cancel booking
        fetch(`/api/cancel_booking.php?id=${bookingId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success';
                alertDiv.textContent = 'Booking berhasil dibatalkan!';
                
                const container = document.querySelector('.container');
                container.insertBefore(alertDiv, container.firstChild);
                
                // Update status in UI
                const statusElement = document.getElementById(`status-${bookingId}`);
                statusElement.textContent = 'Cancelled';
                statusElement.className = 'booking-status status-cancelled';
                
                // Remove action buttons
                const actionsElement = document.getElementById(`actions-${bookingId}`);
                actionsElement.innerHTML = '<span class="text-muted">Tidak ada tindakan tersedia</span>';
                
                // Scroll to top
                window.scrollTo(0, 0);
                
                // Remove alert after 5 seconds
                setTimeout(() => {
                    alertDiv.remove();
                }, 5000);
            } else {
                // Show error message
                alert('Gagal membatalkan booking: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan. Silakan coba lagi.');
        });
    }
}

// Function to delete flight (admin)
function deleteFlight(flightId) {
    if (confirm('Apakah Anda yakin ingin menghapus jadwal penerbangan ini?')) {
        // Make AJAX request to delete flight
        fetch(`/admin/api/delete_flight.php?id=${flightId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success';
                alertDiv.textContent = 'Jadwal penerbangan berhasil dihapus!';
                
                const container = document.querySelector('.admin-content');
                container.insertBefore(alertDiv, container.firstChild);
                
                // Remove row from table
                const row = document.getElementById(`flight-row-${flightId}`);
                row.remove();
                
                // Scroll to top
                window.scrollTo(0, 0);
                
                // Remove alert after 5 seconds
                setTimeout(() => {
                    alertDiv.remove();
                }, 5000);
            } else {
                // Show error message
                alert('Gagal menghapus jadwal penerbangan: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan. Silakan coba lagi.');
        });
    }
}

// Function to delete airline (admin)
function deleteAirline(airlineId) {
    if (confirm('Apakah Anda yakin ingin menghapus maskapai ini?')) {
        // Make AJAX request to delete airline
        fetch(`/admin/api/delete_airline.php?id=${airlineId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success';
                alertDiv.textContent = 'Maskapai berhasil dihapus!';
                
                const container = document.querySelector('.admin-content');
                container.insertBefore(alertDiv, container.firstChild);
                
                // Remove row from table
                const row = document.getElementById(`airline-row-${airlineId}`);
                row.remove();
                
                // Scroll to top
                window.scrollTo(0, 0);
                
                // Remove alert after 5 seconds
                setTimeout(() => {
                    alertDiv.remove();
                }, 5000);
            } else {
                // Show error message
                alert('Gagal menghapus maskapai: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan. Silakan coba lagi.');
        });
    }
}