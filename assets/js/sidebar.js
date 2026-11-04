// ACTIVE MENU OTOMATIS
const links = document.querySelectorAll(".sidebar a");

links.forEach(link => {
    if (link.href === window.location.href) {
        link.classList.add("active");
    }
});