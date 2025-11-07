<?php
require_once '../helpers/DAO.php';
require_once '../helpers/QuestionDAO.php';
require_once '../helpers/CategoryDAO.php';
require_once '../helpers/MemberDAO.php';

session_start();

if (!isset($_SESSION['member'])) {
    header('Location: login.php');
    exit;
}

$member = $_SESSION['member'];
$questionDAO = new QuestionDAO();
$categoryDAO = new CategoryDAO();
$message = "";

// 削除処理
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($questionDAO->delete($id)) {
        $message = "問題 #{$id} を削除しました。";
    } else {
        $message = "削除に失敗しました。";
    }
}

// 編集対象取得
$editQuestion = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $editQuestion = $questionDAO->findById($id);
}

// 登録・更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answers = $_POST['answers'] ?? ["", "", "", ""];
    $correct_answers = $_POST['correct_answers'] ?? [];

    $q_data = [
        'q_content' => $_POST['q_content'] ?? '',
        'answer_content' => $answers[0] ?? '',
        'wrong_answer1' => $answers[1] ?? '',
        'wrong_answer2' => $answers[2] ?? '',
        'wrong_answer3' => $answers[3] ?? '',
        'q_source' => $_POST['q_source'] ?? '',
        'answers' => json_encode($answers),
        'correct_answers' => json_encode($correct_answers)
    ];

    // 画像アップロード処理
    if (isset($_FILES['question_image']) && $_FILES['question_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $tmpName = $_FILES['question_image']['tmp_name'];
        $fileName = uniqid() . '_' . basename($_FILES['question_image']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($tmpName, $targetPath)) {
            $q_data['image_path'] = $fileName;
        }
    } elseif (!empty($_POST['q_number']) && !empty($editQuestion->image_path)) {
        $q_data['image_path'] = $editQuestion->image_path;
    }

    // 更新 or 新規登録
    if (!empty($_POST['q_number'])) {
        $q_number = intval($_POST['q_number']);
        if ($questionDAO->update($q_number, $q_data)) {
            $message = "問題 #{$q_number} を更新しました。";
        } else {
            $message = "更新に失敗しました。";
        }
    } else {
        if ($questionDAO->insert($q_data)) {
            $message = "問題を登録しました。";
        } else {
            $message = "登録に失敗しました。";
        }
    }
}

// データ再取得
$categories = $categoryDAO->getAll();
$questions = $questionDAO->getAll();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <title>問題登録・管理</title>
    <script>
    function confirmDelete(id) {
        if (confirm(`問題 #${id} を削除しますか？`)) {
            location.href = `?delete=${id}`;
        }
    }
    </script>
</head>

