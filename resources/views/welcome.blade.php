<?php
session_start();

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'pelanggan') {
        header("Location: pelanggan/dashboard.php");
        exit;
    } elseif ($_SESSION['role'] == 'admin') {
        header("Location: admin/dashboard.php");
        exit;
    } elseif ($_SESSION['role'] == 'karyawan') {
        header("Location: karyawan/dashboard.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ilin Catering | Healthy Food</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <script src="{{ asset('js/landing.js') }}" defer></script>

</head>
<body>

<header class="navbar">
    <div class="logo">
        <a href="/">
            <img src="{{ asset('images/logo.png') }}" alt="Ilin Catering Logo">
        </a>
    </div>

    <nav>
        <a href="#">Home</a>
        <a href="/about">About</a>
        <a href="/menu">Menu</a>
        <a href="#">Review</a>
        <a href="/kontak">Kontak</a>
        <button class="btn-login">Log In</button>
    </nav>
</header>

<section class="hero">
    <div class="hero-left">
        <p class="subtitle">Makanan rumahan, enak, hemat, bersih.</p>
        <h1>
            <span class="highlight">Rasakan Kesempurnaan</span><br>
            di Setiap Gigitan
        </h1>
        <button class="btn-primary" onclick="window.location='{{ url('/menu') }}'"> Lihat Menu </button>
    </div>

    <div class="hero-right">
        <div class="liquid-bg" id="liquidBg"></div>
        <img src="{{ asset('images/food.png') }}" class="food-img" alt="Food">
    </div>
</section>

<section class="info-section">
    <div class="info-container fade-up">

        <div class="info-item">
            <div class="info-icon">👤</div>
            <h4>Tanpa Berlangganan</h4>
            <p>
                Bisa langsung pesan sesuai<br>
                yang diinginkan tanpa ada<br>
                kontrak langganan.
            </p>
        </div>

        <div class="divider"></div>

        <div class="info-item">
            <div class="info-icon">🍽️</div>
            <h4>Bebas Pilih Menu</h4>
            <p>
                Terdapat 20 pilihan menu<br>
                yang tersedia, bebas<br>
                memilih menu.
            </p>
        </div>

        <div class="divider"></div>

        <div class="info-item">
            <div class="info-icon">⏱️</div>
            <h4>Bebas Pilih Waktu</h4>
            <p>
                Waktu pengantaran fleksibel<br>
                dari jam 07:00 – 20:00 WIB.
            </p>
            <small>*syarat dan kondisi berlaku</small>
        </div>

    </div>
</section>

<section class="menu-section">
    <h2 class="fade-up">Menu Terpopuler<br>Minggu ini</h2>

    <div class="menu-list">
        <div class="menu-card fade-up">
            <img src="{{ asset('images/menu1.jpeg') }}">
            <p>Ayam Crispy Saus<br>Tiram</p>
        </div>

        <div class="menu-card fade-up">
            <img src="{{ asset('images/menu2.jpeg') }}">
            <p>Nasi Goreng Sosis<br>Chikuwa</p>
        </div>
    </div>

    <button class="btn-more fade-up"
            onclick="window.location='{{ url('/menu') }}'">
        Lebih Banyak Menu →
    </button>
</section>

<!-- KATEGORI MENU -->
<section class="category-section fade-up">
    <div class="category-header">
        <h2>Kategori Menu</h2>
        <a href="#" class="btn-category">
            Lihat Menu Selengkapnya →
        </a>
    </div>

    <div class="category-list">

        <div class="category-card">
            <img src="{{ asset('images/cat-ayam.jpeg') }}" alt="Ayam">
            <div class="category-overlay">
                <h4>Ayam</h4>
                <span>23 Menu</span>
            </div>
        </div>

        <div class="category-card">
            <img src="{{ asset('images/cat-ikan.jpeg') }}" alt="Ikan">
            <div class="category-overlay">
                <h4>Ikan & Seafood</h4>
                <span>7 Menu</span>
            </div>
        </div>

        <div class="category-card">
            <img src="{{ asset('images/cat-nasi.jpg') }}" alt="Nasi">
            <div class="category-overlay">
                <h4>Nasi</h4>
                <span>5 Menu</span>
            </div>
        </div>

        <div class="category-card">
            <img src="{{ asset('images/cat-sapi.jpeg') }}" alt="Sapi">
            <div class="category-overlay">
                <h4>Sapi & Kambing</h4>
                <span>3 Menu</span>
            </div>
        </div>

    </div>
</section>

<!-- FOOTER -->
<footer class="footer">
    <div class="footer-container">

        <!-- Brand -->
        <div class="footer-brand">
            <img src="{{ asset('images/logo.png') }}" alt="Ilin Catering Logo">
            <p>
                Ilin Catering menyediakan makanan rumahan yang sehat,
                bersih, dan lezat untuk kebutuhan harian Anda.
            </p>
        </div>

        <!-- Menu -->
        <div class="footer-links">
            <h4>Menu</h4>
            <a href="#">Home</a>
            <a href="#">About</a>
            <a href="#">Menu</a>
            <a href="#">Review</a>
            <a href="#">Kontak</a>
        </div>

        <!-- Kontak -->
        <div class="footer-contact">
            <h4>Kontak</h4>
            <p>📍 Bati-Bati, Indonesia</p>
            <p>📞 0812-3456-7890</p>
            <p>✉️ ilincatering@gmail.com</p>
        </div>

    </div>

    <div class="footer-bottom">
        © {{ date('Y') }} Ilin Catering. All rights reserved.
    </div>
</footer>

</body>
</html>