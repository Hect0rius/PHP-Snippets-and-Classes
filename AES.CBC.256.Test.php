<?php
/*
 * Written by Hect0r Xorius <staticpi.net@gmail.com>
 */
include('AES.CBC.256.php');

$keyStr = 'FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF';

$cipheredData = aesCbc256encrypt('Hello World', $keyStr);

echo 'Ciphered : ' . $cipheredData['ciphered']) . "\n";
echo 'IV : ' . $cipheredData['iv'] . "\n";

$unciphered = aesCbc256decrypt($cipheredData['ciphered'], $keyStr, $cipheredData['iv']);

echo "Unciphered : " . $unciphered . "\n";
echo "Done :)\n";

?>
