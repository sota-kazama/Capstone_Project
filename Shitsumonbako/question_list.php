<?php
require_once '../helpers/ShitumonDAO.php';
require_once '../helpers/DAO.php';

// PHPでテーマ取得（Cookieが無ければlight）
$theme = $_COOKIE['theme'] ?? 'light';

// DAO生成
$dao = new ShitumonDAO();

// 分野・並び順・ページ取得
$area_number = $_GET['area_number'] ?? '';
$order       = $_GET['order'] ?? 'DESC';
$order       = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';
$page        = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$perPage     = 10; // 1ページ10件

// 分野一覧取得
$stmt = DAO::get_db_connect()->query("SELECT area_number, area_name FROM q_categories ORDER BY area_name ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 固定表示する質問（shitu_number=131）
$fixedQuestion = $dao->getByNumber(131);

// 質問一覧取得（固定表示を除外）
$questions = $dao->getAllByAreaOrderPage($area_number, $order, $page, $perPage);

// 総件数
$totalCount = $dao->getCountByArea($area_number);
$totalPages = ceil($totalCount / $perPage);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="../css_theme/base.css" rel="stylesheet">
    <link href="../css_theme/side.css" rel="stylesheet">
    <link
        id="theme-css"
        href="../css_theme/<?= htmlspecialchars($theme) ?>.css"
        rel="stylesheet"
    >
    <link href="../css_theme/toggle-button.css" rel="stylesheet">
    <title>質問一覧</title>
</head>

<body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">
<?php include '../template/header.php'; ?>

<div class="d-flex w-100 min-vh-100">
    <div class="d-none d-md-block">
        <?php include '../template/side.php'; ?>
    </div>

    <main class="main-content p-4 d-flex flex-column">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>質問一覧</h1>
            <a href="question_post.php" class="btn btn-primary">新しい質問</a>
        </div>

        <!-- 並び順・分野選択 -->
        <form method="get" class="mb-4 d-flex gap-2">
            <select name="area_number" class="form-select" onchange="this.form.submit()">
                <option value="">全分野</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat['area_number']) ?>" <?= $cat['area_number'] == $area_number ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['area_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="order" class="form-select" onchange="this.form.submit()">
                <option value="DESC" <?= $order === 'DESC' ? 'selected' : '' ?>>新しい順</option>
                <option value="ASC" <?= $order === 'ASC' ? 'selected' : '' ?>>古い順</option>
            </select>
        </form>

        <div class="d-flex flex-column gap-2">

            <!-- 固定表示（shitu_number=131） -->
            <?php if ($fixedQuestion): ?>
                <a href="question_answer.php?shitu_number=<?= htmlspecialchars($fixedQuestion->shitu_number) ?>"
                   class="list-group-item list-group-item-action mb-2 border border-primary p-3 rounded shadow-sm text-decoration-none">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">
                            <?= htmlspecialchars($fixedQuestion->shitu_title) ?>
                            <?php if ($fixedQuestion->shitu_count > 0): ?>
                                <span class="badge bg-primary ms-2">
                                    <i class="bi bi-check-circle"></i> 回答済み (<?= $fixedQuestion->shitu_count ?>件)
                                </span>
                            <?php endif; ?>
                        </h5>
                        <?php if (!empty($fixedQuestion->area_name)): ?>
                            <small class="text-muted"><?= htmlspecialchars($fixedQuestion->area_name) ?></small>
                        <?php endif; ?>
                    </div>
                    <p class="mb-1"><?= nl2br(htmlspecialchars($fixedQuestion->shitu_content)) ?></p>
                    <small class="text-muted">
                        投稿日: <?= !empty($fixedQuestion->update_at ?? $fixedQuestion->asked_date)
                            ? date("Y-m-d H:i:s", strtotime($fixedQuestion->update_at ?? $fixedQuestion->asked_date))
                            : '不明' ?>
                    </small>
                </a>
            <?php endif; ?>

            <!-- 通常の質問一覧 -->
            <?php if (empty($questions)): ?>
                <div class="alert alert-info">まだ質問はありません。</div>
            <?php else: ?>
                <?php foreach ($questions as $q): ?>
                    <?php if ($q->shitu_number != 131): ?>
                        <a href="question_answer.php?shitu_number=<?= htmlspecialchars($q->shitu_number) ?>"
                           class="list-group-item list-group-item-action mb-2 p-3 rounded shadow-sm text-decoration-none">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0">
                                    <?= htmlspecialchars($q->shitu_title) ?>
                                    <?php if ($q->shitu_count > 0): ?>
                                        <span class="badge bg-primary ms-2">
                                            <i class="bi bi-check-circle"></i> 回答済み (<?= $q->shitu_count ?>件)
                                        </span>
                                    <?php endif; ?>
                                </h5>
                                <?php if (!empty($q->area_name)): ?>
                                    <small class="text-muted"><?= htmlspecialchars($q->area_name) ?></small>
                                <?php endif; ?>
                            </div>
                            <p class="mb-1"><?= nl2br(htmlspecialchars($q->shitu_content)) ?></p>
                            <small class="text-muted">
                                投稿日: <?= !empty($q->update_at ?? $q->asked_date)
                                    ? date("Y-m-d H:i:s", strtotime($q->update_at ?? $q->asked_date))
                                    : '不明' ?>
                            </small>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>

        <!-- ページネーション -->
        <?php if ($totalPages > 1): ?>
            <ul class="pagination justify-content-center mt-4">
                <!-- Previous -->
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <?php if ($page <= 1): ?>
                        <span class="page-link">Previous</span>
                    <?php else: ?>
                        <a class="page-link" href="?area_number=<?= htmlspecialchars($area_number) ?>&order=<?= $order ?>&page=<?= $page - 1 ?>">Previous</a>
                    <?php endif; ?>
                </li>

                <!-- ページ番号 -->
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>" <?= $i == $page ? 'aria-current="page"' : '' ?>>
                        <?php if ($i == $page): ?>
                            <span class="page-link"><?= $i ?></span>
                        <?php else: ?>
                            <a class="page-link" href="?area_number=<?= htmlspecialchars($area_number) ?>&order=<?= $order ?>&page=<?= $i ?>"><?= $i ?></a>
                        <?php endif; ?>
                    </li>
                <?php endfor; ?>

                <!-- Next -->
                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                    <?php if ($page >= $totalPages): ?>
                        <span class="page-link">Next</span>
                    <?php else: ?>
                        <a class="page-link" href="?area_number=<?= htmlspecialchars($area_number) ?>&order=<?= $order ?>&page=<?= $page + 1 ?>">Next</a>
                    <?php endif; ?>
                </li>
            </ul>
        <?php endif; ?>

    </main>
</div>

<!-- テーマ切替ボタン -->
<button id="theme-toggle-btn" class="btn btn-primary theme-toggle-btn">
    <i id="theme-icon" class="bi <?= $theme === 'dark' ? 'bi-sun' : 'bi-moon' ?>"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/theme-toggle.js"></script>

<footer>
    <?php include '../template/footer.php'; ?>
</footer>

</body>
</html>
