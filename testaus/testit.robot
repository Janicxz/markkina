*** Settings ***
Metadata    Tekijä    Jani Luostarinen
Resource    resources/common.resource
Force Tags    test
Suite Setup    Aloita Testi
Suite Teardown    Lopeta Testi

*** Test Cases ***
# Testataan kirjautumiseen liittyvät toiminnot
Kirjautuminen Onnistuu Tunnuksilla
    Kirjaudu sisään    ${Kayttajatunnus}    ${KayttajaSalasana}
Kirjautuminen Ei Onnistu väärillä tunnuksilla
    Kirjaudu sisään    olematonTunnus    olematonSalasana
    Page Should Contain    Kirjautuminen ei onnistunut.
Kirjautuminen Ei Onnistu Tyhjillä Kentillä
    Kirjaudu Sisään
    Page Should Contain    Jätit tietoja täyttämättä.

Uloskirjautuminen Toimii
    Kirjaudu sisään    ${Kayttajatunnus}    ${KayttajaSalasana}
    Kirjaudu Ulos
    Page Should Contain    Uloskirjautuminen onnistui!

Uloskirjautuminen Ei Tapahdu Ilman Sisäänkirjautumista
    Kirjaudu Ulos
    Page Should Contain    Et ole kirjautunut sisään.
# Testataan haku toiminnot
Haku Toimii
    Hae Ilmoitus   Testi
Haku tyhjennys Toimii
    Hae Ilmoitus    Testi
    Click link    link=Tyhjennä haku
    Page Should Not Contain    Hakusanallesi "testi" löytyi ilmoituksia
Hae Omat Ilmoitukset Toimii
    Kirjaudu Sisään   ${Kayttajatunnus}    ${KayttajaSalasana}
    Hae Omat Ilmoitukset
    Page Should Contain    Omat ilmoitukset:
# Testataan ilmoituksen lisäys ja poistaminen
Lisää Myydään Ilmoitus Toimii
    Kirjaudu Sisään   ${Kayttajatunnus}    ${KayttajaSalasana}
    Lisää Myydään Ilmoitus  ilmoitusMyydäänNimi  ilmoitusSeloste
    Wait Until Page Contains    Ilmoituksen lisääminen onnistui!
    Page Should Contain    Ilmoituksen lisääminen onnistui!
Lisää Ostetaan Ilmoitus Toimii
    Kirjaudu Sisään   ${Kayttajatunnus}    ${KayttajaSalasana}
    Lisää Ostetaan Ilmoitus  ilmoitusOstetaanNimi  ilmoitusSeloste Ilmoitus
    Wait Until Page Contains    Ilmoituksen lisääminen onnistui!
    Page Should Contain    Ilmoituksen lisääminen onnistui!
Lisää Ilmoitus Sijainnilla Toimii
    Kirjaudu Sisään   ${Kayttajatunnus}    ${KayttajaSalasana}
    Lisaa Ilmoitus Sijainnilla  ilmoitusSijaintiNimi  ilmoitusSeloste
    Wait Until Page Contains    Ilmoituksen lisääminen onnistui!
    Page Should Contain    Ilmoituksen lisääminen onnistui!
    Poista Ilmoitus  ilmoitusSijaintiNimi
Poista Myydään Ilmoitus Toimii
    Kirjaudu Sisään   ${Kayttajatunnus}    ${KayttajaSalasana}
    Poista Ilmoitus  ilmoitusMyydäänNimi
    Wait Until Page Contains    Ilmoitus poistettu!
    Page Should Contain    Ilmoitus poistettu!
Poista Ostetaan Ilmoitus Toimii
    Kirjaudu Sisään   ${Kayttajatunnus}    ${KayttajaSalasana}
    Poista Ilmoitus  ilmoitusOstetaanNimi
    Wait Until Page Contains    Ilmoitus poistettu!
    Page Should Contain    Ilmoitus poistettu!
Poista Ilmoitus Ei Toimi Ilman Kirjautumista
    Go To    ${POISTAILMOITUS URL}
    Wait Until Page Contains    Ilmoitusta ei voitu poistaa!
    Page Should Contain    Ilmoitusta ei voitu poistaa!
Näytä Ilmoitus Kartalla Toimii
    # Lisätään ilmoitus jossa on sijainti
    Kirjaudu Sisään   ${Kayttajatunnus}    ${KayttajaSalasana}
    Lisaa Ilmoitus Sijainnilla  ilmoitusSijaintiNimi  ilmoitusSeloste
    Wait Until Page Contains    Ilmoituksen lisääminen onnistui!
    Page Should Contain    Ilmoituksen lisääminen onnistui!
    Hae Omat Ilmoitukset
    Click link    name=ilmoitus_sijainti
    Switch Window    title=Ilmoituksen sijainti kartalla
    Wait Until Page Contains Element    ilmoitusOtsikko
    Poista Ilmoitus  ilmoitusSijaintiNimi

Näytä Ilmoitus Kartalla Ei Toimi Ilman Syotteita
    Nayta Ilmoitus Kartalla
    Wait Until Page contains Element    ilmoitusVirheOtsikko