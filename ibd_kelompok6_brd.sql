-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 01, 2025 at 05:33 AM
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
-- Database: `ibd_kelompok6_brd`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `bank_detail` (IN `komunitas_id` INT, IN `periode` DATE)   BEGIN
  SELECT * FROM 4_bank
  WHERE DATE(tgl_transaksi) = periode;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `cek_selisih_kas` (IN `komunitas_id` INT, IN `tanggal` DATE)   BEGIN
  SELECT 
    (SELECT COALESCE(SUM(nominal),0) 
     FROM 3_kas_harian 
     WHERE DATE(tgl_kas_harian) = tanggal) +
    (SELECT COALESCE(SUM(nominal_penerimaan),0) - COALESCE(SUM(nominal_pengeluaran),0) 
     FROM 4_bank 
     WHERE DATE(tgl_transaksi) = tanggal) AS catatan,
    (SELECT COALESCE(SUM(nominal_pemasukan),0) - COALESCE(SUM(nominal_pengeluaran),0) 
     FROM 6_lu_komunitas WHERE id_anggaran = komunitas_id) AS opname,
    ((SELECT COALESCE(SUM(nominal_pemasukan),0) - COALESCE(SUM(nominal_pengeluaran),0) 
      FROM 6_lu_komunitas WHERE id_anggaran = komunitas_id) -
     ((SELECT COALESCE(SUM(nominal),0) 
       FROM 3_kas_harian WHERE DATE(tgl_kas_harian) = tanggal) +
      (SELECT COALESCE(SUM(nominal_penerimaan),0) - COALESCE(SUM(nominal_pengeluaran),0) 
       FROM 4_bank WHERE DATE(tgl_transaksi) = tanggal))) AS selisih;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `kas_harian_detail` (IN `komunitas_id` INT, IN `tanggal` DATE)   BEGIN
  SELECT * FROM 3_kas_harian
  WHERE DATE(tgl_kas_harian) = tanggal;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `laporan_keuangan_komunitas` (IN `komunitas_id` INT, IN `periode` DATE)   BEGIN
  SELECT id_pos,
         SUM(nominal_pemasukan) AS total_penerimaan,
         SUM(nominal_pengeluaran) AS total_pengeluaran,
         SUM(nominal_pemasukan) - SUM(nominal_pengeluaran) AS saldo
  FROM 6_lu_komunitas
  WHERE id_anggaran = komunitas_id
    AND tgl_transaksi <= periode
  GROUP BY id_pos;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `monitoring_pos` (IN `komunitas_id` INT, IN `tahun` INT)   BEGIN
  SELECT id_pos,
         SUM(nominal_pemasukan) AS realisasi_penerimaan,
         SUM(nominal_pengeluaran) AS realisasi_pengeluaran
  FROM 6_lu_komunitas
  WHERE id_anggaran = komunitas_id
    AND YEAR(tgl_transaksi) = tahun
  GROUP BY id_pos;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `rekap_bulanan` (IN `komunitas_id` INT, IN `bulan` INT, IN `tahun` INT)   BEGIN
  SELECT id_pos,
         SUM(nominal_pemasukan) AS total_penerimaan,
         SUM(nominal_pengeluaran) AS total_pengeluaran
  FROM 6_lu_komunitas
  WHERE id_anggaran = komunitas_id
    AND MONTH(tgl_transaksi) = bulan
    AND YEAR(tgl_transaksi) = tahun
  GROUP BY id_pos;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `rekap_tahunan` (IN `komunitas_id` INT, IN `tahun` INT)   BEGIN
  SELECT id_pos,
         SUM(nominal_pemasukan) AS total_penerimaan,
         SUM(nominal_pengeluaran) AS total_pengeluaran
  FROM 6_lu_komunitas
  WHERE id_anggaran = komunitas_id
    AND YEAR(tgl_transaksi) = tahun
  GROUP BY id_pos;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `saldo_komunitas` (IN `komunitas_id` INT, IN `periode` DATE)   BEGIN
  SELECT SUM(nominal_pemasukan) - SUM(nominal_pengeluaran) AS saldo
  FROM 6_lu_komunitas
  WHERE id_anggaran = komunitas_id
    AND tgl_transaksi <= periode;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `1_data`
--

