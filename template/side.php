<?php
require_once __DIR__ . '/../helpers/config.php';

$current = basename($_SERVER['SCRIPT_NAME']); // 現在のページファイル名

$menus = [
    ['file' => '/problem/problem_top.php', 'label' => '問題', 'icon' => 'bi-check2-square'],
    ['file' => '/book.php',                     'label' => '書籍検索', 'icon' => 'bi-book'],
];

// ログイン時メニュー
if (isset($member)) {
    $menus = array_merge($menus, [
        ['file' => '/board/thread_list.php',               'label' => '掲示板',   'icon' => 'bi-person-fill'],
        ['file' => '/mypage/mypage.php',            'label' => 'マイページ', 'icon' => 'bi-card-list'],
        ['file' => '/Shitsumonbako/question_list.php', 'label' => '質問箱', 'icon' => 'bi-chat-left'],
    ]);
}
?>

<div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary" style="width: 280px; min-height:100vh;">
    <a href="<?= BASE_URL ?>/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none"></a>

    <ul class="nav nav-pills flex-column mb-auto">
        <?php foreach ($menus as $menu): 
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
