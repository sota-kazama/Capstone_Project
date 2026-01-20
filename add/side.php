<?php
// 現在のページ名を取得（サブディレクトリ対応）
$current = basename($_SERVER['SCRIPT_NAME']);

// サイドメニュー定義
$menus = [
    ['file' => 'admin_main.php',        'label' => '管理者トップ'],
    ['file' => 'edit_account.php',      'label' => 'アカウント情報編集'],
    ['file' => 'create_book.php',       'label' => '図書登録'],
    ['file' => 'shikaku_manage.php',    'label' => '資格登録'],
    ['file' => 'm_problem_register.php','label' => '問題管理'],
    ['file' => 's_problem_register.php','label' => '問題分野管理'],
    ['file' => 'shitumon.php',          'label' => '質問箱管理'],
    ['file' => 'update_info.php',       'label' => '更新情報'],
    ['file' => 'bug_manage.php',       'label' => 'バグ記載'],
    ['file' => '../mypage/mypage.php',  'label' => 'マイページトップ'],
];
?>

<div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary"
     style="width:280px; min-height:100vh;">

    <ul class="nav nav-pills flex-column mb-auto">

        <?php foreach ($menus as $menu): ?>
            <?php
                $isActive = basename($menu['file']) === $current;
            ?>
            <li class="nav-item">
                <a href="<?= htmlspecialchars($menu['file'], ENT_QUOTES, 'UTF-8') ?>"
                   class="nav-link <?= $isActive ? 'active' : 'link-body-emphasis' ?>"
                   <?= $isActive ? 'aria-current="page"' : '' ?>>
                    <?= htmlspecialchars($menu['label'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            </li>
        <?php endforeach; ?>

        <li class="nav-item mt-3">
            <a href="../logout.php"
               class="nav-link link-body-emphasis"
               onclick="return confirm('本当にログアウトしますか？');">
                ログアウト
            </a>
        </li>

    </ul>
</div>
