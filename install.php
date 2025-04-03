<?php
// Asennustila päällä
$INSTALL_DATABASE = false;
include("kantayhteys.php");

if (!$INSTALL_DATABASE) {
    return;
}
$query = "
CREATE TABLE `ilmoitukset` (
    `ilmoitus_id` int(6) NOT NULL,
    `ilmoitus_laji` int(2) NOT NULL,
    `ilmoitus_nimi` text NOT NULL,
    `ilmoitus_kuvaus` text NOT NULL,
    `ilmoitus_paivays` date NOT NULL,
    `myyja_id` int(6) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
  CREATE TABLE `kayttajat` (
  `kayttaja_id` int(6) NOT NULL,
  `kayttaja_taso` varchar(5) NOT NULL,
  `kayttaja_tunnus` varchar(20) NOT NULL,
  `kayttaja_salasana` varchar(60) NOT NULL,
  `kayttaja_sahkoposti` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

 ALTER TABLE `ilmoitukset`
  ADD PRIMARY KEY (`ilmoitus_id`),
  ADD KEY `myyja_id` (`myyja_id`);
ALTER TABLE `kayttajat`
  ADD PRIMARY KEY (`kayttaja_id`),
  ADD UNIQUE KEY `index_kayttajatunnus` (`kayttaja_tunnus`),
  ADD UNIQUE KEY `index_kayttajasahkoposti` (`kayttaja_sahkoposti`)

  ALTER TABLE `ilmoitukset`
  MODIFY `ilmoitus_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
  ALTER TABLE `kayttajat`
  MODIFY `kayttaja_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
  ";

  if (mysqli_multi_query($dbconnect, $query)) {
    do {
        if ($result = mysqli_store_result($dbconnect)) {
            mysqli_free_result($result);
        }
    } while (mysqli_next_result($dbconnect));
    echo "Tietokanta luotiin onnistuneesti! <br> <b>Muista muuttaa INSTALL_DATABASE = false</b> asentamisen jälkeen install.php tiedostossa! tai <b>POISTA install.php tiedosto!</b>";
}
else {
    echo "Tietokannan luominen epäonnistui!";
}
?>