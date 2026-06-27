-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 22, 2025 at 04:57 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `crm`
--

-- --------------------------------------------------------

--
-- Table structure for table `aduan`
--

CREATE TABLE `aduan` (
  `id_aduan` int(11) NOT NULL,
  `id_pelanggan` int(11) DEFAULT NULL,
  `tajuk` varchar(100) DEFAULT NULL,
  `kandungan` text DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `tarikh` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `aduan`:
--   `id_pelanggan`
--       `pelanggan` -> `id_pelanggan`
--

-- --------------------------------------------------------

--
-- Table structure for table `forum_komen`
--

CREATE TABLE `forum_komen` (
  `id` int(11) NOT NULL,
  `id_post` int(11) DEFAULT NULL,
  `id_staff` int(11) DEFAULT NULL,
  `komen` text DEFAULT NULL,
  `masa` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `forum_komen`:
--   `id_post`
--       `forum_post` -> `id_post`
--   `id_staff`
--       `staff` -> `id_staff`
--

-- --------------------------------------------------------

--
-- Table structure for table `forum_post`
--

CREATE TABLE `forum_post` (
  `id_post` int(11) NOT NULL,
  `id_staff` int(11) DEFAULT NULL,
  `tajuk` varchar(150) DEFAULT NULL,
  `kandungan` text DEFAULT NULL,
  `tarikh` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `forum_post`:
--   `id_staff`
--       `staff` -> `id_staff`
--

-- --------------------------------------------------------

--
-- Table structure for table `forum_tag`
--

CREATE TABLE `forum_tag` (
  `id` int(11) NOT NULL,
  `id_post` int(11) DEFAULT NULL,
  `tag` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `forum_tag`:
--   `id_post`
--       `forum_post` -> `id_post`
--

-- --------------------------------------------------------

--
-- Table structure for table `interaksi`
--

CREATE TABLE `interaksi` (
  `id_interaksi` int(11) NOT NULL,
  `id_pelanggan` int(11) DEFAULT NULL,
  `jenis_interaksi` varchar(100) DEFAULT NULL,
  `butiran` text DEFAULT NULL,
  `masa` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `interaksi`:
--   `id_pelanggan`
--       `pelanggan` -> `id_pelanggan`
--

-- --------------------------------------------------------

--
-- Table structure for table `jualan`
--

CREATE TABLE `jualan` (
  `id_jualan` int(11) NOT NULL,
  `id_pelanggan` int(11) DEFAULT NULL,
  `id_produk` int(11) DEFAULT NULL,
  `kuantiti` int(11) DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL,
  `jumlah` decimal(10,2) DEFAULT NULL,
  `tarikh_jualan` date DEFAULT NULL,
  `status` enum('completed','pending','cancelled','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `jualan`:
--   `id_pelanggan`
--       `pelanggan` -> `id_pelanggan`
--   `id_produk`
--       `produk` -> `id_produk`
--

-- --------------------------------------------------------

--
-- Table structure for table `log_aktiviti`
--

CREATE TABLE `log_aktiviti` (
  `id` int(11) NOT NULL,
  `id_staff` int(11) DEFAULT NULL,
  `role` varchar(50) NOT NULL,
  `tindakan` text DEFAULT NULL,
  `masa` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `log_aktiviti`:
--   `id_staff`
--       `staff` -> `id_staff`
--

-- --------------------------------------------------------

--
-- Table structure for table `log_interaksi`
--

CREATE TABLE `log_interaksi` (
  `id` int(11) NOT NULL,
  `id_pelanggan` int(11) DEFAULT NULL,
  `id_interaksi` int(11) NOT NULL,
  `nota` text DEFAULT NULL,
  `masa` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `log_interaksi`:
--   `id_pelanggan`
--       `pelanggan` -> `id_pelanggan`
--   `id_interaksi`
--       `interaksi` -> `id_interaksi`
--

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_telefon` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `tarikh_daftar` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `pelanggan`:
--

-- --------------------------------------------------------

--
-- Table structure for table `pipeline`
--

CREATE TABLE `pipeline` (
  `id_pipeline` int(11) NOT NULL,
  `id_pelanggan` int(11) DEFAULT NULL,
  `id_produk` int(11) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `no_telefon` varchar(20) NOT NULL,
  `status_semasa` enum('Baru Daftar','Hubungi Semula','Dalam Perbincangan','Tunggu Pembayaran','Selesai','Batal') DEFAULT NULL,
  `catatan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `pipeline`:
--   `id_pelanggan`
--       `pelanggan` -> `id_pelanggan`
--   `id_produk`
--       `produk` -> `id_produk`
--

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id_produk` int(11) NOT NULL,
  `nama_produk` varchar(100) DEFAULT NULL,
  `harga_jualan` decimal(10,2) DEFAULT NULL,
  `harga_modal` decimal(10,2) NOT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `stok` int(11) DEFAULT NULL,
  `status` enum('aktif','tidak aktif','','') NOT NULL,
  `tarikh_masuk` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `produk`:
--

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id_staff` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `jawatan` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `status` enum('aktif','tidak aktif') NOT NULL DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `staff`:
--

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aduan`
--
ALTER TABLE `aduan`
  ADD PRIMARY KEY (`id_aduan`),
  ADD KEY `pelanggan_id` (`id_pelanggan`);

--
-- Indexes for table `forum_komen`
--
ALTER TABLE `forum_komen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`id_post`),
  ADD KEY `staff_id` (`id_staff`);

--
-- Indexes for table `forum_post`
--
ALTER TABLE `forum_post`
  ADD PRIMARY KEY (`id_post`),
  ADD KEY `staff_id` (`id_staff`);

--
-- Indexes for table `forum_tag`
--
ALTER TABLE `forum_tag`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`id_post`);

--
-- Indexes for table `interaksi`
--
ALTER TABLE `interaksi`
  ADD PRIMARY KEY (`id_interaksi`),
  ADD KEY `pelanggan_id` (`id_pelanggan`);

--
-- Indexes for table `jualan`
--
ALTER TABLE `jualan`
  ADD PRIMARY KEY (`id_jualan`),
  ADD KEY `pelanggan_id` (`id_pelanggan`),
  ADD KEY `produk_id` (`id_produk`);

--
-- Indexes for table `log_aktiviti`
--
ALTER TABLE `log_aktiviti`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`id_staff`);

--
-- Indexes for table `log_interaksi`
--
ALTER TABLE `log_interaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pelanggan_id` (`id_pelanggan`),
  ADD KEY `log_interaksi_ibfk_2` (`id_interaksi`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`);

--
-- Indexes for table `pipeline`
--
ALTER TABLE `pipeline`
  ADD PRIMARY KEY (`id_pipeline`),
  ADD KEY `pelanggan_id` (`id_pelanggan`),
  ADD KEY `pipeline_ibfk_2` (`id_produk`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id_staff`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aduan`
--
ALTER TABLE `aduan`
  MODIFY `id_aduan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `forum_komen`
--
ALTER TABLE `forum_komen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `forum_post`
--
ALTER TABLE `forum_post`
  MODIFY `id_post` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `forum_tag`
--
ALTER TABLE `forum_tag`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `interaksi`
--
ALTER TABLE `interaksi`
  MODIFY `id_interaksi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jualan`
--
ALTER TABLE `jualan`
  MODIFY `id_jualan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `log_aktiviti`
--
ALTER TABLE `log_aktiviti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `log_interaksi`
--
ALTER TABLE `log_interaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pipeline`
--
ALTER TABLE `pipeline`
  MODIFY `id_pipeline` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id_staff` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `aduan`
--
ALTER TABLE `aduan`
  ADD CONSTRAINT `aduan_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE;

--
-- Constraints for table `forum_komen`
--
ALTER TABLE `forum_komen`
  ADD CONSTRAINT `forum_komen_ibfk_1` FOREIGN KEY (`id_post`) REFERENCES `forum_post` (`id_post`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_komen_ibfk_2` FOREIGN KEY (`id_staff`) REFERENCES `staff` (`id_staff`) ON DELETE CASCADE;

--
-- Constraints for table `forum_post`
--
ALTER TABLE `forum_post`
  ADD CONSTRAINT `forum_post_ibfk_1` FOREIGN KEY (`id_staff`) REFERENCES `staff` (`id_staff`) ON DELETE CASCADE;

--
-- Constraints for table `forum_tag`
--
ALTER TABLE `forum_tag`
  ADD CONSTRAINT `forum_tag_ibfk_1` FOREIGN KEY (`id_post`) REFERENCES `forum_post` (`id_post`) ON DELETE CASCADE;

--
-- Constraints for table `interaksi`
--
ALTER TABLE `interaksi`
  ADD CONSTRAINT `interaksi_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE;

--
-- Constraints for table `jualan`
--
ALTER TABLE `jualan`
  ADD CONSTRAINT `jualan_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE,
  ADD CONSTRAINT `jualan_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE;

--
-- Constraints for table `log_aktiviti`
--
ALTER TABLE `log_aktiviti`
  ADD CONSTRAINT `log_aktiviti_ibfk_1` FOREIGN KEY (`id_staff`) REFERENCES `staff` (`id_staff`) ON DELETE CASCADE;

--
-- Constraints for table `log_interaksi`
--
ALTER TABLE `log_interaksi`
  ADD CONSTRAINT `log_interaksi_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE,
  ADD CONSTRAINT `log_interaksi_ibfk_2` FOREIGN KEY (`id_interaksi`) REFERENCES `interaksi` (`id_interaksi`) ON DELETE CASCADE;

--
-- Constraints for table `pipeline`
--
ALTER TABLE `pipeline`
  ADD CONSTRAINT `pipeline_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE SET NULL,
  ADD CONSTRAINT `pipeline_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
