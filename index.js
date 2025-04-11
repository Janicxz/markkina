const kuvaClick = (elem) => {
    console.log("Kuva clicked " + elem);

    if (!elem) {
        return;
    }
    let width = "width: 100%;";
    if (elem.style.cssText === width) {
        width = "width: 150px;";
    }
    elem.style = width;
    console.log("done " + elem.style.cssText);
}