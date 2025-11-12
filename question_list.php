


<?php
require_once 'helpers/ShitumonDAO.php';

// DAO生成
$dao = new ShitumonDAO();

// 質問一覧を取得
$questions = $dao->getAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link href="css/BaseDesignData.css" rel="stylesheet" />
    <link href="css/side.css" rel="stylesheet" />
    <?php include 'template/header.php'; ?>
    
    
    <title>質問一覧</title>
</head>
<body>
    <div class="d-flex w-100 min-vh-100">
        <?php include 'template/side.php'; ?>
        <main class="main-content p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="bi bi-chat-square-text"></i> 質問一覧</h1>
                <a href="question_post.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> 新しい質問</a>
            </div>

            <div class="list-group">
                <?php if (empty($questions)): ?>
                    <p>まだ質問はありません。</p>
                <?php else: ?>
                    <?php foreach ($questions as $q): ?>
                        <a href="question_answer.php?shitu_number=<?= $q['shitu_number'] ?>" class="list-group-item list-group-item-action">
                            <h5 class="mb-1"><?= htmlspecialchars($q['shitu_content']) ?></h5>
                            <small class="text-muted">投稿日: <?= $q['update_at'] ?? '不明' ?></small>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

<footer>
    <?php include 'template/footer.php'; ?>
</footer>
</html>
