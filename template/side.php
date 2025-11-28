<?php
$current = basename($_SERVER['PHP_SELF']);
?>

<div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary" style="width: 280px; height: 1617px;">
    <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
    </a>
    <ul class="nav nav-pills flex-column mb-auto">
        <li>
            <a href="problem/problem.php" class="nav-link <?= $current === 'problem.php' ? 'active' : 'link-body-emphasis' ?>">
                <i class="bi bi-check2-square"></i>
                問題
            </a>
        </li>
        <li>
            <a href="book.php" class="nav-link <?= $current === 'book.php' ? 'active' : 'link-body-emphasis' ?>">
                <i class="bi bi-book"></i>
                書籍検索
            </a>
        </li>
        <li>
            <?php if(isset($member)) : ?>
                <a href="board.php" class="nav-link <?= $current === 'board.php' ? 'active' : 'link-body-emphasis' ?>">
                    <i class="bi bi-person-fill"></i>
                掲示板
                </a>
                <a href="mypage.php" class="nav-link <?= $current === 'mypage.php' ? 'active' : 'link-body-emphasis' ?>">
                    <i class="bi bi-card-list"></i>
                マイページ

                <a href="question_list.php" class="nav-link <?= $current === 'mypage.php' ? 'active' : 'link-body-emphasis' ?>">

                    <i class="bi bi-chat-left"></i>
                質問箱
                </a>
            <?php endif; ?>
        </li>
    </ul>
</div>
