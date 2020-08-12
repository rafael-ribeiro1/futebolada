<?php
$servername = "localhost";
$dBUsername = "root";
$dBPassword = "";
$dBName = "db_futebolada";

$conn = mysqli_connect($servername, $dBUsername, $dBPassword, $dBName);
mysqli_set_charset($conn,"utf8mb4");

if (!$conn) {
    die("Connection failed: ".mysqli_connect_error());
}
