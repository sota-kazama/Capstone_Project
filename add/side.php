<?php
// 現在のページファイル名を取得
$current = basename($_SERVER['PHP_SELF']);
?>

<div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary"
    style="width: 280px; height: 1617px;">

    <ul class="nav nav-pills flex-column mb-auto">
        <li>
            <a href="../mypage.php"
                class="nav-link <?= $current === 'mypage.php' ? 'active' : 'link-body-emphasis' ?>">
                マイページトップ
            </a>
        </li>

        <li>
            <a href="admin_main.php"
                class="nav-link <?= $current === 'admin_main.php' ? 'active' : 'link-body-emphasis' ?>">
                管理者トップ
            </a>
        </li>

        <li>
            <a href="edit_account.php"
                class="nav-link <?= $current === 'edit_account.php' ? 'active' : 'link-body-emphasis' ?>">
                アカウント情報編集
            </a>
        </li>

        <li>
            <a href="shikaku_manage.php"
                class="nav-link <?= $current === 'shikaku_manage.php' ? 'active' : 'link-body-emphasis' ?>">
                資格管理ページ
            </a>
        </li>

        <li>
            <a href="../logout.php"
                class="nav-link <?= $current === '../logout.php' ? 'active' : 'link-body-emphasis' ?>">
                ログアウト
            </a>
        </li>
    </ul>
</div>
