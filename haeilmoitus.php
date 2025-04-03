<?php
session_start();
include("kantayhteys.php");

ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

header("Content-Type: text/html; charset=utf-8");

// Asetetaan aikavyöhyke Suomen aikaan
date_default_timezone_set("Europe/Helsinki");

if (isset($_POST["haku"])) {
    $haku = mysqli_real_escape_string($dbconnect, $_POST["haku"]);
}
echo "<h3>Haun tulokset:</h3><br>
        <p>
            <form action='haeilmoitus.php' method='post'>
                <input name='haku' type='text'>
                <input type='submit' name='submit' value='Hae'>
            </form>
        </p>";

if (isset($_POST["submit"]) && (!empty($haku))) {
    $query=("SELECT * FROM ilmoitukset INNER JOIN kayttajat ON
    ilmoitukset.myyja_id=kayttajat.kayttaja_id WHERE ilmoitus_kuvaus LIKE '%".$haku."%' OR
    ilmoitus_nimi LIKE '%".$haku."%'");
    
    $result = mysqli_query($dbconnect, $query);
    $num = mysqli_num_rows($result);

    if ($num == 0) {
        echo "Hakusanallesi <b>\"$haku\"</b> ei löytynyt ilmoituksia.";
    }
    else {
        echo "Hakusanallesi <b>\"$haku\"</b> löytyi ilmoituksia: <br>";
        $i = 0;
        while ($i < $num) {
            $row = mysqli_fetch_assoc($result);

            $ilmoitus_id = $row["ilmoitus_id"];
            $ilmoitus_laji = $row["ilmoitus_laji"];

            if (false == $ilmoitus_laji) {
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
            if (isset($_SESSION["kayttaja_id"]) && $_SESSION["kayttaja_id"] == $myyja_id ||
                isset($_SESSION["kayttaja_taso"]) && $_SESSION["kayttaja_taso"] == "admin") {
                $poista_ilmoitus_tr = "
                <tr>
                    <td>
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
                    </td>
                </tr>";
            }

            echo "
            <p>
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
                    $poista_ilmoitus_tr
                </table>
            </p>
            ";

            $i++;
        }
    }
}
else {
    echo "Syötä hakusana yllä olevaan kenttään";
}
echo "<br>(<a href='haeilmoitus.php'>Tyhjennä haku</a>)-(<a href='index.php'>Palaa etusivulle</a>)";
?>