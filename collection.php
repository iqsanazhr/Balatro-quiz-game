<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Join Collection with Cards
$stmt = $pdo->prepare("
    SELECT c.*, COUNT(uc.card_id) as quantity
    FROM user_inventory uc
    JOIN cards c ON uc.card_id = c.id
    WHERE uc.user_id = ?
    GROUP BY uc.card_id
    ORDER BY c.category DESC, c.price ASC
");
$stmt->execute([$user_id]);
$my_cards = $stmt->fetchAll();
?>
$stmt->execute([$user_id]);
$my_cards = $stmt->fetchAll();

$page_title = 'COLLECTION';
include 'includes/header.php';

$page_label = 'COLLECTION';
$show_count = true;
$count_value = count($my_cards);
include 'includes/navbar.php';
?>

<div class="content-grid-container">
    <?php if (count($my_cards) == 0): ?>
        <div style="text-align:center; width:100%; margin-top:50px; font-size:2rem; color:#888;">
            NO CARDS ACQUIRED.<br><br>
            <a href="shop.php" style="color:var(--gold);">GO TO SHOP</a>
        </div>
    <?php else: ?>
        <?php foreach ($my_cards as $c): ?>
            <div class="shop-card collection-card">

                <div class="owned-badge">OWNED</div>
                <?php if ($c['quantity'] > 1): ?>
                    <div class="owned-badge" style="top:30px; background:var(--gold); color:black;">
                        x<?php echo $c['quantity']; ?></div>
                <?php endif; ?>

                <div class="card-img-placeholder"
                    style="background: none; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 10px 0;">
                    <!-- Visual distinction for owned cards -->
                    <img src="<?php echo htmlspecialchars($c['image_url']); ?>"
                        alt="<?php echo htmlspecialchars($c['name']); ?>"
                        style="width: 100%; height: 100%; object-fit: contain;">
                </div>
                <div class="card-info">
                    <div class="card-name"><?php echo htmlspecialchars($c['name']); ?></div>
                    <div class="card-type">
                        <?php echo htmlspecialchars($c['type']); ?>
                        <span style="color:<?php echo $c['category'] == 'Skill' ? 'var(--blue)' : 'var(--red)'; ?>">
                            [<?php echo htmlspecialchars($c['category']); ?>]
                        </span>
                    </div>
                    <div style="font-size:0.7rem; color:#555; margin-top:10px;">
                        <?php echo htmlspecialchars($c['description']); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>



<?php
$show_return_btn = true;
include 'includes/footer.php';
?>