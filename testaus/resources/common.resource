*** Settings ***
# Käytetään SeleniumLibraryä testien ajamisessa
Library           SeleniumLibrary

*** Variables ***
# Muuttujat testeille
${Kayttajatunnus}   testikayttaja
${KayttajaSalasana}  testikayttaja
${HOST}  localhost/markkina
${LOGIN URL}   http://${HOST}/kirjautuminen.html
${LOGOUT URL}   http://${HOST}/uloskirjautuminen.php
${HAKU URL}    http://${HOST}/haeilmoitus.php
${INDEX URL}    http://${HOST}/index.php
${LISAAILMOITUS URL}    http://${HOST}/lisaailmoitus.php
${POISTAILMOITUS URL}   http://${HOST}/poistailmoitus.php
${OMATILMOITUKSET URL}    http://${HOST}/selaailmoituksia.php?naytaomat=1
${KARTTA URL}    http://${HOST}/kartta.php

*** Keywords ***
Kirjaudu sisään
    [Documentation]   Kirjautuu sisään käyttäjätunnuksella ja salasanalla
    [Arguments]   ${tunnus}=   ${salasana}=
    Go To   ${LOGIN URL}
    Input Text  name=kayttaja_tunnus  ${tunnus}
    Input Text  name=kayttaja_salasana  ${salasana}
    Click Button   name=kirjauduSisaan
Kirjaudu Ulos
    [Documentation]     Kirjautuu ulos palvelusta
    Go To  ${LOGOUT URL}

Hae Ilmoitus
    [Documentation]   Hakee annetulla hakusanalla ilmoituksia
    [Arguments]   ${hakusana}= 
    Go To  ${HAKU URL}
    Input text  name=haku  ${hakusana}
    Click button  name=submit
    Page Should Contain  Hakusanallesi "${hakusana}" löytyi ilmoituksia:
Hae Omat Ilmoitukset
    [Documentation]    Hakee omat ilmoitukset
    Go To    ${INDEX URL}
    Click link    link=Omat ilmoitukset
Lisää Myydään Ilmoitus
    [Documentation]    Lisää ilmoituksen annetuilla argumenteilla
    [Arguments]    ${ilmoitusNimi}=    ${ilmoitusSeloste}= 
    Go To    ${LISAAILMOITUS URL}
    Input Text    name=ilmoitus_nimi   ${ilmoitusNimi}
    Input Text    name=ilmoitus_kuvaus   ${ilmoitusSeloste}
    Click button    name=lahetaIlmoitus

Lisää Ostetaan Ilmoitus
    [Documentation]    Lisää ilmoituksen annetuilla argumenteilla
    [Arguments]    ${ilmoitusNimi}=    ${ilmoitusSeloste}=
    Go To    ${LISAAILMOITUS URL}
    Input Text    name=ilmoitus_nimi   ${ilmoitusNimi}
    Input Text    name=ilmoitus_kuvaus   ${ilmoitusSeloste}
    Select From List By Value    name=ilmoitus_laji    2
    Click button    name=lahetaIlmoitus
Lisaa Ilmoitus Sijainnilla
    [Documentation]    Lisää ilmoituksen annetuilla argumenteilla
    [Arguments]    ${ilmoitusNimi}=    ${ilmoitusSeloste}=
    Go To    ${LISAAILMOITUS URL}
    Input Text    name=ilmoitus_nimi   ${ilmoitusNimi}
    Input Text    name=ilmoitus_kuvaus   ${ilmoitusSeloste}
    Select Checkbox   name=ilmoitus_sijainti_nayta
    Click Element At Coordinates    id=kartta   10   10
    Click button    name=lahetaIlmoitus
Poista Ilmoitus
    [Documentation]    Poistaa valitun ilmoituksen
    [Arguments]    ${poistettavIlmoitus}
    Go To    ${OMATILMOITUKSET URL}
    Click button    name=Poista ${poistettavIlmoitus}
Nayta Ilmoitus Kartalla
    [Documentation]    Näyttää ilmoituksen joka on annettu argumenttina
    [Arguments]    ${ilmoitusId}=   ${ilmoitusSijaintiLev}=   ${ilmoitusSijaintiPit}= 
    Go To    ${KARTTA URL}?ilmoitus_id=${ilmoitusId}&lev=${ilmoitusSijaintiLev}&pit=${ilmoitusSijaintiPit}
Aloita Testi
    Open Browser    http://${HOST}    chrome
Lopeta Testi
    Close Browser