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
</head>

<head>
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

            質問一覧
            <!-- <div class="list-group"> -->
                <!-- ↓ 実際はDBからループで表示する -->
                <!-- <a href="question_answer.php?id=1" class="list-group-item list-group-item-action">
                    <h5 class="mb-1">PHPでフォームを作る方法を知りたい</h5>
                    <p class="mb-1 text-muted">投稿者：ユーザーA　投稿日：2025/11/10</p>
                </a> -->

                <!-- <a href="question_answer.php?id=2" class="list-group-item list-group-item-action">
                    <h5 class="mb-1">JavaScriptでアニメーションを作るには？</h5>
                    <p class="mb-1 text-muted">投稿者：ユーザーB　投稿日：2025/11/09</p>
                </a> -->
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

<footer>
    <?php include 'template/footer.php'; ?>
</footer>
</html>
