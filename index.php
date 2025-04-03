<?php
session_start();
include("kantayhteys.php");
?>

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Markkinapaikka</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <style>
        input {
            margin-bottom: 10px;
        }
        table {
            border: 1px solid rgba(0, 0, 0, 0.3);
            border-radius: 5px;
            margin-bottom: 10px;
            box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.3);
        }
        .nappirivi {
            display: flex;
            flex-wrap: nowrap;
            gap: 10px;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light  ">
                <a class="navbar-brand" href="index.php">Osto- ja myyntipalsta</a>
                <ul class="navbar-nav">
                    
        <?php 
        if (isset($_SESSION['LOGGEDIN']) && $_SESSION['LOGGEDIN'] == 1) {
            echo "
            <span class='navbar-text'>
                Tervetuloa $_SESSION[kayttaja_tunnus]!
            </span>";

            echo "
            <li class='nav-item'>
                <a class='nav-link active' href='lisaailmoitus.php'>Lisää ilmoitus</a>
            </li>
            <li class='nav-item'>
                <a class='nav-link active' href='tiedot.php'>Muuta tietojasi</a>
            </li>
            <li class='nav-item'>
                <a class='nav-link active' href='uloskirjautuminen.php'>Kirjaudu ulos</a>
            </li>";
        }
        else {
            echo "
            <li class='nav-item'>
                <a class='nav-link active' href='kirjautuminen.html'>Kirjaudu sisään</a>
            </li>
            <li class='nav-item'>
                <a class='nav-link active' href='rekisterointi.html'>Rekisteröidy palveluun</a>
            </li>";
        }?>
      </ul>
        </nav>
    </header>
        <div class="container mt-4">
        <div id="ilmoitukset" class="row mb-4 justify-content-center">
            <h3>Ilmoitukset</h3>
            <p>Hae ilmoituksia:</p><br>
            <form action='haeilmoitus.php' method='post'>
                <input name='haku' type='text'>
                <input type='submit' name='submit' value='Hae'>
            </form>
        </div>
        <div class="row">
            <?php
            // Ilmoitusten tuonti
            $query = "SELECT * FROM ilmoitukset INNER JOIN kayttajat ON ilmoitukset.myyja_id = kayttajat.kayttaja_id";
            $result = mysqli_query($dbconnect, $query);

            if (!$result) {
                printf("Error: %s\n", mysqli_error($dbconnect));
                exit();
            }

            $num = mysqli_num_rows($result);
            $i = 0;
            while ($i < $num) {
                $row = mysqli_fetch_assoc($result);
                $ilmoitus_id = $row["ilmoitus_id"];
                $ilmoitus_laji = $row["ilmoitus_laji"];

                // Ilmoituksen lajia ei löytynyt
                if (false === $ilmoitus_laji) {
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
                if ((isset($_SESSION["kayttaja_id"]) && $_SESSION["kayttaja_id"] == $myyja_id) ||
                    (isset($_SESSION["kayttaja_taso"]) && $_SESSION["kayttaja_taso"] == "admin")) {
                    $poista_ilmoitus_tr = "
                    <tr>
                        <td>
                            <div class='nappirivi'>
                            <form action='poistailmoitus.php' method='post'>
                                <input type='submit' value='Poista' class='btn btn-danger btn-sm'>
                                <input type='hidden' name='poista' value='1'>
                                <input type='hidden' name='poista_id' value='$ilmoitus_id'>
                            </form>
                            <form action='muokkaailmoitus.php' method='post'>
                                <input type='submit' value='Muokkaa' class='btn btn-primary btn-sm'>
                                <input type='hidden' name='muokkaa' value='1'>
                                <input type='hidden' name='muokkaa_id' value='$ilmoitus_id'>
                            </form>
                            </div>
                        </td>
                    </tr>";
                }

                echo "
                    <table width='500' class='container-sm'>
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
                        $poista_ilmoitus_tr
                    </table>
                ";

                $i++;
            }
            ?>
        </div>
    </div> <!-- Container -->
    <footer class="py-4 mt-4 bg-light">
        <div class="container text-center">
            <p class="text-muted">Osto- ja myyntipalsta</p>
        </div>
    </footer>
</body>
</html>