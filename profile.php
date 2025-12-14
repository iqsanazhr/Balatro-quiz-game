<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get Stats
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Get Collection Count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM user_inventory WHERE user_id = ?");
$stmt->execute([$user_id]);
$card_count = $stmt->fetchColumn();

?>
$stmt->execute([$user_id]);
$card_count = $stmt->fetchColumn();

$page_title = 'PROFILE';
include 'includes/header.php';

$page_label = 'PROFILE';
include 'includes/navbar.php';
?>

<div
    style="flex-grow: 1; display:flex; flex-direction:column; align-items:center; justify-content:center; padding-top: 50px; text-align:center;">

    <div class="boss-blind-panel" style="width: 600px; animation: fadeInUp 0.5s ease-out both;">
        <h2 style="color:var(--blue); font-size:3rem; margin: 0; text-shadow: 4px 4px 0 #000;">PLAYER STATS</h2>
        <div style="width: 100%; height: 2px; background: var(--red); margin: 10px 0 20px 0;"></div>

        <div class="stats-table">
            <div class="stats-row">
                <span class="stats-label">NAME:</span>
                <span class="stats-value"><?php echo htmlspecialchars($user['username']); ?></span>
            </div>
            <div class="stats-row">
                <span class="stats-label">CHIPS:</span>
                <span class="stats-value text-gold">$<?php echo number_format($user['chips_balance']); ?></span>
            </div>
            <div class="stats-row">
                <span class="stats-label">CARDS OWNED:</span>
                <span class="stats-value" style="color: var(--blue)"><?php echo $card_count; ?></span>
            </div>
            <div class="stats-row">
                <span class="stats-label">JOINED:</span>
                <span class="stats-value join-date"><?php echo $user['created_at']; ?></span>
            </div>
        </div>

        <div style="margin-top: 30px;">
            <img src="assets/joker-head.png"
                style="width: 80px; image-rendering: pixelated; filter: drop-shadow(4px 4px 0 rgba(0,0,0,0.5));"
                alt="Decoration">
        </div>
    </div>

</div>



<?php
$show_return_btn = true;
include 'includes/footer.php';
?>