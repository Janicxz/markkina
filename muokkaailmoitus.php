<?php
session_start();
include("kantayhteys.php");
// DEBUG
if ($DEBUG_TILA) {
    ini_set("display_errors", 1);
    ini_set("display_startup_errors", 1);
    error_reporting(E_ALL);
}
header("Content-Type: text/html; charset=utf-8");

$ilmoitus_id = $_POST["muokkaa_id"];


if (!isset($ilmoitus_id)) {
    echo "Muokattavan ilmoituksen id uupuu!";
    return;
}

$stmt = mysqli_prepare($dbconnect, "SELECT * FROM ilmoitukset WHERE ilmoitus_id = ?");
mysqli_stmt_bind_param($stmt, "i", $ilmoitus_id);
mysqli_execute($stmt);

$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
$ilmoitus_laji = $row["ilmoitus_laji"];

if (false == $ilmoitus_laji) {
    echo mysqli_error($dbconnect);
}
$ilmoitus_myydaan_tr = "<option value='1'>Myydään</option>";
$ilmoitus_ostetaan_tr = "<option value='2'>Ostetaan</option>";
if ($ilmoitus_laji == 1) {
    $ilmoitus_laji = "Myydään";
    $ilmoitus_myydaan_tr = "<option value='1' selected>Myydään</option>";
}
if ($ilmoitus_laji == 2) {
    $ilmoitus_laji = "Ostetaan";
    $ilmoitus_ostetaan_tr = "<option value='2' selected>Ostetaan</option>";
}
$ilmoitus_nimi = $row["ilmoitus_nimi"];
$ilmoitus_kuvaus = $row["ilmoitus_kuvaus"];
$ilmoitus_paivays = $row["ilmoitus_paivays"];
$ilmoitus_oikeapaivays = date("d-m-Y", strtotime($ilmoitus_paivays));

echo "<form action='ilmoitushallinta.php' method='post'>";
echo "<h3>Muokkaa ilmoitusta</h3>";
echo "
<table>
    <tbody>
        
        <tr>
            <td><p>Ilmoitustyyppi:</p></td>
            <td>
                <select name='ilmoitus_uusilaji'>
                    $ilmoitus_myydaan_tr
                    $ilmoitus_ostetaan_tr
                </select>
            </td>
        </tr>
        <tr>
            <td><p>Kohteen nimi:</p></td>
            <td><input name='ilmoitus_uusinimi' type='text' size='50' value='$ilmoitus_nimi'></td>
        </tr>
        <tr>
            <td>Kohteen kuvaus:</td>
            <td><textarea name='ilmoitus_uusikuvaus' rows='5' cols='80'>$ilmoitus_kuvaus</textarea></td>
        </tr>
        <tr>
            <td><input type='submit' value='Lähetä'></td>
        </tr>
    </tbody>
</table>

<input type='hidden' name='lomaketunnistin' value='2'>
<input type='hidden' name='ilmoitus_id' value='$ilmoitus_id'>

</form>
<p><a href='index.php'>Palaa etusivulle sivulle</a>.</p>";
?>