<?php
require_once __DIR__ . '/../helpers/ShitumonDAO.php';
require_once __DIR__ . '/../helpers/MemberDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: question_list.php');
    exit();
}

session_start();
$member = $_SESSION['member'] ?? null;

// 管理者判定
$is_admin = false;
if (is_object($member) && isset($member->u_admin)) {
    $is_admin = ($member->u_admin == 1);
} elseif (is_array($member) && isset($member['u_admin'])) {
    $is_admin = ($member['u_admin'] == 1);
}

// DAO生成
$shitumonDAO = new ShitumonDAO();

$shitu_number = $_POST['shitu_number'] ?? null;
$return_to    = $_POST['return_to'] ?? 'question_list.php';

if (!$shitu_number) {
    die('削除対象の質問番号が指定されていません。');
}

// 削除対象の質問を取得
$q = $shitumonDAO->getByNumber((int)$shitu_number);

if (!$q) {
    die('質問が存在しません。');
}

// ログインユーザーID取得
$login_user_id = $member->user_id ?? null;

// 投稿者本人または管理者でなければNG
if ((int)$q->user_id !== (int)$login_user_id && !$is_admin) {
    die('この質問を削除する権限がありません。');
}

// 質問＋回答を削除
$shitumonDAO->deleteWithAnswers((int)$shitu_number);

// 🔐 念のため外部URLを防ぐ
if (!preg_match('/^\/[a-zA-Z0-9_\/\-\.]+$/', $return_to)) {
    $return_to = 'question_list.php';
}

// 削除元へ戻る
header('Location: ' . $return_to);
exit();
