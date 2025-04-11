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


// Jos sivunumeroa ei ole pyydetty, asetetaan se oletusarvona 1
$sivuNumero = (!isset($_GET["sivu"])) ? 1: (int)$_GET["sivu"];
$naytaVainOmat =(isset($_GET["naytaomat"]) && $_GET["naytaomat"] == 1) ? true : false;

// Kuinka monta ilmoitusta sivulla näytetään
$ilmoituksiaSivulla = 5;

$query = "SELECT COUNT(*) as yhteensa FROM ilmoitukset";
$result = mysqli_fetch_assoc(mysqli_query($dbconnect, $query));
$sivujaYhteensa = ceil($result["yhteensa"] / $ilmoituksiaSivulla);
// Jos pyydetty sivu on suurempi kuin sivuja yhteensä, rajataan se viimeiseen sivuun
if ($sivuNumero > $sivujaYhteensa) {
    $sivuNumero = $sivujaYhteensa;
}
// Jos pyydettiin alempaa sivua kuin 1, asetetaan näytettävä sivu 1.
if ($sivuNumero < 1) {
    $sivuNumero = 1;
}
// Käyttäjä ei ole kirjautunut sisään, ei ole omia näytettäviä ilmoituksia
if (!isset($_SESSION["LOGGEDIN"]) || $_SESSION["LOGGEDIN"] != 1) {
    $naytaVainOmat = false;
}
$hakuAlku = ($sivuNumero-1) * $ilmoituksiaSivulla;
?>

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selaa ilmoituksia</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
    <br> (<a href='index.php'>Palaa etusivulle</a>).
    <div id="ilmoitukset"> 
        <h2>
            <?php
            if ($naytaVainOmat) {
                echo "Omat ilmoitukset:";
            }
            else {
                echo "Ilmoitukset:";
            }
            ?>
          </h2>
        <?php
            
            // Ilmoitusten tuonti
            $query = "SELECT * FROM ilmoitukset INNER JOIN kayttajat ON ilmoitukset.myyja_id = kayttajat.kayttaja_id";
            // Näytetäänkö vain omat ilmoitukset?
            if ($naytaVainOmat) {
                $query .= " WHERE ilmoitukset.myyja_id = $_SESSION[kayttaja_id]"; 
            }
            // Jos selataan kaikkia ilmoituksia, rajoitetaan tulosten määrää per sivu
            else {
            $query .= " ORDER BY ilmoitukset.ilmoitus_id DESC LIMIT $hakuAlku, $ilmoituksiaSivulla";
            }
            $result = mysqli_query($dbconnect, $query);
            
            // Lisätään kaikki ilmoitukset
            $i = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                echo luoIlmoitusTable($row, $i);
                $i++;
            }
            echo "<br>";
            // Sivujen navigointi painikkeet
            if ($sivuNumero != 1) {
                $edellinenSivu = $sivuNumero-1;
                echo "<a href='selaailmoituksia.php?sivu=1'> << </a> ";
                echo "<a href='selaailmoituksia.php?sivu=$edellinenSivu'> < </a> ";
            }
            echo "<a href='selaailmoituksia.php?sivu=$sivuNumero'>Sivu $sivuNumero</a> ";
            if ($sivuNumero != $sivujaYhteensa) {
                $seuraavaSivu = $sivuNumero+1;
                echo "<a href='selaailmoituksia.php?sivu=$seuraavaSivu'> > </a> ";
                echo "<a href='selaailmoituksia.php?sivu=$sivujaYhteensa'> >> </a> ";
            }
            echo "
            <br>
            <form action='selaailmoituksia.php' method='get'>
                <label for='sivu'>Sivunumero:</label>
                <input type='number' name='sivu' id='sivu' min='1' max='$sivujaYhteensa' value='$sivuNumero'>
                <input type='submit' value='Siirry'>
            </form>";
        ?>
    </div>
    <script src="index.js"></script>
</body>
</html>