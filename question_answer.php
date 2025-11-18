<?php
require_once __DIR__ . '/helpers/ShitumonDAO.php';

$dao = new ShitumonDAO();

// GETパラメータ取得
$shitu_number = $_GET['shitu_number'] ?? null;

if (!$shitu_number) {
    die("質問番号が指定されていません。");
}

// DBから質問取得
$q = $dao->getByNumber($shitu_number);

if (!$q) {
    die("指定された質問が見つかりません。");
}

// 回答一覧を取得
$answers = $dao->getAnswers($shitu_number);
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
    <title>質問詳細</title>
</head>

<body>
    <di class="d-flex w-100 min-vh-100">
        <?php include 'template/side.php'; ?>
        <main class="main-content p-4">
            <h1><i class="bi bi-chat-dots"></i> 質問詳細</h1>

            <!-- 質問内容 -->
            <div class="card mt-4">
                <div class="card-body">
                    <h3 class="card-title"><?= nl2br(htmlspecialchars($q['shitu_content'])) ?></h3>
                    <p class="text-muted mt-3">
                        投稿日：
                        <?php
                        if (!empty($q['update_at'])) {
                            echo date("Y-m-d H:i:s", strtotime($q['update_at']));
                        } elseif (!empty($q['asked_date'])) {
                            echo date("Y-m-d H:i:s", strtotime($q['asked_date']));
                        } else {
                            echo '不明';
                        }
                        ?>
                    </p>
                </div>
            </div>


            <!-- 回答一覧 -->
            <div class="mt-4">
                <h4>回答</h4>
                <?php if (empty($answers)): ?>
                    <p>まだ回答はありません。</p>
                <?php else: ?>
                    <?php foreach ($answers as $a): ?>
                        <div class="card mb-2">
                            <div class="card-body">
                                <p><?= nl2br(htmlspecialchars($a['ans_content'])) ?></p>

                                <small class="text-muted">投稿日: <?= isset($a['update_at']) ? date("Y-m-d H:i:s", strtotime($a['update_at'])) : ($a['answer_date'] ?? '不明') ?></small>


                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- 回答フォーム -->
            <div class="mt-4">
                <h5>回答する</h5>
                <form action="question_answer_process.php" method="post">
                    <input type="hidden" name="shitu_number" value="<?= $shitu_number ?>">
                    <div class="mb-3">
                        <textarea class="form-control" name="ans_content" rows="4" placeholder="回答内容を入力してください" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> 回答する</button>
                </form>
            </div>

            <a href="question_list.php" class="btn btn-secondary mt-3">一覧に戻る</a>
        </main>
    </di

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js">
    </script>
</body>

</html>