<?php
session_start();
include("kantayhteys.php");

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
    $i = 0;
    while ($i < $num) {
        $row = mysqli_fetch_assoc($result);
        $ilmoitus_id = $row["ilmoitus_id"];
        $ilmoitus_laji = $row["ilmoitus_laji"];

        // Ilmoituksen lajia ei löytynyt
        if (false === $ilmoitus_laji) {
            echo mysqli_error($dbconnect);
        }
        if ($ilmoitus_laji == 1) {
            $ilmoitus_laji = "Myydään";
        }
        if ($ilmoitus_laji == 2) {
            $ilmoitus_laji = "Ostetaan";
        }
        $ilmoitus_nimi = $row["ilmoitus_nimi"];
        $ilmoitus_kuvaus = $row["ilmoitus_kuvaus"];
        $ilmoitus_paivays = $row["ilmoitus_paivays"];
        $ilmoitus_oikeapaivays = date("d-m-Y", strtotime($ilmoitus_paivays));
        $myyja_id = $row["myyja_id"];
        $myyja_tunnus = $row["kayttaja_tunnus"];
        $myyja_sahkoposti = $row["kayttaja_sahkoposti"];


        $poista_ilmoitus_tr = "";
        if ((isset($_SESSION["kayttaja_id"]) && $_SESSION["kayttaja_id"] == $myyja_id) ||
            (isset($_SESSION["kayttaja_taso"]) && $_SESSION["kayttaja_taso"] == "admin")) {
            $poista_ilmoitus_tr = "
            <tr>
                <td>
                    <div class='nappirivi'>
                    <form action='poistailmoitus.php' method='post'>
                        <input type='submit' value='Poista'>
                        <input type='hidden' name='poista' value='1'>
                        <input type='hidden' name='poista_id' value='$ilmoitus_id'>
                    </form>
                    <form action='muokkaailmoitus.php' method='post'>
                        <input type='submit' value='Muokkaa'>
                        <input type='hidden' name='muokkaa' value='1'>
                        <input type='hidden' name='muokkaa_id' value='$ilmoitus_id'>
                    </form>
                    </div>
                </td>
            </tr>";
        }

        echo "
            <table width='500'>
                <tr>
                    <td bgcolor='#AABBCC'>
                        <b>$ilmoitus_laji: $ilmoitus_nimi</b>
                    </td>
                </tr>
                <tr>
                    <td>$ilmoitus_kuvaus</td>
                </tr>
                <tr>
                    <td>Ilmoitus jätetty: $ilmoitus_oikeapaivays</td>
                </tr>
                <tr>
                    <td>Myyjä: $myyja_tunnus</td>
                </tr>
                <tr>
                    <td><a href='mailto:$myyja_sahkoposti'>$myyja_sahkoposti</a></td>
                </tr>
                $poista_ilmoitus_tr
            </table>
        ";

        $i++;
    }
    ?>
</body>
</html>