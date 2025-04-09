// Keskittää kartan käyttäjän sijaintiin jos sijaintitiedot on saatavilla
const haeSijainti = () => {
    if (navigator.geolocation && kartta !== null) {
        navigator.geolocation.getCurrentPosition((pos) => {
            asetaSijainti([pos.coords.latitude, pos.coords.longitude]);
        });
    }
}
const asetaSijainti = (sijainti) => {
    if (sijainti !== null) {
        ilmoituksenSijainti = sijainti;
        // Haetaan käyttäjän asettama zoom taso
        let zoom = kartta.getZoom();
        // Asetetaan kartan näkymä sijaintiin
        kartta.setView(ilmoituksenSijainti, zoom);
        // Päivitetään markerin sijainti
        karttaMarker.setLatLng(ilmoituksenSijainti);
        // Päivitetään input kentän arvo
        document.getElementById("ilmoitusSijainti").value = ilmoituksenSijainti;
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
     let markerAsetukset = {
        title: "Ilmoituksen sijainti",
         /*{draggable: 'true'}*/ // Jos halutaan että käyttäjä voi vetää markeria
     }
     karttaMarker = L.marker(ilmoituksenSijainti, markerAsetukset);
     karttaMarker.addTo(kartta);
     // Kun karttaa klikataan, asetetaan ilmoituksen sijainti klikattuun kohtaan
     kartta.on('click', (e) => {
            asetaSijainti([e.latlng.lat, e.latlng.lng]);
     })
}
 addEventListener('DOMContentLoaded',sivuLatautunut);