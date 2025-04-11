<?php
session_start();
include("kantayhteys.php");
include("kuvahallinta.php");

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
                $ilmoitus_sijainti_lev = $row["ilmoitus_sijainti_lev"];
                $ilmoitus_sijainti_pit = $row["ilmoitus_sijainti_pit"];
                $ilmoitus_kuva = $row["ilmoitus_kuva"];
                $myyja_id = $row["myyja_id"];
                $myyja_tunnus = $row["kayttaja_tunnus"];
                $myyja_sahkoposti = $row["kayttaja_sahkoposti"];
                
                $ilmoitus_kuva_tr = "";
                if (!empty($ilmoitus_kuva)) {
                    $tiedostoKuva = kuvaHae($ilmoitus_kuva);
                    if (file_exists($tiedostoKuva)) {
                        $ilmoitus_kuva_tr = "
                        <tr>
                            <td>
                                <img src='$tiedostoKuva' name='ilmoitus_kuva' class='ilmoitus_kuva'>
                            </td>
                        </tr>";
                    }
                }

                $ilmoitus_sijainti_tr = "";
                if ($ilmoitus_sijainti_lev != 0 && $ilmoitus_sijainti_pit != 0) {
                    $ilmoitus_sijainti_tr = "
                    <tr>
                        <td>
                            <a href='kartta.php?ilmoitus_id=$ilmoitus_id&lev=$ilmoitus_sijainti_lev&pit=$ilmoitus_sijainti_pit' target='_blank' name='ilmoitus_sijainti'>Katso sijainti kartalla</a>
                        </td>
                    </tr>";
                }
        
                $poista_ilmoitus_tr = "";
                if ((isset($_SESSION["kayttaja_id"]) && $_SESSION["kayttaja_id"] == $myyja_id) ||
                    (isset($_SESSION["kayttaja_taso"]) && $_SESSION["kayttaja_taso"] == "admin")) {
                    $poista_ilmoitus_tr = "
                    <tr>
                        <td>
                            <div class='nappirivi'>
                            <form action='poistailmoitus.php' method='post'>
                                <input type='submit' name='Poista $ilmoitus_nimi' value='Poista'>
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
                        $ilmoitus_kuva_tr
                        $ilmoitus_sijainti_tr
                        $poista_ilmoitus_tr
                    </table>
                ";
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
</body>
</html>