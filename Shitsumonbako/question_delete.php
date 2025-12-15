<?php
require_once __DIR__ . '/helpers/ShitumonDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: question_list.php');
    exit();
}

$shitu_number = $_POST['shitu_number'] ?? null;

if (!$shitu_number) {
    die("削除対象の質問番号が指定されていません。");
}

// DAO生成
$dao = new ShitumonDAO();

// 質問と回答をまとめて削除
$dao->deleteWithAnswers((int)$shitu_number);

// 削除後は一覧ページにリダイレクト
header('Location: question_list.php');
exit();
