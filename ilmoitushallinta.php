<?php
session_start();
include("kantayhteys.php");
include ("kuvahallinta.php");

function tarkistaTeksti($teksti) {
    if (empty($teksti)) return "";

    return htmlspecialchars($teksti, ENT_QUOTES, "UTF-8");
}

// DEBUG
if ($DEBUG_TILA) {
    ini_set("display_errors", 1);
    ini_set("display_startup_errors", 1);
    error_reporting(E_ALL);
}

header("Content-Type: text/html; charset=utf-8");

if (isset($_SESSION['LOGGEDIN']) && $_SESSION["LOGGEDIN"] == 1) {
    $sivu = $_POST["lomaketunnistin"];
    // Lisää ilmoitus
    if ($sivu == 1) {
        $ilmoitus_laji = tarkistaTeksti($_POST["ilmoitus_laji"]);
        $ilmoitus_nimi = tarkistaTeksti($_POST["ilmoitus_nimi"]);
        $ilmoitus_kuvaus = tarkistaTeksti($_POST["ilmoitus_kuvaus"]);
        $ilmoitus_paivays = tarkistaTeksti($_POST["ilmoitus_paivays"]);
        $ilmoitus_sijainti = tarkistaTeksti($_POST["ilmoitus_sijainti"]);
        $ilmoitus_sijainti_nayta = isset($_POST["ilmoitus_sijainti_nayta"]);
        $ilmoitus_kuva = "";
        $myyja_id = tarkistaTeksti($_POST["myyja_id"]);

        try {
            $ilmoitus_kuva = kuvaLisaa();
        } catch( Exception $ex) {
            echo "Virhe: " . $ex->getMessage() . "<br>";
            echo "Palaa <a href='index.php'>etusivulle</a>.";
            return;
        }

        // Käyttäjä ei halua sijaintia näkyviin kartalla, asetetaan 0,0 sijainniksi
        if (!$ilmoitus_sijainti_nayta) {
            $ilmoitus_sijainti = "0,0";
        }
        $ilmoitus_sijainti = explode(",", $ilmoitus_sijainti);
        if (!empty($ilmoitus_laji) && !empty($ilmoitus_nimi)
        && !empty($ilmoitus_kuvaus) && !empty($ilmoitus_paivays)
        && !empty($myyja_id) && !empty($ilmoitus_sijainti) && count($ilmoitus_sijainti) == 2) {
            $stmt = mysqli_prepare($dbconnect, "INSERT INTO ilmoitukset (ilmoitus_laji, ilmoitus_nimi, ilmoitus_kuvaus, ilmoitus_paivays, ilmoitus_sijainti_lev, ilmoitus_sijainti_pit, ilmoitus_kuva, myyja_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "isssddsi", $ilmoitus_laji, $ilmoitus_nimi, 
            $ilmoitus_kuvaus, $ilmoitus_paivays, $ilmoitus_sijainti[0], $ilmoitus_sijainti[1], 
            $ilmoitus_kuva, $myyja_id);
            mysqli_execute($stmt);
            echo "Ilmoituksen lisääminen onnistui! Palaa <a href='index.php'>etusivulle</a>.";
            //echo "lisättiin sijainti " . $ilmoitus_sijainti[0] . ", " . $ilmoitus_sijainti[1];
        }
        else {
            echo "Jätit tietoja täyttämättä. Ole hyvä ja <a href='lisaailmoitus.php'>täytä lomake uudestaan</a>.";
        }
    }
    // Muokkaa ilmoitusta
    if ($sivu == 2) {
        $ilmoitus_uusilaji = tarkistaTeksti($_POST["ilmoitus_uusilaji"]);
        $ilmoitus_uusinimi = tarkistaTeksti($_POST["ilmoitus_uusinimi"]);
        $ilmoitus_uusikuvaus = tarkistaTeksti($_POST["ilmoitus_uusikuvaus"]);
        $ilmoitus_id = (int)$_POST["ilmoitus_id"];

        if (!empty($ilmoitus_uusilaji) && !empty($ilmoitus_uusinimi)
        && !empty($ilmoitus_uusikuvaus) && !empty($ilmoitus_id)) {

            // Haetaan muokattavan ilmoituksen tiedot
            $stmt = mysqli_prepare($dbconnect, "SELECT kayttajat.kayttaja_id FROM ilmoitukset INNER JOIN kayttajat ON ilmoitukset.myyja_id = kayttajat.kayttaja_id  WHERE ilmoitus_id = ?");
            mysqli_stmt_bind_param($stmt, "i", $ilmoitus_id);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            // Tarkistetaan ollaanko muokkaamassa omaa ilmoitusta, tai onko muokkaaja admin
            if ($row["kayttaja_id"] == $_SESSION["kayttaja_id"] || $_SESSION["kayttaja_taso"] == "admin") {
                $stmt = mysqli_prepare($dbconnect, "UPDATE ilmoitukset SET ilmoitus_laji = ?, ilmoitus_nimi = ?, ilmoitus_kuvaus = ? WHERE ilmoitus_id = ?");
                mysqli_stmt_bind_param($stmt, "issi", $ilmoitus_uusilaji, $ilmoitus_uusinimi, $ilmoitus_uusikuvaus, $ilmoitus_id);
                mysqli_execute($stmt);
                //mysqli_query($dbconnect, "INSERT INTO ilmoitukset (ilmoitus_laji, ilmoitus_nimi, ilmoitus_kuvaus, ilmoitus_paivays, myyja_id)
                //VALUES ('$ilmoitus_laji', '$ilmoitus_nimi', '$ilmoitus_kuvaus', '$ilmoitus_paivays', '$myyja_id')");
                echo "Ilmoituksen muokkaaminen onnistui! Palaa <a href='index.php'>etusivulle</a>.";
            }
            else {
                echo "Ilmoituksen muokkaaminen epäonnistui! Palaa <a href='index.php'>etusivulle</a>.";
            }
        }
        else {
            echo "Jätit tietoja täyttämättä. Ole hyvä ja <a href='lisaailmoitus.php'>täytä lomake uudestaan</a>.";
        }
    }
}
else {
    echo "Et voi lisätä ilmoituksia, koska et ole kirjautunut sisään! <br>
    <a href='kirjautuminen.html'>Kirjaudu sisään</a> tai <a href='rekisterointi.html'>rekisteröi uusi tili</a>";
}
?>