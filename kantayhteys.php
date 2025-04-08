<?php
$servername = "localhost";
$username = "markkina";
$password = "markkina";
$database = "markkina";

$DEBUG_TILA = false;

try {
    $dbconnect = mysqli_connect($servername, $username, $password, $database);
} catch (Exception $e) {
    die("Yhdistäminen tietokantaan epäonnistui:" . $e . "<br>");
}
if (!$dbconnect) {
    die("Yhdistäminen tietokantaan epäonnistui:" . mysqli_connect_error() . "<br>");
}
//echo "Yhdistettiin tietokantaan onnistuneesti<br>";
?>