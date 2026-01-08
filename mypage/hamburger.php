<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$member = $_SESSION['member'] ?? null;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>マイページ</title>
<!-- CSSを別ファイル -->
<link href="../css/hamburger.css" rel="stylesheet">
</head>
<body>

<!-- ハンバーガーボタン -->
<div class="hamburger" id="hamburger">
    <span></span>
    <span></span>
    <span></span>
</div>

<!-- メニュー -->
<nav class="menu" id="menu">
    <ul>
        <?php if ($member) : ?>
        <!-- マイページメニューを最上部に表示 -->
        <li>
            <a href="#">マイページ</a>
            <span class="arrow"></span>
            <ul class="submenu">
                <li><a href="./mypage.php">マイページトップ</a></li>
                <li><a href="./config_user.php">アカウント設定</a></li>
                <li><a href="./goal.php">目標設定</a></li>
                <li><a href="./results.php">成果登録</a></li>
                <li><a href="./shitumon.php">質問箱の管理</a></li>
            </ul>
        </li>
        <?php endif; ?>
        <!-- 通常メニュー -->
        <li><a href="../problem/problem.php">問題</a></li>
        <li><a href="../book.php">書籍検索</a></li>
        <?php if ($member) : ?>
            <li><a href="../board.php">掲示板</a></li>
            <li><a href="../question_list.php">質問箱</a></li>
        <?php endif; ?>
    </ul>
</nav>

<!-- JSを別ファイル -->
<script src="../js/hamburger.js"></script>
</body>
</html>
