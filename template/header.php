<?php
// BASE_URL の定義と読み込み（共通設定ファイル）
require_once __DIR__ . '/../helpers/config.php'; // BASE_URL 読み込み

require_once __DIR__ . '/../helpers/MemberDAO.php';

// セッションが開始されていない場合のみ開始
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// セッションから会員情報を取得
$member = $_SESSION['member'] ?? null;

// 配列かオブジェクトどちらでも user_name を安全に取得
$userName = null;

if (is_array($member) && isset($member['user_name'])) {
    $userName = $member['user_name'];
} elseif (is_object($member) && isset($member->user_name)) {
    $userName = $member->user_name;
}

?>


<header>
    <?php include __DIR__ . '/hamburger.php'; ?>

    <a href="<?= BASE_URL ?>/index.php">
        <img src="<?= BASE_URL ?>/images/icon2.png" alt="サイトのロゴ" />
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