*** Settings ***
Library           SeleniumLibrary

*** Variables ***
${Kayttajatunnus}   testikayttaja
${KayttajaSalasana}  testikayttaja
${HOST}  localhost/markkina
${LOGIN URL}   http://${HOST}/kirjautuminen.html
${LOGOUT URL}   http://${HOST}/uloskirjautuminen.php

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

Aloita Testi
    Open Browser    http://localhost/markkina/    chrome
Lopeta Testi
    Close Browser