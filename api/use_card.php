<?php
session_start();
include '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);
$effect = $data['effect'] ?? '';

if (!$effect) {
    echo json_encode(['error' => 'Missing effect']);
    exit;
}

try {
    // 1. Find the card ID and TYPE associated with this effect
    $stmt = $pdo->prepare("SELECT id, type FROM cards WHERE effect_logic = ? LIMIT 1");
    $stmt->execute([$effect]);
    $card = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$card) {
        throw new Exception("Card not found for effect: $effect");
    }

    $card_id = $card['id'];
    $type = $card['type'];

    // 2. Consume the Card (Delete 1 instance)
    // User requested "Active Skill" & "Sekali Pakai" (One-time use) for ALL cards including Jokers.
    $stmt = $pdo->prepare("DELETE FROM user_inventory WHERE user_id = ? AND card_id = ? LIMIT 1");
    $stmt->execute([$user_id, $card_id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'effect' => $effect]);
    } else {
        echo json_encode(['error' => 'Card not found in inventory']);
    }

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
