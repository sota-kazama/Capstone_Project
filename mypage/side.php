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

// サイドメニュー配列
$menus = [
    ['file' => '/mypage/mypage.php',        'label' => 'マイページトップ', 'icon' => 'bi-square', 'admin' => false],
    ['file' => '/mypage/config_user.php',   'label' => 'アカウント設定',   'icon' => 'bi-gear',   'admin' => false],
    ['file' => '/mypage/goal.php',          'label' => '目標設定',       'icon' => 'bi-bullseye','admin' => false],
    ['file' => '/mypage/results.php',       'label' => '成果登録',       'icon' => 'bi-graph-up','admin' => false],
    ['file' => '/mypage/shitumon.php',      'label' => '質問箱管理',     'icon' => 'bi-question-circle','admin' => false],
    ['file' => '/add/admin_main.php',       'label' => '管理者ページ',   'icon' => 'bi-tools', 'admin' => true],
    ['file' => '/problem/problem_top.php', 'label' => '問題', 'icon' => 'bi-check2-square','admin' => false],
    ['file' => '/book.php',                     'label' => '書籍検索', 'icon' => 'bi-book','admin' => false],
    ['file' => '/board/thread_list.php',               'label' => '掲示板',   'icon' => 'bi-person-fill','admin' => false],
    ['file' => '/Shitsumonbako/question_list.php', 'label' => '質問箱', 'icon' => 'bi-chat-left','admin' => false],
];
?>

<div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary" style="width: 280px; min-height:100vh;">
    <ul class="nav nav-pills flex-column mb-auto">
        <?php foreach ($menus as $menu): ?>
            <?php
                // 管理者メニュー非表示
                if ($menu['admin'] && !$isAdmin) continue;

                $isActive = basename($menu['file']) === $current;
            ?>
            <li>
                <a href="<?= BASE_URL . $menu['file'] ?>" 
                   class="nav-link <?= $isActive ? 'active' : 'link-body-emphasis' ?>" 
                   <?= $isActive ? 'aria-current="page"' : '' ?>>
                    <i class="bi <?= htmlspecialchars($menu['icon'], ENT_QUOTES, 'UTF-8') ?>"></i>
                    <?= htmlspecialchars($menu['label'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
