<?php
session_start();
include("kantayhteys.php");

ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

header("Content-Type: text/html; charset=utf-8");

if (isset($_SESSION['LOGGEDIN']) && $_SESSION["LOGGEDIN"] == 1) {
    $sivu = $_POST["lomaketunnistin"];
    // Lisää ilmoitus
    if ($sivu == 1) {
        $ilmoitus_laji = $_POST["ilmoitus_laji"];
        $ilmoitus_nimi = $_POST["ilmoitus_nimi"];
        $ilmoitus_kuvaus = $_POST["ilmoitus_kuvaus"];
        $ilmoitus_paivays = $_POST["ilmoitus_paivays"];
        $myyja_id = $_POST["myyja_id"];

        if (!empty($ilmoitus_laji) && !empty($ilmoitus_nimi)
        && !empty($ilmoitus_kuvaus) && !empty($ilmoitus_paivays) && !empty($myyja_id)) {
            $stmt = mysqli_prepare($dbconnect, "INSERT INTO ilmoitukset (ilmoitus_laji, ilmoitus_nimi, ilmoitus_kuvaus, ilmoitus_paivays, myyja_id)
            VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "isssi", $ilmoitus_laji, $ilmoitus_nimi, $ilmoitus_kuvaus, $ilmoitus_paivays, $myyja_id);
            mysqli_execute($stmt);
            //mysqli_query($dbconnect, "INSERT INTO ilmoitukset (ilmoitus_laji, ilmoitus_nimi, ilmoitus_kuvaus, ilmoitus_paivays, myyja_id)
            //VALUES ('$ilmoitus_laji', '$ilmoitus_nimi', '$ilmoitus_kuvaus', '$ilmoitus_paivays', '$myyja_id')");
            echo "Ilmoituksen lisääminen onnistui! Palaa <a href='index.php'>etusivulle</a>.";
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