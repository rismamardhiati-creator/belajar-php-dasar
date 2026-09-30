CREATE DATABASE IF NOT EXISTS `rizky_laundry`;
USE `rizky_laundry`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(100) NOT NULL,
  `username` VARCHAR(50) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('pelanggan', 'driver', 'admin', 'pengelola') NOT NULL,
  `no_hp` VARCHAR(20) DEFAULT NULL,
  `alamat` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_resi` VARCHAR(30) UNIQUE NOT NULL,
  `pelanggan_id` INT NOT NULL,
  `driver_id` INT DEFAULT NULL,
  `layanan` VARCHAR(100) NOT NULL,
  `berat_kg` DECIMAL(5,2) DEFAULT 0.00,
  `total_harga` DECIMAL(10,2) DEFAULT 0.00,
  `alamat_pickup` TEXT NOT NULL,
  `catatan` TEXT DEFAULT NULL,
  `status_pakaian` ENUM(
    'Menunggu Penjemputan',
    'Proses Penjemputan',
    'Tiba di Laundry',
    'Pencucian',
    'Pengeringan',
    'Penyetrikaan',
    'Siap Diantar',
    'Proses Pengantaran',
    'Selesai'
  ) DEFAULT 'Menunggu Penjemputan',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`pelanggan_id`) REFERENCES `users`(`id`),
  FOREIGN KEY (`driver_id`) REFERENCES `users`(`id`)
);