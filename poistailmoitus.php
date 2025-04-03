<?php
session_start();
include("kantayhteys.php");

// DEBUG
/*
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);
*/
header("Content-Type: text/html; charset=utf-8");

if (isset($_POST["poista"])) {
    $poista = $_POST["poista"];
}
else {
    echo "Ilmoitusta ei voitu poistaa!  <a href='index.php'>Palaa etusivulle</a>.";
    return;
}
$ilmoitus_id = $_POST["poista_id"];

// Tarkistetaan onko käyttäjä kirjautunut sisään
if (!isset($_SESSION["kayttaja_salasana"])) {
    echo "Et ole kirjautunut sisään. <a href='index.php'>Palaa etusivulle</a>.";
    return;
}

if (isset($poista) && isset($ilmoitus_id)) {
    // Haetaan poistettavan ilmoituksen tiedot
    $stmt = mysqli_prepare($dbconnect, "SELECT kayttajat.kayttaja_id FROM ilmoitukset INNER JOIN kayttajat ON ilmoitukset.myyja_id = kayttajat.kayttaja_id  WHERE ilmoitus_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $ilmoitus_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    // Tarkistetaan ollaanko poistamassa omaa ilmoitusta, tai onko poistaja admin
    if ($row["kayttaja_id"] == $_SESSION["kayttaja_id"] || $_SESSION["kayttaja_taso"] == "admin") {

        $stmt = mysqli_prepare($dbconnect, "DELETE FROM ilmoitukset WHERE ilmoitus_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $ilmoitus_id);
        mysqli_execute($stmt);
    
        //$query = mysqli_query($dbconnect, "DELETE FROM ilmoitukset WHERE ilmoitus_id = $ilmoitus_id");
        echo "Ilmoitus poistettu! <a href='index.php'>Palaa etusivulle</a>.";
    }
    else {
        echo "Ilmoitusta ei voitu poistaa! <a href='index.php'>Palaa etusivulle</a>.";
    }
}
?>