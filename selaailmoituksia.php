<?php
session_start();
include("kantayhteys.php");

ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

// Jos sivunumeroa ei ole pyydetty, asetetaan se oletusarvona 1
$sivuNumero = (!isset($_GET["sivu"])) ? 1: (int)$_GET["sivu"];
/*if (!isset($_GET["sivu"])) {
    $sivuNumero = 1;
}
else {
    $sivuNumero = $_GET["sivu"];
}*/
// Kuinka monta ilmoitusta sivulla näytetään
$ilmoituksiaSivulla = 5;
$hakuAlku = ($sivuNumero-1) * $ilmoituksiaSivulla;

$query = "SELECT COUNT(*) as yhteensa FROM ilmoitukset";
$result = mysqli_fetch_assoc(mysqli_query($dbconnect, $query));
$sivujaYhteensa = ceil($result["yhteensa"] / $ilmoituksiaSivulla);
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
    <div id="ilmoitukset">
        <h2>Ilmoitukset:</h2>
        <?php
                
            // Ilmoitusten tuonti
            $query = "SELECT * FROM ilmoitukset INNER JOIN kayttajat ON ilmoitukset.myyja_id = kayttajat.kayttaja_id LIMIT $hakuAlku, $ilmoituksiaSivulla";
            $result = mysqli_query($dbconnect, $query);
            
            // Lisätään kaikki ilmoitukset
            while ($row = mysqli_fetch_assoc($result)) {
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
            }
            echo "<br>";
            // Sivujen navigointi
            if ($sivuNumero != 1) {
                $edellinenSivu = $sivuNumero-1;
                echo "<a href='selaailmoituksia.php?sivu=1'> << </> ";
                echo "<a href='selaailmoituksia.php?sivu=$edellinenSivu'> < </> ";
            }
            echo "<a href='selaailmoituksia.php?sivu=$sivuNumero'>Sivu $sivuNumero</> "; 
            if ($sivuNumero != $sivujaYhteensa) {
                $seuraavaSivu = $sivuNumero+1;
                echo "<a href='selaailmoituksia.php?sivu=$seuraavaSivu'> > </> ";
                echo "<a href='selaailmoituksia.php?sivu=$sivujaYhteensa'> >> </> ";
            }
            echo "<br> Palaa takaisin <a href='index.php'>etusivulle</a>.";
        ?>
    </div>
</body>
</html>