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
        <li><a href="<?= BASE_URL ?>/problem/problem.php">問題</a></li>
        <li><a href="<?= BASE_URL ?>/book.php">書籍検索</a></li>
        <?php if (isset($member)) : ?>
            <li><a href="<?= BASE_URL ?>/board.php">掲示板</a></li>
            <li><a href="<?= BASE_URL ?>/mypage.php">マイページ</a></li>
            <li><a href="<?= BASE_URL ?>/Shitsumonbako/question_list.php">質問箱</a></li>
        <?php endif; ?>
    </ul>
</nav>

<!-- 赤表示用スタイル追加 -->


<script>
const hamburger = document.getElementById("hamburger");
const menu = document.getElementById("menu");

hamburger.addEventListener("click", () => {
    hamburger.classList.toggle("active");
    menu.classList.toggle("active");
});
</script>
