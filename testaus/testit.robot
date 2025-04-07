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