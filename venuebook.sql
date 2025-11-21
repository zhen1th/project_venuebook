-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 21, 2025 at 09:48 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `venuebook`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `venue_id` int(11) DEFAULT NULL,
  `tanggal_booking` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `total_harga` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `metode_pembayaran` varchar(50) DEFAULT 'transfer',
  `status` enum('pending','success','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `password`, `role`, `created_at`) VALUES
(4, 'bagas pratama', 'bagas', 'bagas31', 'user', '2025-11-18 13:00:14'),
(5, 'tofu', 'admin', 'admin', 'admin', '2025-11-18 13:03:20');

-- --------------------------------------------------------

--
-- Table structure for table `venues`
--

CREATE TABLE `venues` (
  `id` int(11) NOT NULL,
  `nama_venue` varchar(100) NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `harga_per_jam` decimal(10,2) NOT NULL,
  `fasilitas` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `status` enum('available','unavailable') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `venues`
--

INSERT INTO `venues` (`id`, `nama_venue`, `kategori`, `deskripsi`, `alamat`, `harga_per_jam`, `fasilitas`, `gambar`, `status`, `created_at`) VALUES
(1, 'stadion gbk', 'Sepakbola', 'luas', 'jakarta', 1500000.00, 'bench', '1763478575_gbk.webp', 'available', '2025-11-18 15:09:35'),
(2, 'stadion old trafford', 'Sepakbola', 'full merah', 'manchester', 23000000.00, 'museum', '1763481389_old-trafford.jpg', 'available', '2025-11-18 15:56:29'),
(3, 'Stadion Bernabéu', 'Sepakbola', 'Ukuran lapangan: 105 m × 68 m', 'Av. de Concha Espina, 1, Chamartín, 28036 Madrid, Spanyol', 32500000.00, 'Stadion Santiago Bernabéu memiliki fasilitas modern seperti atap retractable (bisa buka-tutup), layar 360 derajat, dan sistem lapangan yang bisa ditarik keluar untuk acara non-sepak bola. Fasilitas lainnya termasuk museum yang menyimpan trofi dan memorabilia klub, area VIP, restoran, skywalk, dan toko resmi. ', '1763560410_santiago-bernabeu-2-68ebcec45e2d92df6c3ac004c2c6a142-1f29880b80b795c2efb67a64fe54628b.avif', 'available', '2025-11-19 13:53:30'),
(4, 'Lava Minisoccer', 'Mini Soccer', 'Menyediakan pelayanan member bisa dimanfaatkan bila sering bermain bola di lapangan mini soccer.', 'Jl. Berbah – Prambanan, Jragung, Jogotirto, Kec. Berbah, Kabupaten Sleman, Daerah Istimewa Yogyakarta 55573', 800000.00, 'Tidak jauh berbeda dengan toko alat olahraga yang menyediakan berbagai perlengkapan olahraga. Di Lava Minisoccer pun sama menyediakan berbagai kebutuhan peralatan bermain bola yang lengkap. Jadi, tidak perlu risau bila belum memiliki sepatu, jersey, atau yang lainnya. Karena bisa langsung sewa sesuai dengan perlengkapan yang dibutuhkan.', '1763561247_Lava-Minisoccer.webp', 'available', '2025-11-19 14:07:27'),
(5, 'FC Mini Soccer', 'Mini Soccer', 'Baru pertama kali main bola di lapangan mini soccer dan ingin mendapatkan pengalaman terbaik? FC Mini Soccer bisa menjadi pilihan yang tepat bagi Sedulur Yogyaku. Karena di sini, semua fasilitas tersedia mulai dari musholla, toilet, tempat tunggu, kantin, dan masih banyak lagi.', 'Jl. Ringroad Barat Jl. Kronggohan, Area Sawah, Trihanggo, Kec. Gamping, Kabupaten Sleman, Daerah Istimewa Yogyakarta 55291', 650000.00, 'Itulah kenapa FC Mini Soccer juga menjadi salah satu lapangan mini soccer Jogja terbaik. Lokasinya pun cukup strategis dan mudah dijangkau. Karena berada di samping dari jalan utama Ringroad Utara Jogja. Bisa dimasukan sebagai list lapangan mini soccer yang wajib dicoba.', '1763561341_FC-Mini-Soccer.webp', 'available', '2025-11-19 14:09:01'),
(6, '3 Putra Badminton Sport Center', 'Bulu Tangkis', '3 Putra Badminton Sport Center merupakan lapangan badminton yang berlokasi di Limo, Depok. Tersedia 4 lapangan Badminton Indoor ukuran standar BWF dengan material Karpet Vinyls.', 'Jl Tiga Putra RT 03 RW 05, Meruyung Kel Grogol, Kec Limo - Depok 16515 (Sebrang Pacuan Kuda Arthayasa)', 65000.00, 'Jual Minuman\r\nMusholla \r\nParkir Mobil \r\nParkir Motor \r\nRuang Ganti \r\nShower \r\nToilet \r\nWi-fi \r\nToko Olahraga ', '1763659252_170987582950787.image_cropper_1709875801634_large.jpg', 'available', '2025-11-20 17:20:52'),
(7, 'The Cage 3 X 3', 'Basket', 'Lapangan basket 3 x 3', 'jl. kapten sumarsono no.174, Medan', 50000.00, 'Jual Makanan Ringan\r\nJual Minuman\r\nParkir Mobil \r\nParkir Motor\r\nRuang Ganti\r\nToilet\r\nTribun Penonton\r\nWi-fi\r\nMusic & Kipas Angin', '1763680676_175356242597344.image_cropper_1753562125688.jpg_large.jpeg', 'available', '2025-11-20 23:17:56'),
(8, ' Victory Arena Banyumanik', 'Basket', 'Perhatian !!!\r\n1. Mohon datang tepat waktu, apabila terlambat, waktu akhir sewa tetap sesuai jadwal.\r\n2. Karena GOR berada dikawasan pemukiman warga, mohon menjaga ketertiban dan dilarang membuat keramaian/kegaduhan, membawa loud speaker atau semacamnya.\r\n3. Tidak diperbolehkan mengadakan event tanpa memberitahu management lapangan terlebih dahulu\r\n# Untuk penyewa pukul 20.00 s/d 22.00 sesi akan berakhir pukul 21.45 . jadi untuk durasi bermain hanya 1 jam 45 menit.', 'jl. karang rejo V no 25 C Banyumanik', 50000.00, 'Parkir Mobil\r\nParkir Motor\r\nToilet \r\nTribun Penonton\r\nShower\r\nJual Minuman', '1763681125_169771838799501.image_cropper_1697718251037_large.jpg', 'available', '2025-11-20 23:25:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `venue_id` (`venue_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `venues`
--
ALTER TABLE `venues`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `venues`
--
ALTER TABLE `venues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
