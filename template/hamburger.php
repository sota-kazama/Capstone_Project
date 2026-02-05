<?php
require_once __DIR__ . '/../helpers/config.php';

$current = basename($_SERVER['SCRIPT_NAME']);

$menus = [
    ['file' => '/problem/problem_top.php', 'label' => '問題'],
    ['file' => '/book.php', 'label' => '書籍検索'],
];

if (isset($member)) {
    $menus = array_merge($menus, [
        ['file' => '/board/thread_list.php', 'label' => '掲示板'],
        ['file' => '/mypage/mypage.php', 'label' => 'マイページ'],
        ['file' => '/Shitsumonbako/question_list.php', 'label' => '質問箱'],
    ]);
}
?>

<link href="<?= BASE_URL ?>/css_theme/hamburger.css" rel="stylesheet" />

<!-- ハンバーガー -->
<div class="hamburger" id="hamburger">
    <span></span>
    <span></span>
    <span></span>
</div>

<!-- メニュー -->
<nav class="menu" id="menu">
    <ul>
        <?php foreach ($menus as $menu): 
            $isActive = basename($menu['file']) === $current;
        ?>
            <li>
                <a href="<?= BASE_URL . $menu['file'] ?>"
                   class="<?= $isActive ? 'active' : '' ?>">
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
