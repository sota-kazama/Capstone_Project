<?php
require_once '../helpers/ShitumonDAO.php';
require_once '../helpers/DAO.php';

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

// 質問一覧取得
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="../css/BaseDesignData.css" rel="stylesheet" />
    <link href="../css/side.css" rel="stylesheet" />

    <?php include '../template/header.php'; ?>
    <title>質問一覧</title>
</head>

<body>
    <div class="d-flex w-100 min-vh-100">
        <?php include '../template/side.php'; ?>
        <main class="main-content p-4">
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

            <div class="list-group">
                <?php if (empty($questions)): ?>
                    <p>まだ質問はありません。</p>
                    <?php else: ?>
                    <?php foreach ($questions as $q): ?>
                        <?php if ($q->shitu_title && $q->shitu_content): ?>
                            <a href="question_answer.php?shitu_number=<?= htmlspecialchars($q->shitu_number) ?>"
                                class="list-group-item list-group-item-action mb-2">
                                <div class="d-flex justify-content-between">
                                    <h5><?= htmlspecialchars($q->shitu_title) ?></h5>
                                    <?php if (!empty($q->area_name)): ?>
                                        <small class="text-muted"><?= htmlspecialchars($q->area_name) ?></small>
                                    <?php endif; ?>
                                </div>
                                <p class="mb-1"><?= nl2br(htmlspecialchars($q->shitu_content)) ?></p>
                                <small class="text-muted">
                                    投稿日: <?= $q->update_at ?? $q->asked_date ? date("Y-m-d H:i:s", strtotime($q->update_at ?? $q->asked_date)) : '不明' ?>
                                </small>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- ページネーション -->
            <?php if ($totalPages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="?area_number=<?= htmlspecialchars($area_number) ?>&order=<?= $order ?>&page=<?= $i ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

 <footer>
        <?php include '../template/footer.php'; ?>
    </footer>

</html>
