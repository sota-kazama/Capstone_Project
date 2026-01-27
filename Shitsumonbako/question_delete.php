<?php
require_once __DIR__ . '/../helpers/ShitumonDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: question_list.php');
    exit();
}

$shitu_number = $_POST['shitu_number'] ?? null;
$return_to    = $_POST['return_to'] ?? 'question_list.php';

if (!$shitu_number) {
    die('削除対象の質問番号が指定されていません。');
}

// DAO生成
$dao = new ShitumonDAO();

// 質問＋回答を削除
$dao->deleteWithAnswers((int)$shitu_number);

// 🔐 念のため外部URLを防ぐ（超重要）
if (!preg_match('/^\/[a-zA-Z0-9_\/\-\.]+$/', $return_to)) {
    $return_to = 'question_list.php';
}

// 削除元へ戻る
header('Location: ' . $return_to);
exit();
