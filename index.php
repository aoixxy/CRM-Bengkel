<?php $title = "Beranda"; ?>
<?php include 'templates/header.php'; ?>

<!-- ================= NAVBAR ================= -->
<nav class="glass shadow-sm px-6 py-3 flex justify-between items-center sticky top-0 z-50">

    <h1 class="font-bold text-gradient text-lg">
        SITANGGANG JAYA PRESS MOTOR
    </h1>

    <div class="hidden md:flex gap-6 items-center">
        <a href="#beranda" class="font-semibold text-blue-600">Beranda</a>
        <a href="#layanan">Layanan</a>
        <a href="#tentang">Tentang</a>
    </div>

    <a href="auth/pilih_role.php" class="btn-modern flex items-center gap-2">
    <i class="fa-solid fa-user-lock"></i>
    Login
</a>
</nav>

<!-- ================= HERO ================= -->
<section id="beranda" class="hero-bg py-16">

<div class="max-w-6xl mx-auto px-6 grid md:grid-cols-2 gap-10 items-center">

    <!-- TEXT -->
    <div class="fade-in">
        <span class="bg-white text-blue-700 px-3 py-1 rounded-full text-sm">
            Bengkel UMKM Terpercaya
        </span>

        <h1 class="text-5xl font-bold mt-4 mb-4 leading-tight">
            Mekanik Presisi <br>
            <span class="text-yellow-300">untuk Kendaraan Anda</span>
        </h1>

        <p class="text-gray-200 mb-6">
            CRM berbasis data dengan fitur SFA, chatbot, dan reward pelanggan loyal.
        </p>

        <div class="flex gap-4 mt-4 flex-wrap">

            <a href="chat.php" class="btn-modern flex items-center gap-2">
    <i class="fa-solid fa-comments"></i>
    Chat
</a>

            <a href="#layanan" 
               class="bg-white text-blue-700 px-6 py-3 rounded-xl shadow hover:bg-blue-50 hover:scale-105 transition flex items-center gap-2">
                <i class="fa-solid fa-arrow-down"></i>
                Lihat Layanan
            </a>

        </div>
    </div>

    <!-- SLIDER -->
    <div class="relative w-full max-w-[500px] mx-auto">

    <div class="overflow-hidden rounded-xl shadow-lg bg-gray-100">

        <div id="slider" class="flex transition-transform duration-700">

            <img src="assets/img/bengkel.webp" 
                 class="w-full flex-shrink-0 h-[350px] object-contain">

            <img src="assets/img/bengkel2.webp" 
                 class="w-full flex-shrink-0 h-[350px] object-contain">

            <img src="assets/img/bengkel3.webp" 
                 class="w-full flex-shrink-0 h-[350px] object-contain">

        </div>
    </div>

</div>
</section>

<!-- ================= LAYANAN ================= -->
<section id="layanan" class="py-16">
<div class="max-w-6xl mx-auto px-6 text-center">

    <h2 class="text-3xl font-bold mb-10 text-gradient">
        Layanan Kami
    </h2>

    <div class="grid md:grid-cols-3 gap-6">

        <div class="bg-white p-6 rounded-xl shadow card-hover">
            <i class="fa-solid fa-tools text-3xl text-blue-600 mb-3"></i>
            <h3 class="font-semibold text-lg">Press Rangka</h3>
            <p class="text-gray-600">Perbaikan rangka presisi.</p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow card-hover">
            <i class="fa-solid fa-screwdriver-wrench text-3xl text-blue-600 mb-3"></i>
            <h3 class="font-semibold text-lg">Servis Berkala</h3>
            <p class="text-gray-600">Perawatan rutin kendaraan.</p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow card-hover">
            <i class="fa-solid fa-gears text-3xl text-blue-600 mb-3"></i>
            <h3 class="font-semibold text-lg">Sparepart</h3>
            <p class="text-gray-600">Sparepart berkualitas.</p>
        </div>

    </div>
</div>
</section>

<!-- ================= TENTANG ================= -->
<section id="tentang" class="py-12">
<div class="max-w-6xl mx-auto px-6 grid md:grid-cols-2 gap-8 items-center">

    <div>
        <h2 class="text-3xl font-bold mb-4">Tentang Bengkel</h2>

        <p class="text-gray-600 mb-4">
            SITANGGANG JAYA PRESS MOTOR adalah bengkel spesialis press rangka motor dan servis kendaraan dengan pengalaman profesional.
        </p>

        <p class="text-gray-600 mb-2">
            📞 <b>081375269688</b>
        </p>

        <p class="text-gray-600 mb-4">
            🕒 <b>08:00 - 19:00 WIB</b>
        </p>
    </div>

    <div class="w-full h-[300px] rounded overflow-hidden shadow">
        <iframe 
            src="https://maps.google.com/maps?q=SITANGGANG%20JAYA%20PRESS%20MOTOR&t=&z=15&ie=UTF8&iwloc=&output=embed"
            class="w-full h-full border-0"
            allowfullscreen>
        </iframe>
    </div>

</div>
</section>

<section class="py-16 bg-white text-center">
<div class="max-w-6xl mx-auto px-6">

    <h2 class="text-3xl font-bold mb-4">
        Kenapa Pilih Kami?
    </h2>

    <p class="text-gray-500 mb-10">
        Pelayanan modern dengan teknologi CRM untuk pengalaman pelanggan terbaik
    </p>

    <div class="grid md:grid-cols-3 gap-6">

        <div class="p-6 rounded-xl shadow card-hover">
            ⚡ Cepat & Akurat
            <p class="text-gray-500 text-sm mt-2">
                Sistem digital mempercepat pelayanan
            </p>
        </div>

        <div class="p-6 rounded-xl shadow card-hover">
            🤖 Chatbot Konsultasi
            <p class="text-gray-500 text-sm mt-2">
                Konsultasi langsung tanpa datang ke bengkel
            </p>
        </div>

        <div class="p-6 rounded-xl shadow card-hover">
            🎁 Reward Pelanggan
            <p class="text-gray-500 text-sm mt-2">
                Dapatkan bonus untuk pelanggan loyal
            </p>
        </div>

    </div>

</div>
</section>

<!-- ================= CHATBOT FLOATING ================= -->
<div class="chatbot-container">

</div>

</div>

<?php include 'templates/footer.php'; ?>