// Keskittää kartan käyttäjän sijaintiin jos sijaintitiedot on saatavilla
const haeSijainti = () => {
    if (navigator.geolocation && kartta !== null) {
        navigator.geolocation.getCurrentPosition((pos) => {
            ilmoituksenSijainti = [pos.coords.latitude, pos.coords.longitude];
            kartta.setView(ilmoituksenSijainti, 10);
            karttaMarker.setLatLng(ilmoituksenSijainti);

            document.getElementById("ilmoitusSijainti").value = ilmoituksenSijainti;
        });
    }
}

let kartta = null;
let karttaMarker = null;
let ilmoituksenSijainti = [60.23, 24.84];
const sivuLatautunut = () => {
    console.log("Aukaistaan karttaa");
    if (document.getElementById('kartta') === null) {
        console.log("Kartta elementtiä ei löytynyt, käyttäjä ei ole kirjautunut sisään?");
        return;
    }
    // Oletusasetukset kartalle, sijainti Helsinki, zoom taso 10
    var mapOptions = {
        center: ilmoituksenSijainti,
        zoom: 10
     }
     kartta = new L.map('kartta', mapOptions);
     // Asetetaan kartta käyttäjän sijainnin mukaan
     haeSijainti();
     // Lisätään openstreetmap karttataso
     var layer = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');
     kartta.addLayer(layer);
     karttaMarker = L.marker(ilmoituksenSijainti, {draggable: 'true'});
     karttaMarker.addTo(kartta);
}
 addEventListener('DOMContentLoaded',sivuLatautunut);