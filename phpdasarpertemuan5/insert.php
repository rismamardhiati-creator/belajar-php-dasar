<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "php_dasar";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
echo "Koneksi berhasil<br>";

$sql = "INSERT INTO mahasiswa (id, nama, umur, alamat) VALUES 
        (null, 'Andin', 17, 'Sumedang Utara'),
        (null, 'iyan', 18, 'Sumedang Utara')";

if (mysqli_query($conn, $sql)) {
    echo "Data berhasil ditambahkan<br>";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}
mysqli_close($conn);
?>