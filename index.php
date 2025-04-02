<?php
session_start();

// DEBUG, TODO, poista kun sivu toimii
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: text/html; charset=utf-8");

// Asetetaan aikavyöhyke Suomen aikaan
date_default_timezone_set("Europe/Helsinki");

echo "<h2>Osto- ja myyntipalsta</h2>";

if (isset($_SESSION['LOGGEDIN']) && $_SESSION['LOGGEDIN'] == 1) {
    echo "Tervetuloa käyttämään palvelua " . $_SESSION["kayttaja_tunnus"] . "!<br>";

    echo "(<a href='lisaailmoitus.php'>Lisää ilmoitus</a>) - (<a href='tiedot.php'>Muuta tietojasi</a>)
    - (<a href='uloskirjautuminen.php'>Kirjaudu ulos</a>)";
}
else {
    echo "<a href='kirjautuminen.html'>Kirjaudu sisään</a> tai
    <a href='rekisterointi.html'>rekisteröidy palveluun</a>.";
}
?>