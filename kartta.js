
const asetaSijainti = (sijainti) => {
    if (sijainti !== null) {
        ilmoituksenSijainti = sijainti;
        // Haetaan käyttäjän asettama zoom taso
        let zoom = kartta.getZoom();
        // Asetetaan kartan näkymä sijaintiin
        kartta.setView(ilmoituksenSijainti, zoom);
        // Päivitetään markerin sijainti
        karttaMarker.setLatLng(ilmoituksenSijainti);
        console.log("Asetettiin ilmoituksen sijainti: " + sijainti);
    }
}

const haeIlmoituksenSijainti = () => {
    let leveys = parseFloat(document.getElementById("ilmoitus_sijainti_lev").value);
    let pituus = parseFloat(document.getElementById("ilmoitus_sijainti_pit").value);
    if (!isNaN(leveys) && !isNaN(pituus)) {
        asetaSijainti([leveys, pituus]);
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
        zoom: 15
     }
     kartta = new L.map('kartta', mapOptions);
     // Lisätään openstreetmap karttataso
     var layer = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');
     kartta.addLayer(layer);
     let markerAsetukset = {
        title: "Ilmoituksen sijainti",
         /*{draggable: 'true'}*/ // Jos halutaan että käyttäjä voi vetää markeria
     }
     karttaMarker = L.marker(ilmoituksenSijainti, markerAsetukset);
     karttaMarker.addTo(kartta);
     haeIlmoituksenSijainti();
}
 addEventListener('DOMContentLoaded',sivuLatautunut);