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
    <title>質問詳細と回答</title>
</head>

<body>
    <div class="d-flex w-100 min-vh-100">
        <?php include 'template/side.php'; ?>

        <main class="main-content p-4">
            <h1 class="mb-4"><i class="bi bi-question-circle"></i> 質問詳細</h1>

            <!-- 質問内容表示エリア -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Q. PHPでフォームを作る方法を知りたい</h5>
                </div>
                <div class="card-body">
                    <p>PHPを使って入力フォームを作りたいのですが、POSTで受け取る処理がよく分かりません。サンプルがあれば教えてください。</p>
                    <p class="text-muted small mb-0">投稿者：ユーザーA　投稿日：2025/11/10</p>
                </div>
            </div>

            <!-- 回答リスト -->
            <h4 class="mb-3"><i class="bi bi-chat-left-text"></i> 回答</h4>
            <div class="list-group mb-4">
                <div class="list-group-item">
                    <p>PHPではフォームのデータを `$_POST` で受け取ります。<br>例えば次のように書きます：</p>
                    <pre><code>&lt;?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    echo "こんにちは、" . htmlspecialchars($name) . "さん！";
}
?&gt;
</code></pre>
                    <p class="text-muted small">回答者：ユーザーB　投稿日：2025/11/10</p>
                </div>
            </div>

            <!-- 回答投稿フォーム -->
            <h4 class="mb-3"><i class="bi bi-pencil-square"></i> 回答を投稿する</h4>
            <form action="answer_post.php" method="post" class="needs-validation" novalidate>
                <div class="mb-3">
                    <textarea class="form-control" name="answer" rows="5" placeholder="あなたの回答を入力してください" required></textarea>
                    <div class="invalid-feedback">回答内容を入力してください。</div>
                </div>
                <button type="submit" class="btn btn-success"><i class="bi bi-send"></i> 回答を投稿</button>
            </form>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (() => {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>

<footer>
    <?php include 'template/footer.php'; ?>
</footer>

</html>