<body>
<div class="d-flex w-100 min-vh-100">
    <?php include 'side.php'; ?>

    <main class="main-content container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2><?= $editQuestion ? "問題編集 (#{$editQuestion->q_number})" : "問題登録" ?></h2>
            <a href="#question-list" class="btn btn-outline-secondary">↓ 登録済み一覧へ</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" class="border rounded p-3 mb-5 bg-light">
            <input type="hidden" name="q_number" value="<?= htmlspecialchars($editQuestion->q_number ?? '') ?>">

            <div class="mb-3">
                <label class="form-label">問題文</label>
                <textarea name="q_content" class="form-control" rows="3" required><?= htmlspecialchars($editQuestion->q_content ?? '') ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">選択肢（4択）</label>
                <?php
                    $answers = $editQuestion ? json_decode($editQuestion->answers, true) : ["", "", "", ""];
                    $corrects = $editQuestion ? json_decode($editQuestion->correct_answers, true) : [];
                ?>
                <?php for ($i = 1; $i <= 4; $i++): ?>
                    <div class="input-group mb-2">
                        <span class="input-group-text"><?= $i ?></span>
                        <input type="text" name="answers[]" class="form-control"
                            value="<?= htmlspecialchars($answers[$i-1] ?? '') ?>" required>
                        <div class="input-group-text">
                            <input type="checkbox" name="correct_answers[]" value="<?= $i ?>"
                                <?= in_array($i, $corrects ?? []) ? 'checked' : '' ?>> 正解
                        </div>
                    </div>
                <?php endfor; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">出典</label>
                <input type="text" name="q_source" class="form-control"
                    value="<?= htmlspecialchars($editQuestion->q_source ?? '') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">画像（任意）</label>
                <input type="file" name="question_image" class="form-control">
                <?php if (!empty($editQuestion->image_path)): ?>
                    <div class="mt-2">
                        <img src="../uploads/<?= htmlspecialchars($editQuestion->image_path) ?>" alt="問題画像" style="max-width:200px;">
                    </div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">分野（複数選択可）</label><br>
                <?php foreach ($categories as $cat): ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="area_numbers[]" 
                            value="<?= htmlspecialchars($cat->area_number) ?>">
                        <label class="form-check-label"><?= htmlspecialchars($cat->area_name) ?></label>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="text-end">
                <?php if ($editQuestion): ?>
                    <a href="?" class="btn btn-secondary">キャンセル</a>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary"><?= $editQuestion ? '更新' : '登録' ?></button>
            </div>
        </form>


        <hr id="question-list">
        <h2>登録済み問題一覧</h2>

        <div class="list-group mt-3">
            <?php foreach ($questions as $q): ?>
                <div class="list-group-item">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>#<?= $q->q_number ?>:</strong>
                            <?= htmlspecialchars(mb_strimwidth($q->q_content, 0, 100, '...')) ?>
                            <?php if (!empty($q->image_path)): ?>
                                <br><img src="../uploads/<?= htmlspecialchars($q->image_path) ?>" alt="問題画像" style="max-width:100px;">
                            <?php endif; ?>
                            <span class="text-muted small">(更新: <?= htmlspecialchars($q->update_at) ?>)</span>
                        </div>
                        <div>
                            <a href="?edit=<?= $q->q_number ?>" class="btn btn-sm btn-outline-primary">編集</a>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete(<?= $q->q_number ?>)">削除</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

<footer>
    <?php include '../template/footer.php'; ?>
</footer>
</html>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
            rel="stylesheet"
        />
        <title>問題登録・管理</title>
        <script>
            function confirmDelete(id) {
                if (confirm(`問題 #${id} を削除しますか？`)) {
                    location.href = `?delete=${id}`;
                }
            }
        </script>
    </head>

    <body>
        <div class="d-flex w-100 min-vh-100">
            <?php include 'side.php'; ?>

            <main class="main-content container mt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2><?= $editQuestion ? "問題編集 (#{$editQuestion->q_number})" : "問題登録" ?></h2>
                    <a href="#question-list" class="btn btn-outline-secondary">↓ 登録済み一覧へ</a>
                </div>

                <?php if ($message): ?>
                <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data" class="border rounded p-3 mb-5 bg-light">
                    <input
                        type="hidden"
                        name="q_number"
                        value="<?= htmlspecialchars($editQuestion->q_number ?? '') ?>"
                    />

                    <div class="mb-3">
                        <label class="form-label">問題文</label>
                        <textarea name="q_content" class="form-control" rows="3" required>
<?= htmlspecialchars($editQuestion->q_content ?? '') ?></textarea
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label">選択肢（4択）</label>
                        <?php
                    $answers = $editQuestion ? json_decode($editQuestion->answers, true) : ["", "", "", ""]; $corrects =
                        $editQuestion ? json_decode($editQuestion->correct_answers, true) : []; ?>
                        <?php for ($i = 1; $i <= 4; $i++): ?>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><?= $i ?></span>
                            <input
                                type="text"
                                name="answers[]"
                                class="form-control"
                                value="<?= htmlspecialchars($answers[$i-1] ?? '') ?>"
                                required
                            />
                            <div class="input-group-text">
                                <input type="checkbox" name="correct_answers[]" value="<?= $i ?>"
                                <?= in_array($i, $corrects ?? []) ? 'checked' : '' ?>> 正解
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">出典</label>
                        <input
                            type="text"
                            name="q_source"
                            class="form-control"
                            value="<?= htmlspecialchars($editQuestion->q_source ?? '') ?>"
                        />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">画像（任意）</label>
                        <input type="file" name="question_image" class="form-control" />
                        <?php if (!empty($editQuestion->image_path)): ?>
                        <div class="mt-2">
                            <img
                                src="../uploads/<?= htmlspecialchars($editQuestion->image_path) ?>"
                                alt="問題画像"
                                style="max-width: 200px"
                            />
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">分野（複数選択可）</label><br />
                        <?php foreach ($categories as $cat): ?>
                        <div class="form-check form-check-inline">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="area_numbers[]"
                                value="<?= htmlspecialchars($cat->area_number) ?>"
                            />
                            <label class="form-check-label"><?= htmlspecialchars($cat->area_name) ?></label>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="text-end">
                        <?php if ($editQuestion): ?>
                        <a href="?" class="btn btn-secondary">キャンセル</a>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-primary"><?= $editQuestion ? '更新' : '登録' ?></button>
                    </div>
                </form>

                <hr id="question-list" />
                <h2>登録済み問題一覧</h2>

                <div class="list-group mt-3">
                    <?php foreach ($questions as $q): ?>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>#<?= $q->q_number ?>:</strong>
                                <?= htmlspecialchars(mb_strimwidth($q->q_content, 0, 100, '...')) ?>
                                <?php if (!empty($q->image_path)): ?> <br /><img
                                    src="../uploads/<?= htmlspecialchars($q->image_path) ?>"
                                    alt="問題画像"
                                    style="max-width: 100px"
                                />
                                <?php endif; ?>
                                <span class="text-muted small"
                                    >(更新:
                                    <?= htmlspecialchars($q->update_at) ?>)</span
                                >
                            </div>
                            <div>
                                <a href="?edit=<?= $q->q_number ?>" class="btn btn-sm btn-outline-primary">編集</a>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="confirmDelete(<?= $q->q_number ?>)"
                                >
                                    削除
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </main>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>

    <footer>
        <?php include '../template/footer.php'; ?>
    </footer>
</html>
