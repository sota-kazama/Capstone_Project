<?php

require_once __DIR__ . '/../helpers/MemberDAO.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!empty($_SESSION['member'])) {
    $member = $_SESSION['member'];
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
        <?php include "template/hamburger.html"; ?>
        <a href="./index.php">
            <img src="images/icon2.png" alt="サイトのロゴ" />
        </a>

        <?php if (isset($member)) : ?>
            <?= $member->user_name ?>さん

            <a href="logout.php">ログアウト</a>
        <?php else : ?>
            <form action="login.php" method="post">
                <p id="login">
                    <input type="submit" value="ログイン" />
                </p>
            </form>
        <?php endif; ?>

    </header>
    <hr />
</body>

</html>