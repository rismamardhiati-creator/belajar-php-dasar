<?php
$a = true;
$b = false;

// Operator Logika
echo var_export($a && $b, true) . "<br>"; // AND (&&)
echo var_export($a || $b, true) . "<br>"; // OR (||)
echo var_export(!$a, true) . "<br>"; // NOT (!)
echo var_export($a xor $b, true) . "<br>"; // XOR (xor)
?>