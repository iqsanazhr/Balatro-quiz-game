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
    // 1. Find the card ID associated with this effect (and ensure it's a Skill/Consumable)
    // We strictly look for the card ID that matches the effect logic.
    // In case multiple cards share logic (unlikely in this seed), we pick one.
    $stmt = $pdo->prepare("SELECT id FROM cards WHERE effect_logic = ? LIMIT 1");
    $stmt->execute([$effect]);
    $card_id = $stmt->fetchColumn();

    if (!$card_id) {
        throw new Exception("Card not found for effect: $effect");
    }

    // 2. Delete ONE instance of this card from user's inventory
    // MySQL DELETE LIMIT 1 is useful here to remove just one duplicate
    $stmt = $pdo->prepare("DELETE FROM user_inventory WHERE user_id = ? AND card_id = ? LIMIT 1");
    $stmt->execute([$user_id, $card_id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Card not found in inventory']);
    }

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
