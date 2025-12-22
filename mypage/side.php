<?php
$current = basename($_SERVER['PHP_SELF']);
$member = $_SESSION['member'] ?? null;
?>

<div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary" style="width: 280px; height: 1617px;">
    <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
    </a>
    <ul class="nav nav-pills flex-column mb-auto">
        <li>
            <a href="./mypage.php" class="nav-link <?= $current === 'mypage.php' ? 'active' : 'link-body-emphasis' ?>">
                <i class="bi bi-square"></i>
                マイページトップ
            </a>
        </li>
        <li>
            <a href="setting.php" class="nav-link <?= $current === 'setting.php' ? 'active' : 'link-body-emphasis' ?>">
                <i class="bi bi-book"></i>
                アカウント設定
            </a>
        </li>
        <li>
            <a href="goal.php" class="nav-link <?= $current === 'goal.php' ? 'active' : 'link-body-emphasis' ?>">
                <i class="bi bi-book"></i>
                目標設定
            </a>
        </li>
        <li>
            <a href="results.php" class="nav-link <?= $current === 'results.php' ? 'active' : 'link-body-emphasis' ?>">
                <i class="bi bi-book"></i>
                成果登録
            </a>
        </li>
        <?php if ($member !== null && $member->u_admin == 1) : ?>
    <li>
        <a href="./add/admin_main.php" class="nav-link <?= $current === 'admin.php' ? 'active' : 'link-body-emphasis' ?>">
            <i class="bi bi-gear"></i>
            管理者ページ
        </a>
    </li>
<?php endif; ?>

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
