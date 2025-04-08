<?php
session_start();
include("kantayhteys.php");

if ($DEBUG_TILA) {
    ini_set("display_errors", 1);
    ini_set("display_startup_errors", 1);
    error_reporting(E_ALL);
}

header("Content-Type: text/html; charset=utf-8");

// Asetetaan aikavyöhyke Suomen aikaan
date_default_timezone_set("Europe/Helsinki");
$ilmoitus_aika = date("Y-m-d");

if (isset($_SESSION['LOGGEDIN']) && $_SESSION["LOGGEDIN"] == 1) {
    $myyja_id = $_SESSION["kayttaja_id"];
    echo "<form action='ilmoitushallinta.php' method='post'>";
    echo "<h3>Lisää ilmoitus</h3>";
    echo "
    <table>
        <tbody>
            
            <tr>
                <td><p>Ilmoitustyyppi:</p></td>
                <td>
                    <select name='ilmoitus_laji'>
                        <option value='1'>Myydään</option>
                        <option value='2'>Ostetaan</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><p>Kohteen nimi:</p></td>
                <td><input name='ilmoitus_nimi' type='text' size='50'></td>
            </tr>
            <tr>
                <td>Kohteen kuvaus:</td>
                <td><textarea name='ilmoitus_kuvaus' rows='5' cols='80'></textarea></td>
            </tr>
            <tr>
                <td><input type='submit' name='lahetaIlmoitus' value='Lähetä'></td>
            </tr>
        </tbody>
    </table>

    <input type='hidden' name='myyja_id' value='$myyja_id'>
    <input type='hidden' name='ilmoitus_paivays' value='$ilmoitus_aika'>
    <input type='hidden' name='lomaketunnistin' value='1'>
  
    </form>
    <p><a href='index.php'>Palaa edelliselle sivulle</a>.</p>";
}
else {
    echo "Et voi lisätä ilmoituksia, koska et ole kirjautunut sisään! <br>
    <a href='kirjautuminen.html'>Kirjaudu sisään</a> tai <a href='rekisterointi.html'>rekisteröi uusi tili</a>";
}
?>