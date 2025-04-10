<?php
session_start();
include("kantayhteys.php");

if ($DEBUG_TILA) {
    ini_set("display_errors", 1);
    ini_set("display_startup_errors", 1);
    error_reporting(E_ALL);
}
if (isset($_REQUEST["ilmoitus_id"]) && !empty($_REQUEST["ilmoitus_id"]) &&
    isset($_REQUEST["lev"]) && !empty($_REQUEST["lev"]) &&
    isset($_REQUEST["pit"]) && !empty($_REQUEST["pit"])) {
    $ilmoitus_id = htmlspecialchars(string: $_REQUEST["ilmoitus_id"]);
    $ilmoitus_sijainti = [htmlspecialchars($_REQUEST["lev"]), htmlspecialchars($_REQUEST["pit"])];
    
    $query = "SELECT * FROM ilmoitukset WHERE ilmoitus_id = ?";
    $stmt = mysqli_prepare($dbconnect, $query);
    mysqli_stmt_bind_param($stmt, "i", $ilmoitus_id);
    mysqli_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $ilmoitus_id = $row["ilmoitus_id"];
        $ilmoitus_laji = $row["ilmoitus_laji"];
        $ilmoitus_kuvaus = $row["ilmoitus_kuvaus"];

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
    } 
    else {
        // Tyhjennetään ilmoitus id muuttuja, jolloin käyttäjälle näytetään virheilmoitus ettei ilmoitusta löytynyt.
        $ilmoitus_id = null;
    }
}
/*
if (isset($ilmoitus_id)) {
    echo "Ilmoitus id: ". $ilmoitus_id . "<br>";
}
if (isset($ilmoitus_sijainti)) {
    echo "Koordinaatit: " .$ilmoitus_sijainti[0] . "," . $ilmoitus_sijainti[1];
} 
*/
?>
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ilmoituksen sijainti kartalla</title>
    <!-- Leaflet CSS tyyli ja Javascript tiedosto -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
      <!-- Make sure you put this AFTER Leaflet's CSS -->
 <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>
     <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php if (isset($ilmoitus_id) && isset($ilmoitus_sijainti)): ?>
        <input type="hidden" id="ilmoitus_id" value="<?php echo $ilmoitus_id; ?>">
        <input type="hidden" id="ilmoitus_nimi" value="<?php echo $ilmoitus_laji . " " . $ilmoitus_nimi; ?>">
        <input type="hidden" id="ilmoitus_kuvaus" value="<?php echo $ilmoitus_kuvaus ?>">
        <input type="hidden" id="ilmoitus_sijainti_lev" value="<?php echo $ilmoitus_sijainti[0]; ?>">
        <input type="hidden" id="ilmoitus_sijainti_pit" value="<?php echo $ilmoitus_sijainti[1]; ?>">
        <h3 id="ilmoitusOtsikko">Ilmoituksen <?php echo "\"". $ilmoitus_laji . " " . $ilmoitus_nimi . "\""?> sijainti</h3>
        <div id="kartta"></div>
        
    <?php else: ?>
        <h3 id="ilmoitusVirheOtsikko">Ilmoitusta ei löytynyt.</h3>
    <?php endif; ?>
    <a href="index.php">Palaa etusivulle.</a>
    <script src="kartta.js"></script>
</body>
</html>