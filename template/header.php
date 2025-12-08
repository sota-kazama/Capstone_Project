<?php

require_once __DIR__ . '/../helpers/MemberDAO.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$member = $_SESSION['member'] ?? null;

// 配列かオブジェクトどちらでも user_name を安全に取得
$userName = null;

if (is_array($member) && isset($member['user_name'])) {
    $userName = $member['user_name'];
} elseif (is_object($member) && isset($member->user_name)) {
    $userName = $member->user_name;
}

?>
<!DOCTYPE html>
<html lang="jp">

<head>
    <meta charset="UTF-8" />
    <link href="../css/BaseDesignData.css" rel="stylesheet" />
</head>

<body>
    <header>
        <?php include "template/hamburger.php"; ?>
        <a href="index.php">
            <img src="images/icon2.png" alt="サイトのロゴ" />
        </a>

        <?php if ($userName !== null) : ?>
            <p id="logout">
                <?= htmlspecialchars($userName) ?> さん
                <a href="logout.php" class="logout-btn">ログアウト</a>
            </p>

        <?php else : ?>
            <form action="login.php" method="post">
                <p id="login">
                    <input type="submit" value="ログイン" class="login-btn1">
                </p>
            <?php endif; ?>
    </header>
    <hr />
</body>

</html>
