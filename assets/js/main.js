// =======================
// LOGOUT
// =======================
function confirmLogout() {
    if (confirm("Yakin ingin keluar?")) {
        window.location.href = "/crm_bengkel/auth/logout.php";
    }
}

// =======================
// CHATBOT TOGGLE
// =======================
function toggleChat() {
    const box = document.getElementById("chat-box");

    if (box.style.display === "flex") {
        box.style.display = "none";
    } else {
        box.style.display = "flex";
    }
}

document.getElementById("chat-toggle").onclick = toggleChat;

// =======================
// MAIN INIT
// =======================
document.addEventListener("DOMContentLoaded", () => {

    // =======================
    // SLIDER
    // =======================
    const slider = document.getElementById("slider");
    const dots = document.querySelectorAll(".dot");

    if (slider) {
        let index = 0;
        const total = slider.children.length;

        let autoSlide;
        let startX = 0;
        let endX = 0;

        function showSlide(i) {
            index = (i + total) % total;
            slider.style.transform = `translateX(-${index * 100}%)`;

            dots.forEach(dot => dot.classList.remove("opacity-100"));
            if (dots[index]) dots[index].classList.add("opacity-100");
        }

        function nextSlide() {
            showSlide(index + 1);
        }

        function prevSlide() {
            showSlide(index - 1);
        }

        // biar bisa dipanggil dari HTML
        window.nextSlide = nextSlide;
        window.prevSlide = prevSlide;

        function startAutoSlide() {
            autoSlide = setInterval(nextSlide, 3000);
        }

        function stopAutoSlide() {
            clearInterval(autoSlide);
        }

        // SWIPE
        slider.addEventListener("touchstart", (e) => {
            startX = e.touches[0].clientX;
            stopAutoSlide();
        });

        slider.addEventListener("touchmove", (e) => {
            endX = e.touches[0].clientX;
        });

        slider.addEventListener("touchend", () => {
            const diff = startX - endX;

            if (diff > 50) nextSlide();
            if (diff < -50) prevSlide();

            startAutoSlide();
        });

        // HOVER
        slider.addEventListener("mouseenter", stopAutoSlide);
        slider.addEventListener("mouseleave", startAutoSlide);

        // INIT
        showSlide(0);
        startAutoSlide();
    }

    // =======================
    // CHATBOT BUTTON
    // =======================
    const chatBtn = document.getElementById("chatbot-toggle");

    if (chatBtn) {
        chatBtn.addEventListener("click", toggleChat);
    }

});