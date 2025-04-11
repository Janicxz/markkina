<?php
session_start();
include("kantayhteys.php");
include("kuvahallinta.php");
include("ilmoitus.php");

if ($DEBUG_TILA) {
    ini_set("display_errors", 1);
    ini_set("display_startup_errors", 1);
    error_reporting(E_ALL);
}
?>

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Markkinapaikka</title>

    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Osto- ja myyntipalsta</h2>
    <?php
    if (isset($_SESSION['LOGGEDIN']) && $_SESSION['LOGGEDIN'] == 1) {
        echo "<p>Tervetuloa käyttämään palvelua " . $_SESSION["kayttaja_tunnus"] . "!</p><br>";

        echo "<p>(<a href='lisaailmoitus.php'>Lisää ilmoitus</a>) - (<a href='tiedot.php'>Muuta tietojasi</a>)
        - (<a href='uloskirjautuminen.php'>Kirjaudu ulos</a>)</p>";
    }
    else {
        echo "<p><a href='kirjautuminen.html'>Kirjaudu sisään</a> tai
        <a href='rekisterointi.html'>rekisteröidy palveluun</a>.</p>";
    }

    echo "<h3>Ilmoitukset:</h3>";

    echo "<p>Hae ilmoituksia:</p><br>
        <form action='haeilmoitus.php' method='post'>
            <input name='haku' type='text'>
            <input type='submit' name='submit' value='Hae'>
        </form>";
    echo "
    <p>(<a href='selaailmoituksia.php'>Selaa ilmoituksia</a>) </p>";
    
    // Näytetään omat ilmoitukset vain kirjautuneille käyttäjille
    if (isset($_SESSION["LOGGEDIN"]) && $_SESSION["LOGGEDIN"] == 1) {
        echo "<p>(<a href='selaailmoituksia.php?naytaomat=1'>Omat ilmoitukset</a>)</p>";
    }
    // Ilmoitusten tuonti, valitaan 5 uusinta ilmoitusta (suurimman ilmoitus_id:n mukaan)
    $query = "SELECT * FROM ilmoitukset INNER JOIN kayttajat ON ilmoitukset.myyja_id = kayttajat.kayttaja_id ORDER BY ilmoitukset.ilmoitus_id DESC LIMIT 5";
    $result = mysqli_query($dbconnect, $query);

    if (!$result) {
        printf("Error: %s\n", mysqli_error($dbconnect));
        exit();
    }

    $num = mysqli_num_rows($result);
    echo "<div class='ilmoitukset'>";
    $i = 0;
    while ($i < $num) {
        $row = mysqli_fetch_assoc($result);
        echo luoIlmoitusTable($row, $i);
        $i++;
    }
    echo "</div>";
    ?>
    <script src="index.js"></script>
</body>
</html>