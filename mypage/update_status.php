<?php
require_once '../helpers/ShitumonDAO.php';

session_start();

if (!isset($_SESSION['member'])) {
    echo json_encode(['success' => false, 'message' => 'ログインが必要です']);
    exit;
}

$shitumonDAO = new ShitumonDAO();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $shitu_number = $_POST['shitu_number'] ?? 0;

    // デバッグ用ログ
    error_log("Action: $action, Shitu Number: $shitu_number");

    if ($shitu_number > 0) {
        if ($action === 'end_reception') {
            // 受付終了
            $shitumonDAO->updateReceptionStatus($shitu_number, 0); // 0は「受付終了」
            error_log("Reception status updated to 'closed' for question number: $shitu_number");
            echo json_encode(['success' => true]);
            exit;
        }
    }
}

echo json_encode(['success' => false, 'message' => '無効なリクエスト']);
exit;
?>
