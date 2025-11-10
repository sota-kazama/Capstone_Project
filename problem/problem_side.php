<?php
$current = basename($_SERVER['PHP_SELF']);
$member = $_SESSION['member'] ?? null;
?>

<div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary" style="width: 280px; height: 1617px;">
    <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
    </a>
    <ul class="nav nav-pills flex-column mb-auto">
        <li>
            <a href="./problem.php" class="nav-link <?= $current === 'problem.php' ? 'active' : 'link-body-emphasis' ?>">
                問題トップ
            </a>
        </li>
        <li>
            <a href="problem_response.php" class="nav-link <?= $current === 'problem_response.php' ? 'active' : 'link-body-emphasis' ?>">
                問題回答
            </a>
        </li>
        <li>
            <a href="problem_review.php" class="nav-link <?= $current === 'problem_review.php' ? 'active' : 'link-body-emphasis' ?>">
                問題復習
            </a>
        </li>

</div>
