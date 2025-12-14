<?php
session_start();
include 'includes/db.php';
$page_title = 'INFO';
include 'includes/header.php';

$page_label = 'INFO';
include 'includes/navbar.php';
?>

<div class="content-grid-container" style="display:block; color:white; padding:40px 100px; overflow-y:auto;">
    <h1 style="color:var(--red); font-size:3rem;">GAME RULES</h1>

    <p>1. <strong>THE BLIND</strong>: A Quiz Challenge you must defeat.</p>
    <p>2. <strong>CHIPS</strong>: The currency earned by answering correctly. Used to buy Joker cards.</p>
    <p>3. <strong>JOKERS</strong>: Special cards that enhance your collection (Cosmetic for this version).</p>
    <p>4. <strong>GAME OVER</strong>: Answer incorrectly and you lose the hand.</p>

    <br>
    <hr style="border-color:var(--blue);"><br>

    <h2 style="color:var(--blue);">ABOUT</h2>
    <p>This is a fan-made web application inspired by the game Localthunk's Balatro.</p>
    <p>Created for University Project.</p>

    <br><br>
    <hr><br><br>

    <h1 style="color:var(--red); font-size:3rem; text-align:center; margin-bottom: 40px;">CARD ENCYCLOPEDIA</h1>

    <?php
    // Fetch Skill Cards
    $stmt = $pdo->prepare("SELECT * FROM cards WHERE category = 'Skill' ORDER BY price ASC");
    $stmt->execute();
    $skill_cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch Collection Cards
    $stmt = $pdo->prepare("SELECT * FROM cards WHERE category = 'Collection' ORDER BY price ASC");
    $stmt->execute();
    $collection_cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <h2 style="color:var(--gold); border-bottom: 2px solid var(--gold); display:inline-block; margin-bottom: 30px;">SKILL CARDS</h2>
    <?php foreach ($skill_cards as $card): ?>
        <div class="game-info-section" style="padding: 20px 0; border-bottom: 1px dashed #444;">
            <div class="info-content">
                <img src="<?php echo htmlspecialchars($card['image_url']); ?>"
                    alt="<?php echo htmlspecialchars($card['name']); ?>" class="joker-showcase tilt-card"
                    style="max-width: 200px; transform: rotate(-5deg);">
                <div class="info-text">
                    <h3 style="color: var(--blue); text-shadow: 2px 2px 0 #000; font-size: 2.2rem;">
                        <?php echo htmlspecialchars($card['name']); ?>
                    </h3>
                    <p style="color: #fff; font-size: 1.4rem;">
                        "<?php echo htmlspecialchars($card['description']); ?>"
                    </p>
                    <div style="margin-top: 10px;">
                        <span class="card-price">$<?php echo number_format($card['price']); ?></span>
                        <span style="color: #aaa; margin-left: 10px;"><?php echo htmlspecialchars($card['type']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <br><br>
    <h2 style="color:var(--red); border-bottom: 2px solid var(--red); display:inline-block; margin-bottom: 30px;">COLLECTION CARDS</h2>
    <?php foreach ($collection_cards as $card): ?>
        <div class="game-info-section" style="padding: 20px 0; border-bottom: 1px dashed #444;">
            <div class="info-content">
                <img src="<?php echo htmlspecialchars($card['image_url']); ?>"
                    alt="<?php echo htmlspecialchars($card['name']); ?>" class="joker-showcase tilt-card"
                    style="max-width: 200px; transform: rotate(-5deg);">
                <div class="info-text">
                    <h3 style="color: var(--gold); text-shadow: 2px 2px 0 #000; font-size: 2.2rem;">
                        <?php echo htmlspecialchars($card['name']); ?>
                    </h3>
                    <p style="color: #fff; font-size: 1.4rem;">
                        "<?php echo htmlspecialchars($card['description']); ?>"
                    </p>
                    <div style="margin-top: 10px;">
                        <span class="card-price">$<?php echo number_format($card['price']); ?></span>
                        <span style="color: #aaa; margin-left: 10px;"><?php echo htmlspecialchars($card['type']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <div style="text-align:center; margin-top:50px; color:#777;">
        <p>More cards coming soon...</p>
    </div>

</div>



<?php
$show_return_btn = true;
include 'includes/footer.php';
?>