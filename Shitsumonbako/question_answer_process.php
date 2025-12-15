<?php
require_once __DIR__ . '/helpers/ShitumonDAO.php';

$dao = new ShitumonDAO();

// POSTパラメータ取得
$shitu_number = $_POST['shitu_number'] ?? null;
$ans_content = trim($_POST['ans_content'] ?? '');

// バリデーション
if (!$shitu_number) {
    die("質問番号が指定されていません。");
}
if ($ans_content === '') {
    die("回答内容を入力してください。");
}

// DBに回答追加
try {
    $dao->addAnswer($shitu_number, $ans_content);
} catch (PDOException $e) {
    die("回答の追加に失敗しました: " . htmlspecialchars($e->getMessage()));
}

// 詳細ページにリダイレクト
header("Location: question_answer.php?shitu_number=" . urlencode($shitu_number));
exit;
