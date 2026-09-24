-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 08, 2026 at 07:16 PM
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
-- Database: `arayaofficial`
--

-- --------------------------------------------------------

--
-- Table structure for table `customer_vouchers`
--

CREATE TABLE `customer_vouchers` (
  `id` int(11) NOT NULL,
  `nama_pelanggan` varchar(150) NOT NULL,
  `nomor_whatsapp` varchar(20) NOT NULL,
  `promo_id` int(11) NOT NULL,
  `kode_voucher` varchar(30) NOT NULL,
  `status_pakai` enum('Belum Digunakan','Sudah Digunakan') DEFAULT 'Belum Digunakan',
  `tanggal_klaim` timestamp NOT NULL DEFAULT current_timestamp(),
  `tanggal_pakai` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `daftar_harga`
--

CREATE TABLE `daftar_harga` (
  `id_harga` int(11) NOT NULL,
  `p_paket` varchar(50) NOT NULL,
  `durasi` varchar(50) NOT NULL,
  `daf_harga` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `daftar_harga`
--

INSERT INTO `daftar_harga` (`id_harga`, `p_paket`, `durasi`, `daf_harga`) VALUES
(1, 'Reguler', '1 Jam', '12000'),
(2, 'Voucher', '3 Jam', '25000'),
(3, 'Voucher', '5 jam', '40000'),
(4, 'Voucher', '10 jam', '75000'),
(5, 'Vip', '1 Jam', '25000'),
(6, 'Vip', '2 Jam', '50000'),
(7, 'Vip', '3 Jam', '75000'),
(8, 'Vip', '4 Jam', '85000'),
(9, 'Vip', '5 jam', '100000'),
(10, 'RENTAL', '12 JAM', '90000'),
(11, 'RENTAL', '24 JAM', '130000'),
(13, 'RENTAL', '48 JAM', '200000'),
(14, 'TV', '12 / 24 JAM', '20000'),
(15, 'Paket', '3 Jam', '30000'),
(16, 'Premiere 1', '1 Jam', '45000'),
(17, 'Premiere 1', '2 Jam', '80000'),
(18, 'Premiere 2', '1 Jam', '35000'),
(19, 'Premiere 2', '2 Jam', '60000');

-- --------------------------------------------------------

--
-- Table structure for table `feedback_pelanggan`
--

CREATE TABLE `feedback_pelanggan` (
  `id` int(11) NOT NULL,
  `nama_pelanggan` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `rating` int(11) NOT NULL,
  `pesan` text NOT NULL,
  `tanggal_dibuat` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `feedback_pelanggan`
--

INSERT INTO `feedback_pelanggan` (`id`, `nama_pelanggan`, `email`, `rating`, `pesan`, `tanggal_dibuat`) VALUES
(1, 'rommy gunawan', 'rommygunawan124@gmail.com', 5, 'tempatnya keren', '2026-09-02 17:49:59');

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `id_karyawan` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `cabang` varchar(20) NOT NULL,
  `tanggal` varchar(50) NOT NULL,
  `shift` varchar(30) NOT NULL,
  `omset` varchar(100) NOT NULL,
  `operasional` varchar(200) NOT NULL,
  `total_pengeluaran` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`id_karyawan`, `id_user`, `cabang`, `tanggal`, `shift`, `omset`, `operasional`, `total_pengeluaran`) VALUES
(97, 20, 'Beruntung', '2026-09-01', '2', '600000', 'listrik', '400000'),
(98, 20, 'Beruntung', '2026-09-02', '2', '450000', 'Jaga malam', '150000'),
(99, 17, 'Beruntung', '2026-09-09', '1', '500000', '', '0');

-- --------------------------------------------------------

--
-- Table structure for table `kat_ruangan`
--

CREATE TABLE `kat_ruangan` (
  `id_ruangan` int(11) NOT NULL,
  `nama_ruangan` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `kat_ruangan`
--

INSERT INTO `kat_ruangan` (`id_ruangan`, `nama_ruangan`) VALUES
(1, 'REGULER'),
(2, 'VIP');

-- --------------------------------------------------------

--
-- Table structure for table `kat_unit`
--

CREATE TABLE `kat_unit` (
  `id_unit` int(11) NOT NULL,
  `nama_unit` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `kat_unit`
--

INSERT INTO `kat_unit` (`id_unit`, `nama_unit`) VALUES
(1, 'PS4');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `nama_pembayaran` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `nama_pembayaran`) VALUES
(1, 'Cash'),
(2, 'Qris');

-- --------------------------------------------------------

--
-- Table structure for table `pengeluaran`
--

CREATE TABLE `pengeluaran` (
  `id_pengeluaran` int(11) NOT NULL,
  `tanggal` varchar(200) NOT NULL,
  `cabang` varchar(100) NOT NULL,
  `nama_staff_ruko` varchar(200) NOT NULL,
  `pendapatan` varchar(200) NOT NULL,
  `potongan` varchar(100) NOT NULL,
  `total_bersih` varchar(100) NOT NULL,
  `level` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengeluaran`
--

INSERT INTO `pengeluaran` (`id_pengeluaran`, `tanggal`, `cabang`, `nama_staff_ruko`, `pendapatan`, `potongan`, `total_bersih`, `level`) VALUES
(22, '2026-09-01', 'beruntung', 'WIFI', '', '', 'Rp. 500.000', 'wifi'),
(23, '2026-09-01', 'gambut', 'STICK', '', '', 'Rp. 500.000', 'service'),
(24, '2026-09-01', 'gambut', 'NETFLIX', '', '', 'Rp. 186.000', 'netflix'),
(25, '2026-09-01', 'beruntung', 'NETFLIX', '', '', 'Rp. 186.000', 'netflix'),
(26, '2026-09-01', 'beruntung', 'WIFI', '', '', 'Rp. 400.000', 'wifi'),
(27, '2026-09-01', 'beruntung', 'SERVICE STICK', '', '', 'Rp. 300.000', 'service'),
(28, '2026-09-01', 'gambut', 'PDAM', '', '', 'Rp. 500.000', 'pdam'),
(29, '2026-09-01', 'beruntung', 'PDAM', '', '', 'Rp. 200.000', 'pdam'),
(30, '2026-09-01', 'beruntung', 'Rommy Gunawan', 'Rp. 2.000.000', 'Rp. 200.000', 'Rp. 1.800.000', 'staff'),
(31, '2026-09-01', 'beruntung', 'BERUNTUNG JAYA', '', '', 'Rp. 7.000.000', 'sewa');

-- --------------------------------------------------------

--
-- Table structure for table `promos`
--

CREATE TABLE `promos` (
  `id` int(11) NOT NULL,
  `nama_promo` varchar(100) NOT NULL,
  `kode_prefix` varchar(10) NOT NULL,
  `kuota` int(11) NOT NULL,
  `potongan` varchar(50) NOT NULL,
  `status` enum('Aktif','Nonaktif') DEFAULT 'Aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rental`
--

CREATE TABLE `rental` (
  `id_rental` int(11) NOT NULL,
  `nama_perental` varchar(50) NOT NULL,
  `durasi_sewa` varchar(100) NOT NULL,
  `alamat_lengkap` text NOT NULL,
  `jaminan` varchar(50) NOT NULL,
  `sharelok` varchar(100) NOT NULL,
  `wa` varchar(13) NOT NULL,
  `catatan` text NOT NULL,
  `status` varchar(50) NOT NULL,
  `img` varchar(100) NOT NULL,
  `id_kurir` int(11) DEFAULT NULL,
  `tgl_selesai` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservasi`
--

CREATE TABLE `reservasi` (
  `id_reservasi` int(11) NOT NULL,
  `cabang` varchar(50) NOT NULL,
  `nama_reservasi` varchar(50) NOT NULL,
  `tgl_bermain` date NOT NULL,
  `ruang` varchar(20) NOT NULL,
  `jml_orang` varchar(10) NOT NULL,
  `tambahan` varchar(20) NOT NULL,
  `w_kedatangan` varchar(20) NOT NULL,
  `durasi` varchar(20) NOT NULL,
  `whatsapp` varchar(13) NOT NULL,
  `dp` varchar(100) NOT NULL,
  `catatan` text NOT NULL,
  `status` varchar(100) NOT NULL,
  `kode_voucher` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `reservasi`
--

INSERT INTO `reservasi` (`id_reservasi`, `cabang`, `nama_reservasi`, `tgl_bermain`, `ruang`, `jml_orang`, `tambahan`, `w_kedatangan`, `durasi`, `whatsapp`, `dp`, `catatan`, `status`, `kode_voucher`) VALUES
(11, 'Gambut', 'rrr', '2026-08-13', 'Premiere 1', '5', '1 mic', '16:31 WITA', '2 Jam', '439', '', '-', 'Pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `target`
--

CREATE TABLE `target` (
  `id_target` int(11) NOT NULL,
  `ncabang` varchar(100) NOT NULL,
  `shift` varchar(20) NOT NULL,
  `target` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `target`
--

INSERT INTO `target` (`id_target`, `ncabang`, `shift`, `target`) VALUES
(1, 'Beruntung', '1', '300000'),
(2, 'Beruntung', '2', '450000'),
(3, 'Beruntung', '3', '250000'),
(4, 'Gambut', '1', '300000'),
(5, 'Gambut', '2', '800000'),
(6, 'Gambut', '3', '900000');

-- --------------------------------------------------------

--
-- Table structure for table `timer_billing`
--

CREATE TABLE `timer_billing` (
  `id_timer` varchar(50) NOT NULL,
  `cabang` varchar(50) DEFAULT NULL,
  `no_meja` varchar(50) DEFAULT NULL,
  `judul_meja` varchar(100) DEFAULT NULL,
  `paket` int(11) DEFAULT NULL,
  `time_left` int(11) DEFAULT NULL,
  `end_time` varchar(50) DEFAULT NULL,
  `is_running` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `timer_history`
--

CREATE TABLE `timer_history` (
  `id` int(11) NOT NULL,
  `cabang` varchar(50) DEFAULT NULL,
  `no_meja` varchar(50) DEFAULT NULL,
  `judul_meja` varchar(100) DEFAULT NULL,
  `end_time` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `nama_lengkap` varchar(50) NOT NULL,
  `jabatan` varchar(100) NOT NULL,
  `no_wa` varchar(13) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(12) NOT NULL,
  `images` varchar(100) NOT NULL,
  `level` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `nama_lengkap`, `jabatan`, `no_wa`, `username`, `password`, `images`, `level`) VALUES
(16, 'Hadi Subarjan', 'owner', '08134152535', 'hadisubarjan@owner.com', '@owneraraya', '', 'admin'),
(17, 'Rommy Gunawan', 'supervisor', '085750755502', 'rommygn@spv.com', '@spvaraya', '', 'karyawan'),
(18, 'Muhammad Ardi', 'operator', '089664901559', 'ardi@op.com', '@opardi', '', 'karyawan'),
(19, 'Mahendra', 'operator', '081350816897', 'mahendra@op.com', '@opmahen', '', 'karyawan'),
(20, 'Muhammad Rasyid Ridho', 'operator', '085133804755', 'edo@op.com', '@edoop', '', 'karyawan'),
(21, 'Maulana', 'operator', '081345972385', 'maulana@op.com', '@opmaulana', '', 'karyawan'),
(22, 'Muhammad Ramadhan', 'operator', '087869269749', 'ramadhan@op.com', '@opramadhan', '', 'karyawan'),
(23, 'Annas', 'operator', '083155854993', 'annas@op.com', '@opannas', '', 'karyawan'),
(24, 'Muhammad Ihsan', 'operator', '081258812521', 'ihsan@op.com', '@opihsan', '', 'karyawan'),
(25, 'Admin Sewa Playstation', 'operator', '085750755502', 'sewapsaraya@rental.com', '@rentalaraya', '', 'rental'),
(26, 'Araya Gamestation 1', 'operator', ' 085147520182', 'araya1@rsv.com', '@araya1rsv', '', 'reservasi'),
(27, 'Araya Gamestation 2', 'operator', '085750755502', 'araya2@rsv.com', '@araya2rsv', '', 'reservasi');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customer_vouchers`
--
ALTER TABLE `customer_vouchers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_voucher` (`kode_voucher`),
  ADD KEY `promo_id` (`promo_id`);

--
-- Indexes for table `daftar_harga`
--
ALTER TABLE `daftar_harga`
  ADD PRIMARY KEY (`id_harga`);

--
-- Indexes for table `feedback_pelanggan`
--
ALTER TABLE `feedback_pelanggan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id_karyawan`);

--
-- Indexes for table `kat_ruangan`
--
ALTER TABLE `kat_ruangan`
  ADD PRIMARY KEY (`id_ruangan`);

--
-- Indexes for table `kat_unit`
--
ALTER TABLE `kat_unit`
  ADD PRIMARY KEY (`id_unit`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`);

--
-- Indexes for table `pengeluaran`
--
ALTER TABLE `pengeluaran`
  ADD PRIMARY KEY (`id_pengeluaran`);

--
-- Indexes for table `promos`
--
ALTER TABLE `promos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_prefix` (`kode_prefix`);

--
-- Indexes for table `rental`
--
ALTER TABLE `rental`
  ADD PRIMARY KEY (`id_rental`);

--
-- Indexes for table `reservasi`
--
ALTER TABLE `reservasi`
  ADD PRIMARY KEY (`id_reservasi`),
  ADD KEY `tgl_bermain` (`tgl_bermain`),
  ADD KEY `status` (`status`),
  ADD KEY `cabang` (`cabang`);

--
-- Indexes for table `target`
--
ALTER TABLE `target`
  ADD PRIMARY KEY (`id_target`);

--
-- Indexes for table `timer_billing`
--
ALTER TABLE `timer_billing`
  ADD PRIMARY KEY (`id_timer`);

--
-- Indexes for table `timer_history`
--
ALTER TABLE `timer_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customer_vouchers`
--
ALTER TABLE `customer_vouchers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `daftar_harga`
--
ALTER TABLE `daftar_harga`
  MODIFY `id_harga` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `feedback_pelanggan`
--
ALTER TABLE `feedback_pelanggan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id_karyawan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `kat_ruangan`
--
ALTER TABLE `kat_ruangan`
  MODIFY `id_ruangan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `kat_unit`
--
ALTER TABLE `kat_unit`
  MODIFY `id_unit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pengeluaran`
--
ALTER TABLE `pengeluaran`
  MODIFY `id_pengeluaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `promos`
--
ALTER TABLE `promos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rental`
--
ALTER TABLE `rental`
  MODIFY `id_rental` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `reservasi`
--
ALTER TABLE `reservasi`
  MODIFY `id_reservasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `target`
--
ALTER TABLE `target`
  MODIFY `id_target` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `timer_history`
--
ALTER TABLE `timer_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customer_vouchers`
--
ALTER TABLE `customer_vouchers`
  ADD CONSTRAINT `customer_vouchers_ibfk_1` FOREIGN KEY (`promo_id`) REFERENCES `promos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
