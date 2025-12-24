<?php
require_once '../helpers/ShitumonDAO.php';

session_start();
header('Content-Type: application/json');

// 未ログインチェック
if (!isset($_SESSION['member'])) {
    echo json_encode([
        'success' => false,
        'message' => '未ログイン'
    ]);
    exit;
}

// POST以外は拒否
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => '不正なリクエスト'
    ]);
    exit;
}

// action チェック
if (!isset($_POST['action']) || $_POST['action'] !== 'delete') {
    echo json_encode([
        'success' => false,
        'message' => '不正な操作'
    ]);
    exit;
}

// shitu_number チェック
if (!isset($_POST['shitu_number'])) {
    echo json_encode([
        'success' => false,
        'message' => '質問番号がありません'
    ]);
    exit;
}

$shitu_number = (int)$_POST['shitu_number'];

try {
    $dao = new ShitumonDAO();

    // ★ 回答も含めて削除（外部キー対策）
    $result = $dao->deleteWithAnswers($shitu_number);

    echo json_encode([
        'success' => $result
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
