<?php
require_once __DIR__ . '/helpers/ShitumonDAO.php';

$dao = new ShitumonDAO();

$shitu_number = $_POST['shitu_number'] ?? null;
$ans_content = $_POST['ans_content'] ?? '';

if (!$shitu_number || !$ans_content) {
    die("入力が不正です。");
}

// DBに回答追加
$dao->addAnswer($shitu_number, $ans_content);

// 詳細ページに戻る
header("Location: question_answer.php?shitu_number={$shitu_number}");
exit;
