<?php
function luoIlmoitusTable($row, $i) {
    $uusi_table = "";
    $ilmoitus_id = $row["ilmoitus_id"];
    $ilmoitus_laji = $row["ilmoitus_laji"];

    // Ilmoituksen lajia ei löytynyt
    if (false === $ilmoitus_laji) {
        echo "Ilmoituksen lajia ei löytynyt!";
        //echo mysqli_error($dbconnect);
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
                    <img src='$tiedostoKuva' name='ilmoitus_kuva' class='ilmoitus_kuva' id='kuva_$i' onClick='kuvaClick(this);'>
                </td>
            </tr>";
        }
    }

    $ilmoitus_sijainti_tr = "";
    if ($ilmoitus_sijainti_lev != 0 && $ilmoitus_sijainti_pit != 0) {
        $ilmoitus_sijainti_tr = "
        <tr>
            <td>
                <a href='kartta.php?ilmoitus_id=$ilmoitus_id&lev=$ilmoitus_sijainti_lev&pit=$ilmoitus_sijainti_pit' target='_blank'>Katso sijainti kartalla</a>
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
                    <input type='submit' value='Poista'>
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

    $uusi_table .= "
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
            $ilmoitus_sijainti_tr
            $ilmoitus_kuva_tr
            $poista_ilmoitus_tr
        </table>
    ";
    return $uusi_table;
}
?>