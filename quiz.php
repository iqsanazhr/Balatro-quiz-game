<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'includes/db.php';
$user_id = $_SESSION['user_id'];

// Fetch User's Skill Cards (grouped by type/name)
$stmt = $pdo->prepare("
    SELECT c.name, c.type, c.effect_logic, c.image_url, COUNT(uc.card_id) as quantity 
    FROM user_inventory uc
    JOIN cards c ON uc.card_id = c.id
    WHERE uc.user_id = ? AND c.category = 'Skill'
    GROUP BY c.name
");
$stmt->execute([$user_id]);
$inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Convert to JSON for JS
$inventory_json = json_encode($inventory);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>BALATRO - THE BLIND</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/quiz.css?v=<?php echo time(); ?>">
</head>

<body class="game-body">
    <div class="crt-overlay"></div>

    <div class="game-layout">
        <!-- LEFT STATUS COLUMN (HUD + TIMER) -->
        <div class="status-sidebar">

            <!-- HUD VERTICAL -->
            <div class="hud-vertical">
                <div class="hud-item">
                    <span class="label">SCORE</span>
                    <span class="value text-gold" id="current-score">0</span>
                </div>
                <div class="hud-item">
                    <span class="label">MULT</span>
                    <span class="value text-red" id="current-mult">X1.0</span>
                </div>
                <div class="hud-item">
                    <span class="label">TARGET</span>
                    <span class="value text-blue" id="target-score">???</span>
                </div>
            </div>

            <!-- VERTICAL TIMER -->
            <div class="timer-container-vertical">
                <div class="timer-bar-vertical" id="timer-bar"></div>
            </div>

        </div>

        <!-- SIDEBAR LEFT REMOVED per user request -->

        <!-- MAIN GAME AREA -->

        <!-- SIDEBAR LEFT REMOVED per user request -->

        <!-- MAIN GAME AREA -->
        <div class="main-stage">

            <!-- QUESTION CARD (THE BLIND) -->
            <div id="question-container" class="question-card">
                <div class="blind-badge">SMALL BLIND</div>
                <h2 id="question-text">LOADING...</h2>
                <div class="reward-tag">REWARD: <span id="q-reward">50</span> CHIPS</div>
            </div>

            <!-- OPTIONS (THE HAND) -->
            <div id="options-container" class="options-grid">
                <!-- Option Cards injected by JS -->
            </div>

        </div>

        <!-- SIDEBAR RIGHT (CONSUMABLES) -->
        <div class="sidebar-right">
            <h3>CONSUMABLES</h3>
            <div id="consumable-slots" class="card-slots">
                <!-- Injected by JS -->
            </div>
        </div>
    </div>

    <!-- OVERLAYS -->
    <div id="game-overlay" class="result-overlay" style="display:none;">
        <h1 id="overlay-title">ROUND OVER</h1>
        <h2 id="overlay-score">SCORE: 0</h2>
        <button class="btn-play" onclick="window.location.href='home.php'">RETURN TO LOBBY</button>
    </div>

    <script>
        window.userInventory = <?php echo $inventory_json; ?>;
    </script>
    <script src="assets/js/quiz.js"></script>
</body>

</html>