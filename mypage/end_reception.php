<?php
require_once '../helpers/ShitumonDAO.php';

session_start();

// 未ログインの場合
if (!isset($_SESSION['member'])) {
    header('Location: ../auth/login.php');
    exit;
}

// ShitumonDAOインスタンスを生成
$shitumonDAO = new ShitumonDAO();

// URLパラメータから質問番号を取得
$shitu_number = isset($_GET['shitu_number']) ? $_GET['shitu_number'] : 0;

// 質問の受付状態を「受付終了」に更新
if ($shitu_number > 0) {
    // 質問の受付状態を変更
    $shitumonDAO->updateReceptionStatus($shitu_number, 0); // 0は「受付終了」を意味する状態
    header('Location: mypage.php'); // 処理後にマイページにリダイレクト
    exit;
} else {
    // 質問番号が無効の場合、エラー画面にリダイレクト
    header('Location: error.php');
    exit;
}
?>
