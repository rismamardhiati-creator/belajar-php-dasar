<?php
$orang = [
    ["Nama" => "risma", "Umur" => 17],
    ["Nama" => "andin", "Umur" => 17],
    ["Nama" => "april", "Umur" => 17]
];

foreach ($orang as $individu) {
    echo $individu["Nama"] . " berumur " . $individu["Umur"] . " tahun.<br>";
}
?>