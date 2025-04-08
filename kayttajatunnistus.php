<?php

// Luodaan uusi sessio
$session = session_start();
// Muodostetaan yhteys tietokantaan
include("kantayhteys.php");

$RECAPTCHA_SECRET = "";

header("Content-Type: text/html; charset=utf-8");

// Tarkistetaan mistä lomakkeesta tähän sivulle on tultu
$sivu = $_POST["lomaketunnistin"];
// Tarkistetaan lomakkeesta tulevat tiedot real_escape_string funktion avulla
// Funktio lisää kenoviivat vaarallisten kirjainten eteen ettei sql injektio olisi mahdollista
$kayttaja_tunnus = mysqli_real_escape_string($dbconnect, $_POST["kayttaja_tunnus"]);
$kayttaja_salasana = mysqli_real_escape_string($dbconnect, $_POST["kayttaja_salasana"]);
// Hashataan käyttäjän salasana MD5:lla, ei turvallinen!
//$kayttaja_salasana = md5($kayttaja_salasana);

if ($DEBUG_TILA) {
    ini_set("display_errors", 1);
    ini_set("display_startup_errors", 1);
    error_reporting(E_ALL);
}

// Kutsuttu rekisteröitymislomakkeesta
if ($sivu == 0) {
    $kayttaja_sposti = $dbconnect->real_escape_string($_POST["kayttaja_sposti"]);
    // Toteutetaan salasanan hash password_hash funktion avulla, tämä myös suolaa salasanan
    $kayttaja_salasana = password_hash($kayttaja_salasana, PASSWORD_DEFAULT);

    if (
        $kayttaja_sposti == "" ||
        $kayttaja_tunnus == "" || $kayttaja_salasana == "" ||
        !isset($_POST["g-recaptcha-response"]) || $_POST["g-recaptcha-response"] == ""
        /*|| $varmistus != "kuusi"*/ //!isset($_POST["varmistus"])
    ) {
        die("Jätit tietoja täyttämättä. Ole hyvä ja <a href='rekisterointi.html'>täytä lomake uudelleen.</a>");
    }
    // Käytetään Googlen RECAPTCHA palvelua
    $recaptchResponse = $_POST["g-recaptcha-response"];
    $verifyVastaus = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $RECAPTCHA_SECRET . "&response=" . $recaptchResponse);
    // Tarkistetaan palauttiko googlen recaptcha vastauksen
    if (!$verifyVastaus) {
        die("Recaptcha tarkistus epäonnistui. Ole hyvä ja <a href='rekisterointi.html'>täytä lomake uudelleen.</a>");
    }
    $verifyVastaus = json_decode($verifyVastaus);
    // Tarkistetaan onko käyttäjä ihminen
    if (!$verifyVastaus->success) {
        die("Recaptcha tarkistus epäonnistui. Ole hyvä ja <a href='rekisterointi.html'>täytä lomake uudelleen.</a>");
    }
    
    //$varmistus = $_POST["varmistus"];
    // Luodaan mysql kysely
    //echo "INSERT INTO kayttajat (kayttaja_id, kayttaja_taso, kayttaja_tunnus, kayttaja_salasana, kayttaja_sahkoposti) VALUES(NULL, 'user', '$kayttaja_tunnus', '$kayttaja_salasana', '$kayttaja_sposti')";
    $query = mysqli_query($dbconnect, "SELECT * FROM kayttajat WHERE kayttaja_tunnus = '$kayttaja_tunnus'");
    // Jos käyttäjätunnus löy§tyi tietokannasta
    if (mysqli_num_rows($query) != 0) {
        echo "Tunnus on jo käytössä! <a href='rekisterointi.html'>Kokeile uudelleen</a>.";
    } else {
        $query = mysqli_query($dbconnect, "INSERT INTO kayttajat (kayttaja_id, kayttaja_taso, kayttaja_tunnus, kayttaja_salasana, kayttaja_sahkoposti) VALUES(NULL, 'user', '$kayttaja_tunnus', '$kayttaja_salasana', '$kayttaja_sposti')");
        echo "Rekisteröinti onnistui! <a href='kirjautuminen.html'>Kirjaudu sisään</a> palveluun.";
        mysqli_close($dbconnect);
    }
    return;
}

