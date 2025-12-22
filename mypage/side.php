<?php
require_once __DIR__ . '/../helpers/config.php'; // BASE_URL 読み込み

// 現在のページファイル名
$current = basename($_SERVER['PHP_SELF']);

// セッションから会員情報取得（配列 or オブジェクト両対応）
$member = $_SESSION['member'] ?? null;
$isAdmin = false;

if ($member !== null) {
    if (is_object($member) && isset($member->u_admin)) {
        $isAdmin = ($member->u_admin == 1);
    } elseif (is_array($member) && isset($member['u_admin'])) {
        $isAdmin = ($member['u_admin'] == 1);
    }
}
?>

<div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary" style="width: 280px; height: 1617px">
    <ul class="nav nav-pills flex-column mb-auto">
        <!-- マイページ -->
        <li>
            <a
                href="<?= BASE_URL ?>/mypage/mypage.php"
                class="nav-link <?= $current === 'mypage.php' ? 'active' : 'link-body-emphasis' ?>"
            >
                <i class="bi bi-square"></i>
                マイページトップ
            </a>
        </li>

        <!-- アカウント設定 -->
        <li>
            <a
                href="<?= BASE_URL ?>/mypage/config_user.php"
                class="nav-link <?= $current === 'config_user.php' ? 'active' : 'link-body-emphasis' ?>"
            >
                <i class="bi bi-gear"></i>
                アカウント設定
            </a>
        </li>

        <!-- 目標設定 -->
        <li>
            <a
                href="<?= BASE_URL ?>/mypage/goal.php"
                class="nav-link <?= $current === 'goal.php' ? 'active' : 'link-body-emphasis' ?>"
            >
                <i class="bi bi-bullseye"></i>
                目標設定
            </a>
        </li>

        <!-- 成果登録 -->
        <li>
            <a
                href="<?= BASE_URL ?>/mypage/results.php"
                class="nav-link <?= $current === 'results.php' ? 'active' : 'link-body-emphasis' ?>"
            >
                <i class="bi bi-graph-up"></i>
                成果登録
            </a>
        </li>

        <!-- 質問箱管理機能 -->
        <li>
            <a
                href="<?= BASE_URL ?>/mypage/shitumon.php"
                class="nav-link <?= $current === 'shitumon.php' ? 'active' : 'link-body-emphasis' ?>"
            >
                <i class="bi bi-question-circle"></i>
                質問箱管理
            </a>
        </li>

        <!-- 管理者メニュー -->
        <?php if ($isAdmin) : ?>
        <li>
            <a
                href="<?= BASE_URL ?>/add/admin_main.php"
                class="nav-link <?= $current === 'admin_main.php' ? 'active' : 'link-body-emphasis' ?>"
            >
                <i class="bi bi-tools"></i>
                管理者ページ
            </a>
        </li>
        <?php endif; ?>
    </ul>
</div>
