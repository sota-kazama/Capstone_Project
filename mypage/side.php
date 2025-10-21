<?php
$current = basename($_SERVER['PHP_SELF']);
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
    </ul>
</div>
