-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 27 Okt 2025 pada 04.14
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kertajati_booking`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `airlines`
--

CREATE TABLE `airlines` (
  `airline_id` int(11) NOT NULL,
  `airline_name` varchar(100) NOT NULL,
  `airline_code` char(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `airlines`
--

INSERT INTO `airlines` (`airline_id`, `airline_name`, `airline_code`) VALUES
(1, 'Garuda Indonesia', 'GA'),
(2, 'Lion Air', 'JT'),
(3, 'AirAsia', 'QZ'),
(4, 'Citilink', 'QG'),
(5, 'Batik Air', 'ID'),
(6, 'Sriwijaya Air', 'SJ'),
(7, 'Wings Air', 'IW'),
(8, 'Nam Air', 'IN'),
(9, 'Trigana Air', 'TG'),
(10, 'Xpress Air', 'XN'),
(11, 'TransNusa', '8B'),
(12, 'Super Air Jet', 'IU'),
(13, 'Pelita Air', 'IP');

-- --------------------------------------------------------

--
-- Struktur dari tabel `airports`
--

CREATE TABLE `airports` (
  `airport_code` char(3) NOT NULL,
  `airport_name` varchar(150) NOT NULL,
  `city` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `airports`
--

INSERT INTO `airports` (`airport_code`, `airport_name`, `city`) VALUES
('BDJ', 'Syamsudin Noor Airport', 'Banjarmasin'),
('BIK', 'Francisco Xavier Airport', 'Bau-Bau'),
('BKS', 'Fatmawati Soekarno Airport', 'Bengkulu'),
('BPN', 'Sultan Aji Muhammad Sulaiman Airport', 'Balikpapan'),
('BTH', 'Hang Nadim Airport', 'Batam'),
('BTJ', 'Sultan Iskandar Muda Airport', 'Banda Aceh'),
('BUU', 'Betoambari Airport', 'Bau-Bau'),
('CGK', 'Soekarno-Hatta International Airport', 'Jakarta'),
('DJJ', 'Sentani Airport', 'Jayapura'),
('DPS', 'Ngurah Rai International Airport', 'Denpasar'),
('KDI', 'Haluoleo Airport', 'Kendari'),
('KJT', 'Bandara Internasional Jawa Barat Kertajati', 'Majalengka'),
('LOP', 'Lombok International Airport', 'Lombok'),
('LUW', 'Bua Airport', 'Luwu'),
('MDN', 'Juwata Airport', 'Tarakan'),
('MJU', 'Andi Jemma Airport', 'Mamuju'),
('MKQ', 'Mopah Airport', 'Merauke'),
('PDG', 'Minangkabau International Airport', 'Padang'),
('PGK', 'Sultan Thaha Airport', 'Jambi'),
('PKU', 'Sultan Syarif Kasim II Airport', 'Pekanbaru'),
('PLM', 'Sultan Mahmud Badaruddin II Airport', 'Palembang'),
('SUB', 'Juanda International Airport', 'Surabaya'),
('TIM', 'Mopah Airport', 'Merauke'),
('TJQ', 'Raden Inten II Airport', 'Bandar Lampung'),
('TNJ', 'Raja Haji Fisabilillah Airport', 'Tanjung Pinang'),
('UPG', 'Hasanuddin International Airport', 'Makassar');

-- --------------------------------------------------------

--
-- Struktur dari tabel `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `flight_id` int(11) NOT NULL,
  `booking_code` varchar(10) NOT NULL,
  `total_passengers` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `flight_id`, `booking_code`, `total_passengers`, `total_price`, `status`, `booking_date`) VALUES
(1, 1, 4, 'ACQ2DBSS', 1, 650000.00, 'confirmed', '2025-10-27 02:46:08');

-- --------------------------------------------------------

--
-- Struktur dari tabel `flights`
--

