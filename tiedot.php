<?php
session_start();
include("kantayhteys.php");

// DEBUG
if ($DEBUG_TILA) {
    ini_set("display_errors", 1);
    ini_set("display_startup_errors", 1);
    error_reporting(E_ALL);
}

header("Content-Type: text/html; charset=utf-8");

if (isset($_SESSION['LOGGEDIN']) && $_SESSION['LOGGEDIN'] == 1) {
    $sahkoposti = $_SESSION["kayttaja_sahkoposti"];
    echo "<form action='kayttajatunnistus.php' method='post'>";
    echo "<p>Muutetaan käyttäjätietoja käyttäjälle '<b>" .
    $_SESSION["kayttaja_tunnus"] . "</b>':</p>";
    echo "<h3>Salasanan muuttaminen</h3>";
    echo "Entinen salasana: <input name='kayttaja_salasana' type='password'> <br>";
    echo "Uusi salasana: <input name='kayttaja_uusisalasana' type='password'> <br>";
    echo "Nykyinen sähköpostiosoite: $sahkoposti <br>";
    echo "Uusi sähköpostiosoite: <input name='kayttaja_uusisahkoposti' type='email' value='$sahkoposti'> <br>";
    echo "<input type='hidden' name='kayttaja_tunnus' value='$_SESSION[kayttaja_tunnus]'>";
    echo "<input type='hidden' name='lomaketunnistin' value='2'>";
    echo "<p><input type='submit' value'Muuta'></p>";
    echo "</form>";
    echo "<a href='index.php'>Palaa etusivulle</a>";
}
else {
    echo "<title>Virhe!</title>";
    echo "Et ole kirjautunut sisään!";
}
?>