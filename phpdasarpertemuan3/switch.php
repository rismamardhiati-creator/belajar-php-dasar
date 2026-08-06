<?php
$hari = "Senin";

switch ($hari) {
    case "Senin":
        echo "Mulai bekerja!";
        break;
    case "Jumat":
        echo "Hampir akhir pekan!";
        break;
    case "Sabtu":
        echo "Waktunya libur!";
        break;
    default:
        echo "Hari biasa";
}