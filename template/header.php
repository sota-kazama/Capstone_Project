<?php
require_once __DIR__ . '/../helpers/config.php';
require_once __DIR__ . '/../helpers/MemberDAO.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$member = $_SESSION['member'] ?? null;

$userName = null;
if (is_array($member) && isset($member['user_name'])) {
    $userName = $member['user_name'];
} elseif (is_object($member) && isset($member->user_name)) {
    $userName = $member->user_name;
}

$isMypage = str_contains($_SERVER['SCRIPT_NAME'], '/mypage/');
?>

<header>

    <?php if ($isMypage) : ?>
        <?php include __DIR__ . '/../mypage/hamburger.php'; ?>

    <?php else : ?>
        <?php include __DIR__ . '/hamburger.php'; ?>
    <?php endif; ?>

    <a href="<?= BASE_URL ?>/index.php">
        <img src="<?= BASE_URL ?>/images/icon.png" alt="サイトのロゴ" />
    </a>

    <?php if ($userName !== null) : ?>
        <p id="logout">
            <?= htmlspecialchars($userName) ?> さん
            <a href="<?= BASE_URL ?>/auth/logout.php" class="logout-btn">ログアウト</a>
        </p>
    <?php else : ?>
        <form action="<?= BASE_URL ?>/auth/login.php" method="post">
            <p id="login">
                <input type="submit" value="ログイン" class="login-btn1" />
            </p>
        </form>
    <?php endif; ?>
</header>
<hr />
