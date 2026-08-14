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

$sql = "UPDATE mahasiswa SET nama='Andin Diandra', umur=17, alamat='Sumedang' WHERE id=1";

if (mysqli_query($conn, $sql)) {
    echo "Data berhasil diperbarui<br>";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>