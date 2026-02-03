<?php
require_once __DIR__ . '/../helpers/config.php'; // BASE_URL

// 現在ページファイル名
$current = basename($_SERVER['SCRIPT_NAME']);

// セッションから会員情報取得
$member = $_SESSION['member'] ?? null;
$isAdmin = false;

if ($member !== null) {
    if (is_object($member) && isset($member->u_admin)) {
        $isAdmin = ($member->u_admin == 1);
    } elseif (is_array($member) && isset($member['u_admin'])) {
        $isAdmin = ($member['u_admin'] == 1);
    }
}

// メニュー配列
$menus = [
    ['file' => '/problem/problem_top.php', 'label' => '問題', 'icon' => 'bi-check2-square', 'admin' => false],
    ['file' => '/book.php', 'label' => '書籍検索', 'icon' => 'bi-book', 'admin' => false],
    ['file' => '/board/thread_list.php', 'label' => '掲示板', 'icon' => 'bi-person-fill', 'admin' => false],
    ['file' => '/mypage/mypage.php', 'label' => 'マイページ', 'icon' => 'bi-house', 'admin' => false],
    ['file' => '/Shitsumonbako/question_list.php', 'label' => '質問箱', 'icon' => 'bi-chat-left', 'admin' => false],
    ['file' => '/add/admin_main.php', 'label' => '管理者ページ', 'icon' => 'bi-tools', 'admin' => true],
];
?>

<link href="<?= BASE_URL ?>../css_theme/hamburger.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<!-- ハンバーガー -->
<div class="hamburger" id="hamburger">
    <span></span>
    <span></span>
    <span></span>
</div>

<!-- サイドバー / メニュー -->
<nav class="menu" id="menu">
    <ul class="nav flex-column">
        <?php foreach ($menus as $menu): ?>
            <?php
            // 管理者メニューは非表示
            if ($menu['admin'] && !$isAdmin) continue;

            $isActive = basename($menu['file']) === $current;
            ?>
            <li class="nav-item">
                <a href="<?= BASE_URL . $menu['file'] ?>" 
                   class="nav-link <?= $isActive ? 'active' : 'link-body-emphasis' ?>" 
                   <?= $isActive ? 'aria-current="page"' : '' ?>>
                    <i class="bi <?= htmlspecialchars($menu['icon'], ENT_QUOTES, 'UTF-8') ?>"></i>
                    <?= htmlspecialchars($menu['label'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>

<script>
const hamburger = document.getElementById("hamburger");
const menu = document.getElementById("menu");

hamburger.addEventListener("click", () => {
    hamburger.classList.toggle("active");
    menu.classList.toggle("active");
});
</script>
