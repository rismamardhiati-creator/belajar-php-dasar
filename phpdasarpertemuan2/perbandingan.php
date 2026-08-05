<?php
$a = 30;
$b = 18;

// Operator Perbandingan
echo var_export($a == $b, true) . "<br>"; // Sama dengan (==)
echo var_export($a === $b, true) . "<br>"; // Identik (===)
echo var_export($a != $b, true) . "<br>"; // Tidak sama dengan (!=)
echo var_export($a !== $b, true) . "<br>"; // Tidak identik (!==)
echo var_export($a > $b, true) . "<br>"; // Lebih besar dari (>)
echo var_export($a < $b, true) . "<br>"; // Lebih kecil dari (<)
echo var_export($a >= $b, true) . "<br>"; // Lebih besar atau sama dengan (>=)
echo var_export($a <= $b, true) . "<br>"; // Lebih kecil atau sama dengan (<=)
?>