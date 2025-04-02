<?php
session_start();
include("kantayhteys.php");
?>

<?php
// DEBUG, poista kun sivu toimii
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

header("Content-Type: text/html; charset=utf-8");

if (isset($_SESSION['LOGGEDIN']) && $_SESSION['LOGGEDIN'] == 1) {
    // Poistetaan tämän sessionin muuttujat ja lopulta session itse
    session_unset();
    session_destroy();

    echo "Uloskirjautuminen onnistui! <a href='kirjautuminen.html'>Kirjaudu sisään</a> tai <a href='index.php'>palaa etusivulle</a>.";
}
?>