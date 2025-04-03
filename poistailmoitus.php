<?php
session_start();
include("kantayhteys.php");

ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

header("Content-Type: text/html; charset=utf-8");

if (isset($_POST["poista"])) {
    $poista = $_POST["poista"];
}
$ilmoitus_id = $_POST["poista_id"];

if (isset($poista) && isset($ilmoitus_id)) {
    $stmt = mysqli_prepare($dbconnect, "DELETE FROM ilmoitukset WHERE ilmoitus_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $ilmoitus_id);
    mysqli_execute($stmt);

    //$query = mysqli_query($dbconnect, "DELETE FROM ilmoitukset WHERE ilmoitus_id = $ilmoitus_id");
    echo "Ilmoitus poistettu! <a href='index.php'>Palaa etusivulle</a>.";
}
?>