<?php

class Motor {
    public $merk = "Honda";
    public $bensin = 85;

    public function cekStatus() {
        // Mengambil property $merk dan $bensin milik class ini pakai $this
        echo "Motor " . $this->merk . " memiliki sisa bensin " . $this->bensin . "%";
    }
}

$motorSaya = new Motor();

// Memanggil method
$motorSaya->cekStatus();

?>