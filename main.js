function navbarMod() {
    if(isMobile){
        navbar.classList.add("fixed-bottom");
        navbar.classList.remove("sticky-top");
    }
    else{
        navbar.classList.remove("fixed-bottom");
        navbar.classList.add("sticky-top");
    }
}

let navbar = document.querySelector(".navbar");
let isMobile = window.matchMedia("only screen and (max-width: 760px)").matches;
// console.log(navbar);
navbarMod();

let navbarBrand = document.querySelector(".navbar-brand");
let navPills = document.querySelectorAll(".nav-link");

navPills.forEach(navPill => {
    navPill.addEventListener("click", changeNavbarBrand)
});

function changeNavbarBrand() {
    let activePill = document.querySelector(".nav-link.active");
    let activePillID = activePill.id
    switch (activePillID) {
        // case "personal-tab":
        //     navbarBrand.innerHTML = ("Mir Shafayat Ahmed")
        //     break;
        // case "education-tab":
        //     navbarBrand.innerHTML = ("Education")
        //     break;
        // case "work-experience-tab":
        //     navbarBrand.innerHTML = ("Work Experience")
        //     break;
        // case "projects-tab":
        //     navbarBrand.innerHTML = ("Projects")
        //     break;
        default:
            navbarBrand.innerHTML = ("5 Day Forecast")
            break;
    }
}

let formSelect = document.querySelector(".form-select");
let locationOption = formSelect.firstElementChild;

getLocation();

function getLocation() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(setGPSValue);
  } else {
    form.innerHTML = "Geolocation is not supported by this browser.";
  }
}

function setGPSValue(position) {
  locationOption.value = `{\"lat\":${position.coords.latitude},\"long\":${position.coords.longitude}}`;
}