CREATE TABLE `1_data` (
  `id_anggaran` int(11) NOT NULL,
  `nama_pemimpinlokal` varchar(100) NOT NULL,
  `nama_bendaharakomunitas` varchar(100) NOT NULL,
  `nama_kota` enum('Jakarta','Bandung','Yogyakarta','Semarang') NOT NULL,
  `pos_A` decimal(10,2) DEFAULT 0.00,
  `pos_B` decimal(10,2) DEFAULT 0.00,
  `pos_C` decimal(15,2) DEFAULT 0.00,
  `pos_D` decimal(15,2) DEFAULT 0.00,
  `pos_E` decimal(15,2) DEFAULT 0.00,
  `pos_F` decimal(15,2) DEFAULT 0.00,
  `pos_G` decimal(15,2) DEFAULT 0.00,
  `pos_H` decimal(15,2) DEFAULT 0.00,
  `pos_I` decimal(15,2) DEFAULT 0.00,
  `pos_1` decimal(15,2) DEFAULT 0.00,
  `pos_2` decimal(15,2) DEFAULT 0.00,
  `pos_3` decimal(15,2) DEFAULT 0.00,
  `pos_4` decimal(15,2) DEFAULT 0.00,
  `pos_5` decimal(15,2) DEFAULT 0.00,
  `pos_6` decimal(15,2) DEFAULT 0.00,
  `pos_7` decimal(15,2) DEFAULT 0.00,
  `pos_8` decimal(15,2) DEFAULT 0.00,
  `pos_9` decimal(15,2) DEFAULT 0.00,
  `pos_10` decimal(15,2) DEFAULT 0.00,
  `pos_11` decimal(15,2) DEFAULT 0.00,
  `pos_12` decimal(15,2) DEFAULT 0.00,
  `pos_13` decimal(15,2) DEFAULT 0.00,
  `pos_14` decimal(15,2) DEFAULT 0.00,
  `pos_15` decimal(15,2) DEFAULT 0.00,
  `pos_16` decimal(15,2) DEFAULT 0.00,
  `pos_17` decimal(15,2) DEFAULT 0.00,
  `pos_18` decimal(15,2) DEFAULT 0.00,
  `pos_19` decimal(15,2) DEFAULT 0.00,
  `pos_20` decimal(15,2) DEFAULT 0.00,
  `pos_21` decimal(15,2) DEFAULT 0.00,
  `pos_22` decimal(15,2) DEFAULT 0.00,
  `pos_23` decimal(15,2) DEFAULT 0.00,
  `pos_24` decimal(15,2) DEFAULT 0.00,
  `pos_25` decimal(15,2) DEFAULT 0.00,
  `pos_26` decimal(15,2) DEFAULT 0.00,
  `pos_27` decimal(15,2) DEFAULT 0.00,
  `pos_28` decimal(15,2) DEFAULT 0.00,
  `pos_29` decimal(15,2) DEFAULT 0.00,
  `pos_30` decimal(15,2) DEFAULT 0.00,
  `pos_31` decimal(15,2) DEFAULT 0.00,
  `pos_32` decimal(15,2) DEFAULT 0.00,
  `pos_33` decimal(15,2) DEFAULT 0.00,
  `pos_34` decimal(15,2) DEFAULT 0.00,
  `pos_35` decimal(15,2) DEFAULT 0.00,
  `pos_36` decimal(15,2) DEFAULT 0.00,
  `pos_37` decimal(15,2) DEFAULT 0.00,
  `pos_38` decimal(15,2) DEFAULT 0.00,
  `jumlah_penerimaan` decimal(15,2) DEFAULT 0.00,
  `jumlah_beban` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `1_data`
--

INSERT INTO `1_data` (`id_anggaran`, `nama_pemimpinlokal`, `nama_bendaharakomunitas`, `nama_kota`, `pos_A`, `pos_B`, `pos_C`, `pos_D`, `pos_E`, `pos_F`, `pos_G`, `pos_H`, `pos_I`, `pos_1`, `pos_2`, `pos_3`, `pos_4`, `pos_5`, `pos_6`, `pos_7`, `pos_8`, `pos_9`, `pos_10`, `pos_11`, `pos_12`, `pos_13`, `pos_14`, `pos_15`, `pos_16`, `pos_17`, `pos_18`, `pos_19`, `pos_20`, `pos_21`, `pos_22`, `pos_23`, `pos_24`, `pos_25`, `pos_26`, `pos_27`, `pos_28`, `pos_29`, `pos_30`, `pos_31`, `pos_32`, `pos_33`, `pos_34`, `pos_35`, `pos_36`, `pos_37`, `pos_38`, `jumlah_penerimaan`, `jumlah_beban`) VALUES
(1, 'Bruder Thomas', 'Bruder Frith', 'Semarang', 0.00, 0.00, 36000000.00, 12000000.00, 5000000.00, 2000000.00, 20000000.00, 3000000.00, 5000000.00, 24000000.00, 0.00, 6000000.00, 0.00, 4800000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 5400000.00, 3600000.00, 0.00, 0.00, 8000000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 2000000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 4000000.00, 0.00, 0.00, 0.00, 83000000.00, 57800000.00),
(2, '', '', '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(3, '', '', '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(4, 'uy', 'tr', 'Jakarta', 0.00, 525.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(5, 'uy', 'tr', 'Jakarta', 0.00, 525.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00);

--
-- Triggers `1_data`
--
DELIMITER $$
CREATE TRIGGER `trg_1data_before_ins` BEFORE INSERT ON `1_data` FOR EACH ROW BEGIN
    SET NEW.jumlah_penerimaan =
        COALESCE(NEW.pos_C,0) + COALESCE(NEW.pos_D,0) + COALESCE(NEW.pos_E,0) +
        COALESCE(NEW.pos_F,0) + COALESCE(NEW.pos_G,0) + COALESCE(NEW.pos_H,0) +
        COALESCE(NEW.pos_I,0);

    SET NEW.jumlah_beban =
        COALESCE(NEW.pos_1,0) + COALESCE(NEW.pos_2,0) + COALESCE(NEW.pos_3,0) + COALESCE(NEW.pos_4,0) +
        COALESCE(NEW.pos_5,0) + COALESCE(NEW.pos_6,0) + COALESCE(NEW.pos_7,0) + COALESCE(NEW.pos_8,0) +
        COALESCE(NEW.pos_9,0) + COALESCE(NEW.pos_10,0) + COALESCE(NEW.pos_11,0) + COALESCE(NEW.pos_12,0) +
        COALESCE(NEW.pos_13,0) + COALESCE(NEW.pos_14,0) + COALESCE(NEW.pos_15,0) + COALESCE(NEW.pos_16,0) +
        COALESCE(NEW.pos_17,0) + COALESCE(NEW.pos_18,0) + COALESCE(NEW.pos_19,0) + COALESCE(NEW.pos_20,0) +
        COALESCE(NEW.pos_21,0) + COALESCE(NEW.pos_22,0) + COALESCE(NEW.pos_23,0) + COALESCE(NEW.pos_24,0) +
        COALESCE(NEW.pos_25,0) + COALESCE(NEW.pos_26,0) + COALESCE(NEW.pos_27,0) + COALESCE(NEW.pos_28,0) +
        COALESCE(NEW.pos_29,0) + COALESCE(NEW.pos_30,0) + COALESCE(NEW.pos_31,0) + COALESCE(NEW.pos_32,0) +
        COALESCE(NEW.pos_33,0) + COALESCE(NEW.pos_34,0) + COALESCE(NEW.pos_35,0) + COALESCE(NEW.pos_36,0) +
        COALESCE(NEW.pos_37,0) + COALESCE(NEW.pos_38,0);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_1data_before_upd` BEFORE UPDATE ON `1_data` FOR EACH ROW BEGIN
    SET NEW.jumlah_penerimaan =
        COALESCE(NEW.pos_C,0) + COALESCE(NEW.pos_D,0) + COALESCE(NEW.pos_E,0) +
        COALESCE(NEW.pos_F,0) + COALESCE(NEW.pos_G,0) + COALESCE(NEW.pos_H,0) +
        COALESCE(NEW.pos_I,0);

    SET NEW.jumlah_beban =
        COALESCE(NEW.pos_1,0) + COALESCE(NEW.pos_2,0) + COALESCE(NEW.pos_3,0) + COALESCE(NEW.pos_4,0) +
        COALESCE(NEW.pos_5,0) + COALESCE(NEW.pos_6,0) + COALESCE(NEW.pos_7,0) + COALESCE(NEW.pos_8,0) +
        COALESCE(NEW.pos_9,0) + COALESCE(NEW.pos_10,0) + COALESCE(NEW.pos_11,0) + COALESCE(NEW.pos_12,0) +
        COALESCE(NEW.pos_13,0) + COALESCE(NEW.pos_14,0) + COALESCE(NEW.pos_15,0) + COALESCE(NEW.pos_16,0) +
        COALESCE(NEW.pos_17,0) + COALESCE(NEW.pos_18,0) + COALESCE(NEW.pos_19,0) + COALESCE(NEW.pos_20,0) +
        COALESCE(NEW.pos_21,0) + COALESCE(NEW.pos_22,0) + COALESCE(NEW.pos_23,0) + COALESCE(NEW.pos_24,0) +
        COALESCE(NEW.pos_25,0) + COALESCE(NEW.pos_26,0) + COALESCE(NEW.pos_27,0) + COALESCE(NEW.pos_28,0) +
        COALESCE(NEW.pos_29,0) + COALESCE(NEW.pos_30,0) + COALESCE(NEW.pos_31,0) + COALESCE(NEW.pos_32,0) +
        COALESCE(NEW.pos_33,0) + COALESCE(NEW.pos_34,0) + COALESCE(NEW.pos_35,0) + COALESCE(NEW.pos_36,0) +
        COALESCE(NEW.pos_37,0) + COALESCE(NEW.pos_38,0);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `2_perkiraan`
--

CREATE TABLE `2_perkiraan` (
  `ID_pos` varchar(5) NOT NULL,
  `kode` varchar(50) DEFAULT NULL,
  `akun` varchar(100) DEFAULT NULL,
  `posisi` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `2_perkiraan`
--

INSERT INTO `2_perkiraan` (`ID_pos`, `kode`, `akun`, `posisi`) VALUES
('1', '510101', 'Makanan', 'Pengeluaran'),
('10', '510105', 'Iuran Hidup Bermasyarakat Dan Menggereja', 'Pengeluaran'),
('11', '510401', 'Listrik', 'Pengeluaran'),
('12', '510402', 'Air', 'Pengeluaran'),
('13', '510403', 'Telepon Dan Internet', 'Pengeluaran'),
('14', '520201', 'Keperluan Ibadah', 'Pengeluaran'),
('15', '530303', 'Sumbangan', 'Pengeluaran'),
('16', '540101', 'Insentif ART', 'Pengeluaran'),
('17', '540201', 'Pemeliharaan Rumah', 'Pengeluaran'),
('18', '540202', 'Pemeliharaan Kebun Dan Piaraan', 'Pengeluaran'),
('19', '540203', 'Pemeliharaan Kendaraan', 'Pengeluaran'),
('2', '510201', 'Pakaian Dan Perlengkapan Pribadi', 'Pengeluaran'),
('20', '540204', 'Pemeliharaan Mesin Dan Peralatan', 'Pengeluaran'),
('21', '550101', 'Administrasi Komunitas', 'Pengeluaran'),
('22', '550105', 'Legal dan Perijinan', 'Pengeluaran'),
('23', '550106', 'Buku, Majalah, Koran', 'Pengeluaran'),
('24', '550107', 'Administrasi Bank', 'Pengeluaran'),
('25', '550201', 'Pajak Bunga Bank', 'Pengeluaran'),
('26', '550202', 'Pajak Kendaraan Dan PBB', 'Pengeluaran'),
('27', '110202', 'Kas Kecil DP', 'Pengeluaran'),
('28', '110201', 'Kas Kecil Komunitas', 'Pengeluaran'),
('29', '520501', 'Penunjang Kesehatan Lansia', 'Pengeluaran'),
('3', '510301', 'Pemeriksaan Dan Pengobatan', 'Pengeluaran'),
('30', '520502', 'Pemeliharaan Rohani Lansia', 'Pengeluaran'),
('31', '520503', 'Kegiatan Bruder Lansia', 'Pengeluaran'),
('32', '130400', 'Mesin dan Peralatan', 'Pengeluaran'),
('33', '510100', 'Perabot Rumah Tangga', 'Pengeluaran'),
('34', '510502', 'Transport Pertemuan', 'Pengeluaran'),
('35', '520302', 'Perayaan Syukur', 'Pengeluaran'),
('36', '520500', 'Kegiatan Lansia', 'Pengeluaran'),
('37', '540200', 'Pemeliharaan Rumah', 'Pengeluaran'),
('38', '550100', 'Budget Khusus Lainnya', 'Pengeluaran'),
('39', '510300', 'Pemeriksaan Dan Pengobatan', 'Pengeluaran'),
('4', '510303', 'Hiburan / Rekreasi', 'Pengeluaran'),
('40', '530201', 'Pertemuan DP', 'Pengeluaran'),
('41', '530100', 'Kegiatan Acc DP', 'Pengeluaran'),
('5', '510501', 'Transport Harian', 'Pengeluaran'),
('6', '520401', 'Studi Pribadi', 'Pengeluaran'),
('7', '510102', 'Bahan Bakar Dapur', 'Pengeluaran'),
('8', '510103', 'Perlengkapan Cuci dan Kebersihan', 'Pengeluaran'),
('9', '510104', 'Perabot Rumah Tangga', 'Pengeluaran'),
('A', '110100', 'Kas', 'Pemasukan'),
('B', '110301', 'Bank', 'Pemasukan'),
('C', '410101', 'Gaji/Pendapatan Bruder', 'Pemasukan'),
('D', '410102', 'Pensiun Bruder', 'Pemasukan'),
('E', '430101', 'Hasil Kebun Dan Piaraan', 'Pemasukan'),
('F', '420101', 'Bunga Tabungan', 'Pemasukan'),
('G', '410202', 'Sumbangan', 'Pemasukan'),
('H', '430103', 'Penerimaan Lainnya', 'Pemasukan'),
('I', '610100', 'Penerimaan dari DP', 'Pemasukan');

-- --------------------------------------------------------

--
-- Table structure for table `3_kas_harian`
--

CREATE TABLE `3_kas_harian` (
  `ID_kas_harian` int(11) NOT NULL,
  `tgl_kas_harian` date NOT NULL,
  `ID_pos` varchar(5) NOT NULL,
  `keterangan_kas` text DEFAULT NULL,
  `ID_bruder` int(11) DEFAULT NULL,
  `nominal` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `3_kas_harian`
--

INSERT INTO `3_kas_harian` (`ID_kas_harian`, `tgl_kas_harian`, `ID_pos`, `keterangan_kas`, `ID_bruder`, `nominal`) VALUES
(21, '2025-09-01', 'A', 'Saldo Awal', 1, 30000000.00),
(23, '2025-01-05', '5', 'Transport harian ke pasar', 1, 100000.00),
(24, '2025-01-06', '11', 'Bayar listrik bulan Januari', 2, 750000.00),
(25, '2025-01-07', '12', 'Bayar air PDAM', 3, 500000.00);

--
-- Triggers `3_kas_harian`
--
DELIMITER $$
CREATE TRIGGER `trg_kas_harian_ad` AFTER DELETE ON `3_kas_harian` FOR EACH ROW BEGIN
    DELETE FROM `6_lu_komunitas` WHERE id_pos = OLD.ID_pos;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_kas_harian_ai` AFTER INSERT ON `3_kas_harian` FOR EACH ROW BEGIN
    INSERT INTO `6_lu_komunitas` (id_pos, nominal_pemasukan, nominal_pengeluaran)
    VALUES (
        NEW.ID_pos,
        CASE WHEN NEW.nominal >= 0 THEN NEW.nominal ELSE 0 END,
        CASE WHEN NEW.nominal < 0 THEN ABS(NEW.nominal) ELSE 0 END
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_kas_harian_au` AFTER UPDATE ON `3_kas_harian` FOR EACH ROW BEGIN
    UPDATE `6_lu_komunitas`
    SET nominal_pemasukan = CASE WHEN NEW.nominal >= 0 THEN NEW.nominal ELSE 0 END,
        nominal_pengeluaran = CASE WHEN NEW.nominal < 0 THEN ABS(NEW.nominal) ELSE 0 END
    WHERE id_pos = OLD.ID_pos;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `4_bank`
--

CREATE TABLE `4_bank` (
  `ID_tabel_bank` varchar(10) NOT NULL,
  `nama_bank` varchar(100) NOT NULL,
  `no_rek_bank` varchar(50) NOT NULL,
  `atas_nama_bank` varchar(100) NOT NULL,
  `tgl_transaksi` date NOT NULL,
  `ID_pos` varchar(5) NOT NULL,
  `keterangan_bank` text DEFAULT NULL,
  `nominal_penerimaan` decimal(15,2) DEFAULT 0.00,
  `nominal_pengeluaran` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `4_bank`
--

INSERT INTO `4_bank` (`ID_tabel_bank`, `nama_bank`, `no_rek_bank`, `atas_nama_bank`, `tgl_transaksi`, `ID_pos`, `keterangan_bank`, `nominal_penerimaan`, `nominal_pengeluaran`) VALUES
('28-1', 'Bank BRI', '4567891230', 'Agustinus Suparno', '2025-01-07', '28', 'Kas kecil komunitas', 0.00, 2000000.00),
('B001', 'Mandiri', '1850001474409', 'Agustinus Suparno', '2025-09-04', 'I', 'Terima kiriman dari bendahara DP', 22000000.00, 0.00),
('C1', 'Bank BCA', '1234567890', 'Agustinus Suparno', '2025-01-06', 'C', 'Gaji Bruder masuk', 3000000.00, 0.00),
('I1', 'Bank BCA', '1234567890', 'Agustinus Suparno', '2025-01-05', 'I', 'Transfer dari DP', 5000000.00, 0.00);

--
-- Triggers `4_bank`
--
DELIMITER $$
CREATE TRIGGER `trg_bank_ad` AFTER DELETE ON `4_bank` FOR EACH ROW BEGIN
    DELETE FROM `6_lu_komunitas`
    WHERE id_pos = OLD.ID_pos;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_bank_ai` AFTER INSERT ON `4_bank` FOR EACH ROW BEGIN
    INSERT INTO `6_lu_komunitas` (id_pos, nominal_pemasukan, nominal_pengeluaran)
    VALUES (
        NEW.ID_pos,
        NEW.nominal_penerimaan,
        NEW.nominal_pengeluaran
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_bank_au` AFTER UPDATE ON `4_bank` FOR EACH ROW BEGIN
    UPDATE `6_lu_komunitas`
    SET nominal_pemasukan = NEW.nominal_penerimaan,
        nominal_pengeluaran = NEW.nominal_pengeluaran
    WHERE id_pos = OLD.ID_pos;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `5_bruder`
--

CREATE TABLE `5_bruder` (
  `ID_pp` int(11) NOT NULL,
  `ID_bruder` int(11) DEFAULT NULL,
  `tgl_datang_komunitas` date DEFAULT NULL,
  `tgl_pulang_komunitas` date DEFAULT NULL,
  `tgl_pergi_luarkota` date DEFAULT NULL,
  `tgl_pulang_luarKota` date DEFAULT NULL,
  `jumlah_hari` int(11) DEFAULT NULL,
  `keterangan_pp` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `5_bruder`
--

INSERT INTO `5_bruder` (`ID_pp`, `ID_bruder`, `tgl_datang_komunitas`, `tgl_pulang_komunitas`, `tgl_pergi_luarkota`, `tgl_pulang_luarKota`, `jumlah_hari`, `keterangan_pp`) VALUES
(1, 1, '2025-01-01', '2025-01-15', NULL, NULL, 14, 'Komunitas Magelang');

--
-- Triggers `5_bruder`
--
DELIMITER $$
CREATE TRIGGER `trg_hitungan_hari_bruder` BEFORE INSERT ON `5_bruder` FOR EACH ROW BEGIN
    -- Jika punya data datang & pulang komunitas
    IF NEW.tgl_datang_komunitas IS NOT NULL 
       AND NEW.tgl_pulang_komunitas IS NOT NULL THEN
        SET NEW.jumlah_hari = DATEDIFF(NEW.tgl_pulang_komunitas, NEW.tgl_datang_komunitas);

    -- Jika punya data pergi & pulang luar kota
    ELSEIF NEW.tgl_pergi_luarkota IS NOT NULL 
       AND NEW.tgl_pulang_luarKota IS NOT NULL THEN
        SET NEW.jumlah_hari = DATEDIFF(NEW.tgl_pulang_luarKota, NEW.tgl_pergi_luarkota);

    ELSE
        SET NEW.jumlah_hari = 0;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `6_lu_komunitas`
--

CREATE TABLE `6_lu_komunitas` (
  `id_lu` int(11) NOT NULL,
  `id_anggaran` int(11) NOT NULL,
  `id_pos` varchar(5) NOT NULL,
  `tgl_transaksi` date DEFAULT NULL,
  `nominal_pemasukan` decimal(15,2) DEFAULT 0.00,
  `nominal_pengeluaran` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `6_lu_komunitas`
--

INSERT INTO `6_lu_komunitas` (`id_lu`, `id_anggaran`, `id_pos`, `tgl_transaksi`, `nominal_pemasukan`, `nominal_pengeluaran`) VALUES
(1, 0, 'A', '2025-01-01', 30000000.00, 0.00),
(3, 0, 'I', '2025-01-01', 22000000.00, 0.00),
(5, 0, '5', '2025-01-01', 100000.00, 0.00),
(6, 0, '11', '2025-01-01', 750000.00, 0.00),
(7, 0, '12', '2025-01-01', 500000.00, 0.00),
(8, 0, 'I', '2025-01-01', 5000000.00, 0.00),
(9, 0, 'C', '2025-01-01', 3000000.00, 0.00),
(10, 0, '28', '2025-01-01', 0.00, 2000000.00);

-- --------------------------------------------------------

--
-- Table structure for table `7_evaluasi`
--

CREATE TABLE `7_evaluasi` (
  `id_eval` int(11) NOT NULL,
  `nama_komunitas` varchar(100) DEFAULT NULL,
  `id_pos` varchar(5) NOT NULL,
  `anggaran` decimal(15,2) DEFAULT NULL,
  `realisasi` decimal(15,2) DEFAULT NULL,
  `selisih` decimal(15,2) DEFAULT NULL,
  `persentase` decimal(5,2) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `8_kas_opname`
--

CREATE TABLE `8_kas_opname` (
  `id` int(11) NOT NULL,
  `hari` varchar(20) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `waktu` time DEFAULT NULL,
  `tempat` varchar(50) DEFAULT NULL,
  `lokasi` varchar(255) DEFAULT NULL,
  `saldo_catatan` decimal(15,2) DEFAULT NULL,
  `kas_kecil` decimal(15,2) DEFAULT NULL,
  `saldo_bendahara` decimal(15,2) DEFAULT NULL,
  `jumlah_hasil` decimal(15,2) DEFAULT NULL,
  `selisih` decimal(15,2) DEFAULT NULL,
  `pemimpin_nama` varchar(100) DEFAULT NULL,
  `pemimpin_ttd` longblob DEFAULT NULL,
  `bendahara_nama` varchar(100) DEFAULT NULL,
  `bendahara_ttd` longblob DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `8_kas_opname`
--

INSERT INTO `8_kas_opname` (`id`, `hari`, `tanggal`, `waktu`, `tempat`, `lokasi`, `saldo_catatan`, `kas_kecil`, `saldo_bendahara`, `jumlah_hasil`, `selisih`, `pemimpin_nama`, `pemimpin_ttd`, `bendahara_nama`, `bendahara_ttd`, `created_at`) VALUES
(1, 'Rabu', '2025-10-01', '10:20:00', 'Candi', 'Go', 1200000.00, 150000.00, 1.35, 1.09, 260.00, 'lel', 0x313735393238393135325f70656d696d70696e5f74686f6d2e6a7067, 'lol', 0x313735393238393135325f62656e6461686172615f6b68696d2e6a7067, '2025-10-01 03:25:52'),
(2, 'Rabu', '2025-10-01', '10:20:00', 'Candi', 'Go', 1200000.00, 150000.00, 1.35, 1.09, 260.00, 'lel', 0x313735393238393337375f70656d696d70696e5f74686f6d2e6a7067, 'lol', 0x313735393238393337375f62656e6461686172615f6b68696d2e6a7067, '2025-10-01 03:29:37'),
(3, 'Rabu', '2025-10-01', '10:20:00', 'Candi', 'Go', 1200000.00, 150000.00, 1.35, 1.09, 260.00, 'lel', 0x313735393238393439375f70656d696d70696e5f74686f6d2e6a7067, 'lol', 0x313735393238393439375f62656e6461686172615f6b68696d2e6a7067, '2025-10-01 03:31:37'),
(4, 'Senin', '0000-00-00', '00:00:00', 'Candi', '', 0.00, 0.00, 0.00, 0.00, 0.00, '', 0x313735393238393537365f70656d696d70696e5f6669632e706e67, '', NULL, '2025-10-01 03:32:56');

-- --------------------------------------------------------

--
-- Table structure for table `data_bruder`
--

CREATE TABLE `data_bruder` (
  `ID_bruder` int(11) NOT NULL,
  `nama_bruder` varchar(100) DEFAULT NULL,
  `gambar_bruder` varchar(255) DEFAULT NULL,
  `ttl_bruder` date DEFAULT NULL,
  `alamat_bruder` text DEFAULT NULL,
  `tahun_masuk_postulan` year(4) DEFAULT NULL,
  `tahun_prasetia_pertama` year(4) DEFAULT NULL,
  `tahun_kaul_kekal` year(4) DEFAULT NULL,
  `riwayat_tugas` text DEFAULT NULL,
  `unit_kerja` varchar(100) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `no_telp` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `foto` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_bruder`
--

INSERT INTO `data_bruder` (`ID_bruder`, `nama_bruder`, `gambar_bruder`, `ttl_bruder`, `alamat_bruder`, `tahun_masuk_postulan`, `tahun_prasetia_pertama`, `tahun_kaul_kekal`, `riwayat_tugas`, `unit_kerja`, `alamat`, `no_telp`, `email`, `foto`) VALUES
(1, 'Bruder Thomas FIC', 'thomas.jpg', '1999-11-21', 'Komunitas Bruder FIC, Semarang', '2017', '2018', '2019', 'Pernah bertugas di Muntilan dan Semarang (2021-sekarang)', 'Ekonomi', 'Jl. Pawiyatan Luhur, Semarang', '0852-9012-7455', 'thomas@gmail.com', 'thom.jpg'),
(2, 'Bruder Frith FIC', 'frith.jpg', '1987-03-21', 'Komunitas Bruder FIC, Yogyakarta', '2005', '2008', '2014', 'Pernah bertugas di Medan (2009-2014), Bandung (2014-2020), dan Yogyakarta (2020-sekarang)', 'Bruder', 'Jl. Banyumanik, Semarang', '0838-3890-5802', 'frit@gmail.com', 'frit.jpg'),
(3, 'Bruder Khim FIC', 'khim.jpg', '1992-11-02', 'Komunitas Bruder FIC, Jakarta', '2010', '2013', '2019', 'Pernah bertugas di Jakarta (2013-2018), Makassar (2018-2022), dan Jakarta (2022-sekarang)', 'Bruder', 'Jl. Kartini, Semarang', '0878-1579-4900', 'khim@gmail.com', 'khim.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `login_bruder`
--

CREATE TABLE `login_bruder` (
  `ID_bruder` int(11) NOT NULL,
  `nama_bruder` varchar(100) DEFAULT NULL,
  `password_bruder` varchar(255) DEFAULT NULL,
  `status` enum('econom','bruder') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_bruder`
--

INSERT INTO `login_bruder` (`ID_bruder`, `nama_bruder`, `password_bruder`, `status`) VALUES
(1, 'Thomas', 'thom1', 'econom'),
(2, 'Frith', 'frith1', 'bruder');

-- --------------------------------------------------------

--
-- Table structure for table `tabel_komunitas`
--

CREATE TABLE `tabel_komunitas` (
  `nama_komunitas` varchar(100) NOT NULL,
  `ID_bruder` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `1_data`
--
ALTER TABLE `1_data`
  ADD PRIMARY KEY (`id_anggaran`);

--
-- Indexes for table `2_perkiraan`
--
ALTER TABLE `2_perkiraan`
  ADD PRIMARY KEY (`ID_pos`);

--
-- Indexes for table `3_kas_harian`
--
ALTER TABLE `3_kas_harian`
  ADD PRIMARY KEY (`ID_kas_harian`),
  ADD KEY `3_kas_harian_fk_perkiraan` (`ID_pos`),
  ADD KEY `3_kas_harian_fk_bruder` (`ID_bruder`);

--
-- Indexes for table `4_bank`
--
ALTER TABLE `4_bank`
  ADD PRIMARY KEY (`ID_tabel_bank`),
  ADD KEY `4_bank_fk_perkiraan` (`ID_pos`);

--
-- Indexes for table `5_bruder`
--
ALTER TABLE `5_bruder`
  ADD PRIMARY KEY (`ID_pp`),
  ADD KEY `ID_bruder` (`ID_bruder`);

--
-- Indexes for table `6_lu_komunitas`
--
ALTER TABLE `6_lu_komunitas`
  ADD PRIMARY KEY (`id_lu`);

--
-- Indexes for table `7_evaluasi`
--
ALTER TABLE `7_evaluasi`
  ADD PRIMARY KEY (`id_eval`),
  ADD KEY `fk_evaluasi_pos` (`id_pos`);

--
-- Indexes for table `8_kas_opname`
--
ALTER TABLE `8_kas_opname`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_bruder`
--
ALTER TABLE `data_bruder`
  ADD PRIMARY KEY (`ID_bruder`);

--
-- Indexes for table `login_bruder`
--
ALTER TABLE `login_bruder`
  ADD PRIMARY KEY (`ID_bruder`);

--
-- Indexes for table `tabel_komunitas`
--
ALTER TABLE `tabel_komunitas`
  ADD PRIMARY KEY (`nama_komunitas`,`ID_bruder`),
  ADD KEY `ID_bruder` (`ID_bruder`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `1_data`
--
ALTER TABLE `1_data`
  MODIFY `id_anggaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `3_kas_harian`
--
ALTER TABLE `3_kas_harian`
  MODIFY `ID_kas_harian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `5_bruder`
--
ALTER TABLE `5_bruder`
  MODIFY `ID_pp` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `6_lu_komunitas`
--
ALTER TABLE `6_lu_komunitas`
  MODIFY `id_lu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `7_evaluasi`
--
ALTER TABLE `7_evaluasi`
  MODIFY `id_eval` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `8_kas_opname`
--
ALTER TABLE `8_kas_opname`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `data_bruder`
--
ALTER TABLE `data_bruder`
  MODIFY `ID_bruder` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `3_kas_harian`
--
ALTER TABLE `3_kas_harian`
  ADD CONSTRAINT `3_kas_harian_fk_bruder` FOREIGN KEY (`ID_bruder`) REFERENCES `data_bruder` (`ID_bruder`),
  ADD CONSTRAINT `3_kas_harian_fk_perkiraan` FOREIGN KEY (`ID_pos`) REFERENCES `2_perkiraan` (`ID_pos`);

--
-- Constraints for table `4_bank`
--
ALTER TABLE `4_bank`
  ADD CONSTRAINT `4_bank_fk_perkiraan` FOREIGN KEY (`ID_pos`) REFERENCES `2_perkiraan` (`ID_pos`);

--
-- Constraints for table `5_bruder`
--
ALTER TABLE `5_bruder`
  ADD CONSTRAINT `5_bruder_ibfk_1` FOREIGN KEY (`ID_bruder`) REFERENCES `data_bruder` (`ID_bruder`);

--
-- Constraints for table `7_evaluasi`
--
ALTER TABLE `7_evaluasi`
  ADD CONSTRAINT `fk_evaluasi_pos` FOREIGN KEY (`id_pos`) REFERENCES `2_perkiraan` (`ID_pos`);

--
-- Constraints for table `login_bruder`
--
ALTER TABLE `login_bruder`
  ADD CONSTRAINT `login_bruder_ibfk_1` FOREIGN KEY (`ID_bruder`) REFERENCES `data_bruder` (`ID_bruder`);

--
-- Constraints for table `tabel_komunitas`
--
ALTER TABLE `tabel_komunitas`
  ADD CONSTRAINT `tabel_komunitas_ibfk_1` FOREIGN KEY (`ID_bruder`) REFERENCES `data_bruder` (`ID_bruder`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
