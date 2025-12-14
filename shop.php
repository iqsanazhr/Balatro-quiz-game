<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

// Handle Buy Action
if (isset($_POST['buy_card_id'])) {
    $card_id = $_POST['buy_card_id'];

    // Fetch User Chips again to be safe
    $stmt = $pdo->prepare("SELECT chips_balance FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user_chips = $stmt->fetchColumn();

    // Fetch Card Price
    $stmt = $pdo->prepare("SELECT * FROM cards WHERE id = ?");
    $stmt->execute([$card_id]);
    $card = $stmt->fetch();

    if ($card) {
        // Check if already owned? (Optional, user didn't specify limits, but let's allow duplicates like a deck builder)
        // Or maybe unique? Let's assume Unique for 'Collection' view simplicity, 
        // BUT Balatro allows multiples. Let's allow multiples.

        if ($user_chips >= $card['price']) {
            // Transaction
            $pdo->beginTransaction();
            try {
                // Deduct Chips
                $stmt = $pdo->prepare("UPDATE users SET chips_balance = chips_balance - ? WHERE id = ?");
                $stmt->execute([$card['price'], $user_id]);

                // Add to Collection
                $stmt = $pdo->prepare("INSERT INTO user_inventory (user_id, card_id) VALUES (?, ?)");
                $stmt->execute([$user_id, $card_id]);

                $pdo->commit();
                $message = "ACQUIRED: " . htmlspecialchars($card['name']);
            } catch (Exception $e) {
                $pdo->rollBack();
                $message = "ERROR: TRANSACTION FAILED";
            }
        } else {
            $message = "INSUFFICIENT FUNDS";
        }
    }
}

// Fetch All Cards
$cards_stmt = $pdo->query("SELECT * FROM cards ORDER BY price ASC");
$all_cards = $cards_stmt->fetchAll();

// Refresh User Chips for HUD
$stmt = $pdo->prepare("SELECT chips_balance FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$current_chips = $stmt->fetchColumn();

// Fetch Owned Card IDs for "Unique" logic
$stmt = $pdo->prepare("SELECT DISTINCT card_id FROM user_inventory WHERE user_id = ?");
$stmt->execute([$user_id]);
$owned_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

$page_title = 'SHOP';
include 'includes/header.php';

$page_label = 'SHOP';
$show_chips = true;
include 'includes/navbar.php';
?>

    <!-- Message Overlay -->
    <?php if ($message): ?>
        <div
            style="position:fixed; top:80px; left:50%; transform:translateX(-50%); background:black; border:2px solid var(--gold); padding:10px 20px; z-index:1000; color:var(--gold);">
            <?php echo $message; ?>
        </div>
        <script>setTimeout(() => { document.querySelector('div[style*="position:fixed"]').style.display = 'none'; }, 2000);</script>
    <?php endif; ?>

    <div class="content-grid-container">
        <?php foreach ($all_cards as $c): ?>
            <div class="shop-card">
                <div class="card-img-placeholder" style="background: none; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 10px 0;">
                    <img src="<?php echo htmlspecialchars($c['image_url']); ?>" alt="<?php echo htmlspecialchars($c['name']); ?>" style="width: 100%; height: 100%; object-fit: contain;">
                </div>
                <div class="card-info">
                    <div class="card-name"><?php echo htmlspecialchars($c['name']); ?></div>
                    <div class="card-type" style="font-size:0.9rem; color:#444;">
                        <?php echo htmlspecialchars($c['type']); ?>
                        <span
                            style="font-weight:bold; color:<?php echo $c['category'] == 'Skill' ? 'var(--blue)' : 'var(--red)'; ?>">
                            [<?php echo htmlspecialchars($c['category']); ?>]
                        </span>
                    </div>
                    <div class="card-price" style="font-size:1.4rem;">$<?php echo number_format($c['price']); ?></div>

                    <form method="POST">
                        <input type="hidden" name="buy_card_id" value="<?php echo $c['id']; ?>">
                        
                        <?php 
                        $is_owned = in_array($c['id'], $owned_ids);
                        $is_unique_type = ($c['category'] === 'Collection');
                        
                        if ($is_unique_type && $is_owned): ?>
                             <button type="button" class="btn-buy" disabled style="background:#555; color:#aaa;">
                                OWNED
                            </button>
                        <?php else: ?>
                            <button type="submit" class="btn-buy" <?php echo ($current_chips < $c['price']) ? 'disabled' : ''; ?>>
                                BUY
                            </button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>



<?php
$show_return_btn = true;
include 'includes/footer.php';
?>