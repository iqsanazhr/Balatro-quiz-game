<?php
session_start();
include __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized or Invalid Request']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$score = isset($input['score']) ? intval($input['score']) : 0;

if ($score > 0) {
    try {
        $stmt = $pdo->prepare("UPDATE users SET chips_balance = chips_balance + ? WHERE id = ?");
        $stmt->execute([$score, $_SESSION['user_id']]);

        echo json_encode(['success' => true, 'new_balance' => $score]); // Just return success, balance could be refetched
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database update failed']);
    }
} else {
    echo json_encode(['success' => true, 'message' => 'No chips earned']);
}
?>