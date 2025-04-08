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
    Page Should Contain    Ilmoituksen lisääminen onnistui!
Lisää Ostetaan Ilmoitus Toimii
    Kirjaudu Sisään   ${Kayttajatunnus}    ${KayttajaSalasana}
    Lisää Ostetaan Ilmoitus  ilmoitusOstetaanNimi  ilmoitusSeloste Ilmoitus
    Page Should Contain    Ilmoituksen lisääminen onnistui!
Poista Myydään Ilmoitus Toimii
    Kirjaudu Sisään   ${Kayttajatunnus}    ${KayttajaSalasana}
    Poista Ilmoitus  ilmoitusMyydäänNimi
    Page Should Contain    Ilmoitus poistettu!
Poista Ostetaan Ilmoitus Toimii
    Kirjaudu Sisään   ${Kayttajatunnus}    ${KayttajaSalasana}
    Poista Ilmoitus  ilmoitusOstetaanNimi
    Page Should Contain    Ilmoitus poistettu!
Poista Ilmoitus Ei Toimi Ilman Kirjautumista
    Go To    ${POISTAILMOITUS URL}
    Page Should Contain    Ilmoitusta ei voitu poistaa!