// Sisäänkirjautuminen
if ($sivu == 1) {
    if ($kayttaja_tunnus == "" || $kayttaja_salasana == "") {
        die("Jätit tietoja täyttämättä. Ole hyvä ja <a href='kirjautuminen.html'>täytä lomake uudelleen.</a>");
    }
        // Käytetään tässä password_verify
    $query = mysqli_query($dbconnect, "SELECT * FROM kayttajat WHERE kayttaja_tunnus = '$kayttaja_tunnus'");
    // Käyttäjätunnusta ei löydy
    if (mysqli_num_rows($query) == 0) {
        echo ("Kirjautuminen ei onnistunut. Käyttäjätunnus tai salasana väärin. <a href='kirjautuminen.html'>Kirjaudu sisään uudelleen</a>.<br>
            Jos sinulla ei ole vielä käyttäjätunnusta <a href='rekisterointi.html'>rekisteröidy tästä</a>.");
        return;
    }

    $tiedot = mysqli_fetch_array($query) or die(mysqli_error($dbconnect));
    // Tarkistetaan onko salasana oikein
    if (!password_verify($kayttaja_salasana,$tiedot["kayttaja_salasana"])) {
        echo ("Kirjautuminen ei onnistunut. Käyttäjätunnus tai salasana väärin. <a href='kirjautuminen.html'>Kirjaudu sisään uudelleen</a>.<br>
            Jos sinulla ei ole vielä käyttäjätunnusta <a href='rekisterointi.html'>rekisteröidy tästä</a>.");
        return;
    }
    // Tallennetaan käyttäjän tiedon sessioniin
    $_SESSION["kayttaja_id"] = $tiedot['kayttaja_id'];
    $_SESSION["kayttaja_taso"] = $tiedot['kayttaja_taso'];
    $_SESSION["kayttaja_tunnus"] = $tiedot['kayttaja_tunnus'];
    $_SESSION["kayttaja_salasana"] = $tiedot['kayttaja_salasana'];
    $_SESSION["kayttaja_sahkoposti"] = $tiedot['kayttaja_sahkoposti'];
    $_SESSION['LOGGEDIN'] = 1;

    // Käyttäjätunnus ja salasana oikein
    if (mysqli_num_rows($query) !== 0) {
        echo "Kirjautuminen onnistui! <br> <a href='index.php'>Siirry palveluun</a>";
    }
}
// Muutetaan käyttäjän tietoja
if ($sivu == 2) {
    $kayttaja_uusisalasana = mysqli_real_escape_string($dbconnect,$_POST["kayttaja_uusisalasana"]);
    // "Suojataan" käyttäjän salasana md5 hashilla. Ei turvallinen!
    //$kayttaja_uusisalasana = md5($kayttaja_uusisalasana);
    $kayttaja_uusisalasana = password_hash($kayttaja_uusisalasana, PASSWORD_DEFAULT);
    function vaihdaSahkoposti () {
        // Käyttäjän antama uusi sposti
        $kayttaja_uusisahkoposti = $_POST["kayttaja_uusisahkoposti"];
        global $kayttaja_tunnus;
        global $dbconnect;

        if (!empty($kayttaja_uusisahkoposti)) {
            // Varmistetaan käyttäjän syöte sql injektion varalta
            $kayttaja_uusisahkoposti = mysqli_real_escape_string($dbconnect, $kayttaja_uusisahkoposti);
            $query = mysqli_query($dbconnect, "UPDATE kayttajat SET kayttaja_sahkoposti='$kayttaja_uusisahkoposti' WHERE kayttaja_tunnus = '$kayttaja_tunnus'");
            $_SESSION["kayttaja_sahkoposti"] = $kayttaja_uusisahkoposti;
            //echo "Sähköpostiosoite päivitetty. <br>";
        }
        else {
            die("Jätit sähköposti kentän tyhjäksi. Kokeile <a href='tiedot.php'>uudestaan</a>.");
            //echo "Jätit sähköposti kentän tyhjäksi. Kokeile <a href='tiedot.php'>uudestaan</a>.";
        }
    }
    $query = mysqli_query($dbconnect, "SELECT * FROM kayttajat WHERE kayttaja_tunnus = '$kayttaja_tunnus'");

    $tiedot = mysqli_fetch_array($query) or die(mysqli_error($dbconnect));
    // muutetaan vain sähköposti
    if (empty($kayttaja_salasana)) {
        vaihdaSahkoposti();
        echo "Tietojen muutos onnistui. <br> <a href='index.php'>Palaa etusivulle</a>.";
    }
    // muutetaan sähköposti ja salasana
    else {
        $salasanaOikein = password_verify($kayttaja_salasana, $tiedot["kayttaja_salasana"]);
        if (!$salasanaOikein || empty($kayttaja_uusisalasana)) {
            echo "Syötit väärän salasanan tai jätit tietoja täyttämättä. Kokeile <a href='tiedot.php'>uudestaan</a>.";
        }
        else {
            $query = mysqli_query($dbconnect,"UPDATE kayttajat SET kayttaja_salasana='$kayttaja_uusisalasana' WHERE kayttaja_tunnus='$kayttaja_tunnus'");
            vaihdaSahkoposti();

            echo "Tietojen muutos onnistui. <br> <a href='index.php'>Palaa etusivulle</a>.";
        }
    }
}
?>