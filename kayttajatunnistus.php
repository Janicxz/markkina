<?php
    // Luodaan uusi sessio
    $session = session_start();
    // Muodostetaan yhteys tietokantaan
    include("kantayhteys.php");
    // Tarkistetaan mistä lomakkeesta tähän sivulle on tultu
    $sivu = $_POST["lomaketunnistin"];
    // Tarkistetaan lomakkeesta tulevat tiedot real_escape_string funktion avulla
    // Funktio lisää kenoviivat vaarallisten kirjainten eteen, kuten " '
    $kayttaja_tunnus =  mysqli_real_escape_string($dbconnect,$_POST["kayttaja_tunnus"]);
    $kayttaja_salasana = mysqli_real_escape_string($dbconnect,$_POST["kayttaja_salasana"]);
    
    // Kutsuttu rekisteröitymislomakkeesta
    if ($sivu == 0) {
        $kayttaja_sposti = $dbconnect -> real_escape_string($_POST["kayttaja_sposti"]);
        $varmistus = $_POST["varmistus"];
        if($kayttaja_sposti == "" || $varmistus != "kuusi" ||
            $kayttaja_tunnus == "" || $kayttaja_salasana == "") {
            die("Jätit tietoja täyttämättä. Ole hyvä ja <a href='rekisterointi.html'>täytä lomake uudelleen.</a>");
        }
        // Luodaan mysql kysely
        //echo "INSERT INTO kayttajat (kayttaja_id, kayttaja_taso, kayttaja_tunnus, kayttaja_salasana, kayttaja_sahkoposti) VALUES(NULL, 'user', '$kayttaja_tunnus', '$kayttaja_salasana', '$kayttaja_sposti')";
        $query = mysqli_query($dbconnect, "SELECT * FROM kayttajat WHERE kayttaja_tunnus = '$kayttaja_tunnus'");
        // Jos käyttäjätunnus löytyi tietokannasta
        if (mysqli_num_rows($query) != 0) {
            echo "Tunnus on jo käytössä! <a href='rekisterointi.html'>Kokeile uudelleen</a>.";
        } else {
            $query = mysqli_query($dbconnect,"INSERT INTO kayttajat (kayttaja_id, kayttaja_taso, kayttaja_tunnus, kayttaja_salasana, kayttaja_sahkoposti) VALUES(NULL, 'user', '$kayttaja_tunnus', '$kayttaja_salasana', '$kayttaja_sposti')");
            echo "Rekisteröinti onnistui! <a href='kirjautuminen.html'>Kirjaudu sisään</a> palveluun.";
            mysqli_close($dbconnect);
        }
    }
?>