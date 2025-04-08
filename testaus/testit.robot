*** Settings ***
Resource    resources/common.resource
Force Tags  test
Suite Setup    Aloita Testi
Suite Teardown    Lopeta Testi

*** Test Cases ***
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