*** Settings ***
Library           SeleniumLibrary

*** Variables ***
${Kayttajatunnus}   testikayttaja
${KayttajaSalasana}  testikayttaja
${HOST}  localhost/markkina
${LOGIN URL}   http://${HOST}/kirjautuminen.html
${LOGOUT URL}   http://${HOST}/uloskirjautuminen.php
${HAKU URL}    http://${HOST}/haeilmoitus.php
${INDEX URL}    http://${HOST}/index.php

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
    [Arguments]   ${hakusana}
    Go To  ${HAKU URL}
    Input text  name=haku  ${hakusana}
    Click button  name=submit
    Page Should Contain  Hakusanallesi "${hakusana}" löytyi ilmoituksia:
Hae Omat Ilmoitukset
    [Documentation]    Hakee omat ilmoitukset
    Go To    ${INDEX URL}
    Click link    link=Omat ilmoitukset


Aloita Testi
    Open Browser    http://localhost/markkina/    chrome
Lopeta Testi
    Close Browser