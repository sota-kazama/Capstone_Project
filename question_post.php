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
        <title>質問投稿</title>
    </head>

    <body>
        <div class="d-flex w-100 min-vh-100">
            <?php include 'template/side.php'; ?>

            <main class="main-content p-4">
                <h1 class="mb-4"><i class="bi bi-chat-dots"></i> 質問投稿</h1>

                <form action="question_confirm.php" method="post" class="needs-validation" novalidate>

                    <div class="mb-3">
                        <label for="title" class="form-label">質問タイトル</label>
                        <input type="text" class="form-control" id="title" name="title"
                            placeholder="例：PHP" required />
                        <div class="invalid-feedback">タイトルを入力してください。</div>
                    </div>
                    

                    <div class="mb-3">
                        <label for="category" class="form-label">分野</label>
                        <input type="text" class="form-control" id="category" name="category"
                            placeholder="例：プログラミング" required />
                        <div class="invalid-feedback">分野を入力してください。</div>
                    </div>

                  

                    <div class="mb-3">
                        <label for="content" class="form-label">質問内容</label>
                        <textarea class="form-control" id="content" name="content" rows="6"
                            placeholder="質問内容を詳しく記載してください。" required></textarea>
                        <div class="invalid-feedback">質問内容を入力してください。</div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send"></i> 投稿する
                    </button>
                    <button type="reset" class="btn btn-secondary">リセット</button>
                </form>
            </main>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Bootstrapのバリデーション
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
