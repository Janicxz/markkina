<?php
// DEBUG
if ($DEBUG_TILA) {
    ini_set("display_errors", 1);
    ini_set("display_startup_errors", 1);
    error_reporting(E_ALL);
}
$KANSIO = "kuvat/";
function kuvaHae($tiedostoNimi) {
    global $KANSIO;
    return $KANSIO . $tiedostoNimi;
}
function kuvaPoista($tiedostoNimi) {
    if (empty($tiedostoNimi)) {
        return;
    }
    global $KANSIO;
    $tiedosto = $KANSIO . $tiedostoNimi;
    if (file_exists($tiedosto)) {
         unlink($tiedosto);
    }
    else {
        throw new Exception("Poistettavaa tiedostoa ei löytynyt!");
    }
}
function kuvaLisaa() {
    global $KANSIO;
    // Tarkistetaan onko käyttäjä lähettänyt tiedoston
    if (!isset($_FILES["ilmoitus_kuva"]) || empty($_FILES["ilmoitus_kuva"]) || !file_exists($_FILES["ilmoitus_kuva"]["tmp_name"])) {
        echo "Kuvaa ei lisätty ilmoitukseen <br>";
        return;
    }

    //$kansio = "kuvat/";
    $tiedostoNimi = basename( uniqid());
    $kuvaTyyppi = strtolower(pathinfo($_FILES["ilmoitus_kuva"]["name"], PATHINFO_EXTENSION));
    $tiedostoNimi .= "." . $kuvaTyyppi;
    $tiedostoOnKuva = false;
    $kuvaOk = getimagesize($_FILES["ilmoitus_kuva"]["tmp_name"]);
    // Tarkistetaan onko tiedosto kuva
    if ($kuvaOk !== false) {
        $tiedostoOnKuva = true;
    }
    else {
        throw new Exception("Lähetetty tiedosto ei ole kuva.");
    }
    // Tiedoston koko on yli 5mb
    if ($_FILES["ilmoitus_kuva"]["size"] > 5*1000000) {
        throw new Exception("Kuvan koko on yli sallitun rajan. (5mb)");
    }
    if ($kuvaTyyppi != "jpg" && $kuvaTyyppi != "png" && $kuvaTyyppi != "webp") {
        $tiedostoOnKuva = false;
        throw new Exception("Kuvan tiedostotyyppi ei ole sallittu! Vain jpg, png tai webp tiedostotyypit on tuettu.");
    }

    if ($tiedostoOnKuva) {
        if (move_uploaded_file($_FILES["ilmoitus_kuva"]["tmp_name"],  $KANSIO . $tiedostoNimi)) {
            echo "Kuva " . htmlspecialchars(basename($_FILES["ilmoitus_kuva"]["name"])) . " lisätty ilmoitukseen <br>";
            return $tiedostoNimi;
        }
    }
}
?>