CREATE TABLE `flights` (
  `flight_id` int(11) NOT NULL,
  `airline_id` int(11) NOT NULL,
  `flight_number` varchar(10) NOT NULL,
  `origin_code` char(3) NOT NULL,
  `destination_code` char(3) NOT NULL,
  `departure_time` datetime NOT NULL,
  `arrival_time` datetime NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `available_seats` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `flights`
--

INSERT INTO `flights` (`flight_id`, `airline_id`, `flight_number`, `origin_code`, `destination_code`, `departure_time`, `arrival_time`, `price`, `available_seats`) VALUES
(1, 1, 'GA150', 'KJT', 'CGK', '2025-10-30 08:00:00', '2025-10-31 09:30:00', 750000.00, 180),
(2, 1, 'GA152', 'KJT', 'CGK', '2025-10-30 12:00:00', '2025-10-31 13:30:00', 750000.00, 180),
(3, 1, 'GA154', 'KJT', 'CGK', '2025-10-30 16:00:00', '2025-10-31 17:30:00', 750000.00, 180),
(4, 2, 'JT610', 'KJT', 'CGK', '2025-10-30 09:30:00', '2025-10-31 11:00:00', 650000.00, 179),
(5, 2, 'JT612', 'KJT', 'CGK', '2025-10-30 14:30:00', '2025-10-31 16:00:00', 650000.00, 180),
(6, 3, 'QZ7510', 'KJT', 'CGK', '2025-10-30 07:30:00', '2025-10-31 09:00:00', 600000.00, 180),
(7, 3, 'QZ7512', 'KJT', 'CGK', '2025-10-30 13:30:00', '2025-10-31 15:00:00', 600000.00, 180),
(8, 4, 'QG950', 'KJT', 'CGK', '2025-10-30 08:30:00', '2025-10-31 10:00:00', 680000.00, 72),
(9, 4, 'QG952', 'KJT', 'CGK', '2025-10-30 15:30:00', '2025-10-31 17:00:00', 680000.00, 72),
(10, 5, 'ID6270', 'KJT', 'CGK', '2025-10-30 10:00:00', '2025-10-31 11:30:00', 720000.00, 180),
(11, 1, 'GA151', 'CGK', 'KJT', '2025-10-30 10:30:00', '2025-10-31 12:00:00', 750000.00, 180),
(12, 1, 'GA153', 'CGK', 'KJT', '2025-10-30 14:30:00', '2025-10-31 16:00:00', 750000.00, 180),
(13, 1, 'GA155', 'CGK', 'KJT', '2025-10-30 18:30:00', '2025-10-31 20:00:00', 750000.00, 180),
(14, 2, 'JT611', 'CGK', 'KJT', '2025-10-30 12:00:00', '2025-10-31 13:30:00', 650000.00, 180),
(15, 2, 'JT613', 'CGK', 'KJT', '2025-10-30 17:00:00', '2025-10-31 18:30:00', 650000.00, 180),
(16, 3, 'QZ7511', 'CGK', 'KJT', '2025-10-30 10:00:00', '2025-10-31 11:30:00', 600000.00, 180),
(17, 3, 'QZ7513', 'CGK', 'KJT', '2025-10-30 16:00:00', '2025-10-31 17:30:00', 600000.00, 180),
(18, 4, 'QG951', 'CGK', 'KJT', '2025-10-30 11:00:00', '2025-10-31 12:30:00', 680000.00, 72),
(19, 4, 'QG953', 'CGK', 'KJT', '2025-10-30 18:00:00', '2025-10-31 19:30:00', 680000.00, 72),
(20, 5, 'ID6271', 'CGK', 'KJT', '2025-10-30 13:00:00', '2025-10-31 14:30:00', 720000.00, 180),
(21, 1, 'GA250', 'KJT', 'SUB', '2025-10-30 09:00:00', '2025-10-31 10:45:00', 850000.00, 180),
(22, 2, 'JT620', 'KJT', 'SUB', '2025-10-30 08:00:00', '2025-10-31 09:45:00', 750000.00, 180),
(23, 3, 'QZ7520', 'KJT', 'SUB', '2025-10-30 14:00:00', '2025-10-31 15:45:00', 700000.00, 180),
(24, 1, 'GA251', 'SUB', 'KJT', '2025-10-30 11:45:00', '2025-10-31 13:30:00', 850000.00, 180),
(25, 2, 'JT621', 'SUB', 'KJT', '2025-10-30 10:45:00', '2025-10-31 12:30:00', 750000.00, 180),
(26, 3, 'QZ7521', 'SUB', 'KJT', '2025-10-30 16:45:00', '2025-10-31 18:30:00', 700000.00, 180),
(27, 1, 'GA350', 'KJT', 'DPS', '2025-10-30 07:00:00', '2025-10-31 10:00:00', 1200000.00, 180),
(28, 2, 'JT630', 'KJT', 'DPS', '2025-10-30 09:30:00', '2025-10-31 12:30:00', 1100000.00, 180),
(29, 3, 'QZ7530', 'KJT', 'DPS', '2025-10-30 13:00:00', '2025-10-31 16:00:00', 950000.00, 180),
(30, 1, 'GA351', 'DPS', 'KJT', '2025-10-30 11:00:00', '2025-10-31 14:00:00', 1200000.00, 180),
(31, 2, 'JT631', 'DPS', 'KJT', '2025-10-30 13:30:00', '2025-10-31 16:30:00', 1100000.00, 180),
(32, 3, 'QZ7531', 'DPS', 'KJT', '2025-10-30 17:00:00', '2025-10-31 20:00:00', 950000.00, 180),
(33, 1, 'GA450', 'KJT', 'UPG', '2025-10-30 08:00:00', '2025-10-31 11:30:00', 1500000.00, 180),
(34, 2, 'JT640', 'KJT', 'UPG', '2025-10-30 10:30:00', '2025-10-31 14:00:00', 1400000.00, 180),
(35, 1, 'GA451', 'UPG', 'KJT', '2025-10-30 12:30:00', '2025-10-31 16:00:00', 1500000.00, 180),
(36, 2, 'JT641', 'UPG', 'KJT', '2025-10-30 15:00:00', '2025-10-31 18:30:00', 1400000.00, 180);

-- --------------------------------------------------------

--
-- Struktur dari tabel `passengers`
--

CREATE TABLE `passengers` (
  `passenger_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `id_number` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `passengers`
--

INSERT INTO `passengers` (`passenger_id`, `booking_id`, `full_name`, `id_number`) VALUES
(1, 1, 'ahmad', '123456');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password_hash`, `full_name`, `phone_number`, `role`, `created_at`) VALUES
(1, 'admin', 'admin@kertajati.com', '$2y$10$1pd4EbV7RMxJMaDzmbeZeuBdkiwVxnHL8tUWmP.uI5CQ7XmkbEZpa', 'Administrator', NULL, 'admin', '2025-10-27 01:54:35'),
(2, 'user1', 'user1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ahmad Wijaya', '08123456789', 'user', '2025-10-27 01:54:35'),
(3, 'user2', 'user2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Siti Nurhaliza', '08234567890', 'user', '2025-10-27 01:54:35'),
(4, 'gisan', 'gisandwipa@gmail.com', '$2y$10$fejL39a.Vsae/RBSreftMeelkAEM.SxrnLXgwKf2ZJFI7pGrc1jyS', 'ghaisan', 'ghaisan', 'user', '2025-10-27 02:00:29');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `airlines`
--
ALTER TABLE `airlines`
  ADD PRIMARY KEY (`airline_id`),
  ADD UNIQUE KEY `airline_code` (`airline_code`);

--
-- Indeks untuk tabel `airports`
--
ALTER TABLE `airports`
  ADD PRIMARY KEY (`airport_code`);

--
-- Indeks untuk tabel `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD UNIQUE KEY `booking_code` (`booking_code`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `flight_id` (`flight_id`);

--
-- Indeks untuk tabel `flights`
--
ALTER TABLE `flights`
  ADD PRIMARY KEY (`flight_id`),
  ADD KEY `airline_id` (`airline_id`),
  ADD KEY `origin_code` (`origin_code`),
  ADD KEY `destination_code` (`destination_code`);

--
-- Indeks untuk tabel `passengers`
--
ALTER TABLE `passengers`
  ADD PRIMARY KEY (`passenger_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `airlines`
--
ALTER TABLE `airlines`
  MODIFY `airline_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `flights`
--
ALTER TABLE `flights`
  MODIFY `flight_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT untuk tabel `passengers`
--
ALTER TABLE `passengers`
  MODIFY `passenger_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`flight_id`) REFERENCES `flights` (`flight_id`);

--
-- Ketidakleluasaan untuk tabel `flights`
--
ALTER TABLE `flights`
  ADD CONSTRAINT `flights_ibfk_1` FOREIGN KEY (`airline_id`) REFERENCES `airlines` (`airline_id`),
  ADD CONSTRAINT `flights_ibfk_2` FOREIGN KEY (`origin_code`) REFERENCES `airports` (`airport_code`),
  ADD CONSTRAINT `flights_ibfk_3` FOREIGN KEY (`destination_code`) REFERENCES `airports` (`airport_code`);

--
-- Ketidakleluasaan untuk tabel `passengers`
--
ALTER TABLE `passengers`
  ADD CONSTRAINT `passengers_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
