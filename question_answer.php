<?php
require_once __DIR__ . '/helpers/ShitumonDAO.php';
require_once __DIR__ . '/helpers/AnswerDAO.php';

// GETで質問番号を取得
$shitu_number = $_GET['id'] ?? null;

if (!$shitu_number) {
    header('Location: question_list.php');
    exit();
}

// DAO生成
$questionDAO = new ShitumonDAO();
$answerDAO = new AnswerDAO();

// 質問詳細取得
$question = $questionDAO->getByNumber($shitu_number);

// 回答一覧取得
$answers = $answerDAO->getByQuestionNumber($shitu_number);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>質問詳細</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

<h2>質問内容</h2>
<div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title"><?= htmlspecialchars($question['shitu_content']) ?></h5>
        <p class="card-text">分野: <?= htmlspecialchars($question['s_number']) ?></p>
    </div>
</div>

<h3>回答一覧</h3>
<?php if (count($answers) > 0): ?>
    <?php foreach($answers as $a): ?>
        <div class="card mb-2">
            <div class="card-body">
                <?= nl2br(htmlspecialchars($a['answer_content'])) ?>
                <p class="text-muted small">投稿日時: <?= $a['created_at'] ?></p>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>まだ回答はありません。</p>
<?php endif; ?>

<h3>回答する</h3>
<form action="answer_post_process.php" method="post" class="mb-5">
    <input type="hidden" name="shitu_number" value="<?= $shitu_number ?>">
    <div class="mb-3">
        <textarea name="answer_content" class="form-control" rows="5" placeholder="回答内容を入力" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">回答する</button>
</form>

<a href="question_list.php" class="btn btn-secondary">質問一覧に戻る</a>

</body>
</html>

