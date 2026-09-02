<?php
namespace Data\User {
    class User {
        private string $username;
        private string $password;
        private string $namaLengkap;
        private string $role;

        public function __construct(string $username, string $password, string $namaLengkap, string $role) {
            $this->username = $username;
            $this->password = $password;
            $this->namaLengkap = $namaLengkap;
            $this->role = $role;
        }

        public function getNamaLengkap(): string {
            return $this->namaLengkap;
        }

        public function getRole(): string {
            return $this->role;
        }
    }
}

namespace Data\Layanan {
    abstract class Laundry {
        public string $namaPelanggan;
        public string $tglKembali = "";    
        public string $metodeBayar = "";  
        protected string $status = "Diproses";

        public function __construct(string $namaPelanggan, string $tglKembali = "", string $metodeBayar = "") {
            $this->namaPelanggan = $namaPelanggan;
            $this->tglKembali = $tglKembali;
            $this->metodeBayar = $metodeBayar;
        }

        public function getStatus(): string {
            return $this->status;
        }

        public function setStatus(string $status): void {
            $this->status = $status;
        }

        abstract public function hitungTotal(): float;
        abstract public function getDetail(): string;
    }

    class LaundryKiloan extends Laundry {
        private float $berat;
        private float $hargaPerKg = 7000;

        public function __construct(string $namaPelanggan, float $berat, string $tglKembali = "", string $metodeBayar = "") {
            parent::__construct($namaPelanggan, $tglKembali, $metodeBayar);
            $this->berat = $berat;
        }

        public function hitungTotal(): float {
            return $this->berat * $this->hargaPerKg;
        }

        public function getDetail(): string {
            return "Laundry Kiloan ({$this->berat} Kg)";
        }
    }

    class LaundryKarpet extends Laundry {
        private float $panjang;
        private float $lebar;
        private float $hargaPerMeter = 12000;

        public function __construct(string $namaPelanggan, float $panjang, float $lebar, string $tglKembali = "", string $metodeBayar = "") {
            parent::__construct($namaPelanggan, $tglKembali, $metodeBayar);
            $this->panjang = $panjang;
            $this->lebar = $lebar;
        }

        public function hitungTotal(): float {
            $luas = $this->panjang * $this->lebar;
            return $luas * $this->hargaPerMeter;
        }

        public function getDetail(): string {
            $luas = $this->panjang * $this->lebar;
            return "Laundry Karpet ({$luas} m²)";
        }
    }
}
?>