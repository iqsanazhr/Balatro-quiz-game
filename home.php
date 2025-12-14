<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch up-to-date user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BALATRO - DASHBOARD</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
</head>

<body class="dashboard-body">
    <div class="crt-overlay"></div>

    <!-- Header / HUD -->
    <div class="hud-top">
        <div class="hud-item user-info">
            <span class="label">PLAYER:</span> <span
                class="value"><?php echo htmlspecialchars($user['username']); ?></span>
        </div>
        <div class="hud-item chips-display">
            <span class="label">CHIPS:</span> <span
                class="value text-gold">$<?php echo number_format($user['chips_balance']); ?></span>
        </div>
        <a href="login.php?logout=1" class="hud-item btn-logout">EJECT</a>
    </div>

    <!-- Main Logo (Object) -->
    <img src="assets/balatro_logo.png" alt="Balatro Logo" class="home-logo">

    <h2 class="menu-header">MAIN MENU</h2>

    <!-- Main Card Menu (Horizontal Scroll) -->
    <div class="card-menu-container">
        <!-- 1. Profile -> Showman -->
        <div class="menu-card art-card" style="background-image: url('assets/card/Showman.webp');"
            onclick="window.location.href='profile.php'">
            <div class="card-inner">
                <div class="card-title art-label">PROFILE</div>
            </div>
        </div>

        <!-- 2. Info -> DNA -->
        <div class="menu-card art-card" style="background-image: url('assets/card/DNA.webp'); animation-delay: 0.2s;"
            onclick="window.location.href='info.php'">
            <div class="card-inner">
                <div class="card-title art-label">INFO</div>
            </div>
        </div>

        <!-- 3. THE BLIND (PLAY) -> Joker (Keep existing or update to provided art? User didn't specify changing Play art, just "samakan ui nnya dengan joker card play". I'll keep default joker for Play as user didn't rename it.) -->
        <div class="menu-card art-card main-play" style="background-image: url('assets/joker.png');"
            onclick="window.location.href='quiz.php'">
            <div class="card-inner">
                <div class="card-title art-label blink-text" style="font-size: 2.5rem;">PLAY</div>
            </div>
        </div>

        <!-- 4. Shop -> Brainstorm -->
        <div class="menu-card art-card" style="background-image: url('assets/card/Brainstorm.webp');"
            onclick="window.location.href='shop.php'">
            <div class="card-inner">
                <div class="card-title art-label">SHOP</div>
            </div>
        </div>

        <!-- 5. Collection -> Pareidolia -->
        <div class="menu-card art-card" style="background-image: url('assets/card/Pareidolia.webp');"
            onclick="window.location.href='collection.php'">
            <div class="card-inner">
                <div class="card-title art-label">DECK</div>
            </div>
        </div>
    </div>

    <!-- Game Info / Joker Showcase -->
    <div class="game-info-section">
        <div class="info-content">
            <img src="assets/joker.png" alt="Joker" class="joker-showcase tilt-card">
            <div class="info-text">
                <h3>The poker roguelike.</h3>
                <p>Balatro is a hypnotically satisfying deckbuilder where you play illegal poker hands, discover
                    game-changing jokers, and trigger adrenaline-pumping, outrageous combos.</p>
            </div>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
    <script>
        // Simple Tilt Logic just for this page if not generic
        const cards = document.querySelectorAll('.tilt-card');

        cards.forEach(card => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                const centerX = rect.width / 2;
                const centerY = rect.height / 2;

                const rotateX = ((y - centerY) / centerY) * -15; // Max 15deg
                const rotateY = ((x - centerX) / centerX) * 15;

                card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.05)`;
            });

            card.addEventListener('mouseleave', () => {
                card.style.transform = `perspective(1000px) rotateX(0deg) rotateY(0deg) scale(1)`;
            });
        });
    </script>
    <!-- Scrolling Banner Footer -->
    <div class="scrolling-banner"></div>

    <!-- Final Footer Section -->
    <div class="footer-section">
        <img src="assets/left.png" class="footer-img-left" alt="Decoration Left">

        <div class="footer-text">
            <div class="footer-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Legal Information</a>
                <a href="#">Cookie Settings</a>
            </div>
            <div class="copyright">
                &copy; 2025 Playstack Ltd. Balatro is a registered trademark of LocalThunk LLC. All rights reserved.
            </div>
        </div>

        <img src="assets/right.png" class="footer-img-right" alt="Decoration Right">
    </div>

</body>

</html>