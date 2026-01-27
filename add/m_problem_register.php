<?php
require_once '../helpers/DAO.php';
require_once '../helpers/QuestionDAO.php';
require_once '../helpers/CategoryDAO.php';
require_once '../helpers/MemberDAO.php';

session_start();

// 未ログインの場合
if (!isset($_SESSION['member'])) {
    header('Location: ../auth/login.php');
    exit;
}

$member      = $_SESSION['member'];
$questionDAO = new QuestionDAO();
$categoryDAO = new CategoryDAO();
$message     = '';
$theme       = $_COOKIE['theme'] ?? 'light';

/* ===== 削除処理 ===== */
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    $message = $questionDAO->delete($id)
        ? "問題 #{$id} を削除しました。"
        : '削除に失敗しました。';
}

/* ===== 編集対象 ===== */
$editQuestion   = null;
$editCategories = [];

if (isset($_GET['edit'])) {
    $id           = (int) $_GET['edit'];
    $editQuestion = $questionDAO->findById($id);

    if ($editQuestion) {
        $editCategories = $categoryDAO->getCategoriesByQuestion(
            $editQuestion->q_number
        );
    }
}

/* ===== 登録・更新 ===== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $answers        = $_POST['answers'] ?? ['', '', '', ''];
    $correctAnswers = $_POST['correct_answers'] ?? [];

    $q_data = [
        'q_content'       => $_POST['q_content'] ?? '',
        'answer_content'  => $answers[0] ?? '',
        'wrong_answer1'   => $answers[1] ?? '',
        'wrong_answer2'   => $answers[2] ?? '',
        'wrong_answer3'   => $answers[3] ?? '',
        'q_source'        => $_POST['q_source'] ?? '',
        'answers'         => json_encode($answers, JSON_UNESCAPED_UNICODE),
        'correct_answers' => json_encode($correctAnswers),
    ];

    /* ===== 画像アップロード ===== */
    if (!empty($_POST['delete_image'])) {

        $q_data['image_path'] = null;

    } elseif (
        isset($_FILES['question_image']) &&
        $_FILES['question_image']['error'] === UPLOAD_ERR_OK
    ) {

        $uploadDir = __DIR__ . '/../uploads/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = uniqid() . '_' . basename($_FILES['question_image']['name']);

        if (
            move_uploaded_file(
                $_FILES['question_image']['tmp_name'],
                $uploadDir . $fileName
            )
        ) {
            $q_data['image_path'] = $fileName;
        }

    } elseif (
        !empty($_POST['q_number']) &&
        !empty($editQuestion->image_path)
    ) {
        $q_data['image_path'] = $editQuestion->image_path;
    }

    /* ===== 登録 or 更新 ===== */
    if (!empty($_POST['q_number'])) {

        $q_number = (int) $_POST['q_number'];

        $message = $questionDAO->update($q_number, $q_data)
            ? "問題 #{$q_number} を更新しました。"
            : '更新に失敗しました。';

    } else {

        if ($questionDAO->insert($q_data)) {
            $q_number = $questionDAO->getLastInsertId();
            $message  = '問題を登録しました。';
        } else {
            $message = '登録に失敗しました。';
        }
    }

    /* ===== カテゴリ関連付け ===== */
    $categoryDAO->deleteCategoriesByQuestion($q_number);

    foreach ($_POST['area_numbers'] ?? [] as $area_number) {
        $categoryDAO->insertCategoryAssociation(
            $q_number,
            $area_number
        );
    }
}

/* ===== 検索 ===== */
$isSearching   = false;
$searchResults = [];

if (isset($_GET['search'])) {
    $keyword = trim($_GET['keyword'] ?? '');

    if ($keyword !== '') {
        $searchResults = $questionDAO->search($keyword);
        $isSearching   = true;
    }
}

/* ===== 再取得 ===== */
$categories = $categoryDAO->getAll();
$questions  = $questionDAO->getAll();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>問題登録・管理</title>
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
</head>

<body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">

<?php include '../template/header2.php'; ?>

<div class="d-flex min-vh-100">

    <?php include 'side.php'; ?>

    <main class="flex-grow-1 p-4">

        <h1 class="mb-3">
            <?= $editQuestion
                ? "問題編集 (#{$editQuestion->q_number})"
                : '問題登録'
            ?>
        </h1>

        <?php if ($message): ?>
            <div class="alert alert-info">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- ================= 登録フォーム ================= -->
        <div class="card p-4 mt-3">
            <form method="post" enctype="multipart/form-data">

                <input
                    type="hidden"
                    name="q_number"
                    value="<?= htmlspecialchars($editQuestion->q_number ?? '') ?>"
                >

                <div class="mb-3">
                    <label class="form-label">問題文</label>
                    <textarea
                        name="q_content"
                        class="form-control"
                        rows="3"
                        required
                    ><?= htmlspecialchars($editQuestion->q_content ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">選択肢（4択）</label>

                    <?php
                    $answers  = $editQuestion
                        ? json_decode($editQuestion->answers, true)
                        : ['', '', '', ''];

                    $corrects = $editQuestion
                        ? json_decode($editQuestion->correct_answers, true)
                        : [];
                    ?>

                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><?= $i ?></span>

                            <input
                                type="text"
                                name="answers[]"
                                class="form-control"
                                value="<?= htmlspecialchars($answers[$i - 1] ?? '') ?>"
                                required
                            >

                            <div class="input-group-text">
                                <input
                                    type="checkbox"
                                    name="correct_answers[]"
                                    value="<?= $i ?>"
                                    <?= in_array($i, $corrects) ? 'checked' : '' ?>
                                >
                                正解
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
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">画像</label>
                    <input type="file" name="question_image" class="form-control">

                    <?php if (!empty($editQuestion->image_path)): ?>
                        <div class="mt-2">
                            <p class="mb-1">現在の画像:</p>
                            <img
                                src="../uploads/<?= htmlspecialchars($editQuestion->image_path) ?>"
                                class="img-thumbnail"
                                style="max-width: 200px;"
                            >
                            <div class="form-check mt-2">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="delete_image"
                                    value="1"
                                >
                                <label class="form-check-label">
                                    この画像を削除する
                                </label>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label class="form-label">分野</label><br>

                    <?php foreach ($categories as $cat): ?>
                        <div class="form-check form-check-inline">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="area_numbers[]"
                                value="<?= $cat->area_number ?>"
                                <?= in_array($cat->area_number, $editCategories)
                                    ? 'checked'
                                    : ''
                                ?>
                            >
                            <label class="form-check-label">
                                <?= htmlspecialchars($cat->area_name) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="text-end">
                    <?php if ($editQuestion): ?>
                        <a href="?" class="btn btn-secondary">キャンセル</a>
                    <?php endif; ?>
                    <button class="btn btn-primary">
                        <?= $editQuestion ? '更新' : '登録' ?>
                    </button>
                </div>

            </form>
        </div>

        <!-- ================= 検索 ================= -->
        <div class="card p-4 mt-4">
            <h4>問題検索</h4>
            <form method="get" class="d-flex gap-2">
                <input
                    type="text"
                    name="keyword"
                    class="form-control"
                    value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>"
                    placeholder="問題文・出典で検索"
                >
                <button name="search" class="btn btn-secondary">
                    検索
                </button>
            </form>
        </div>

        <!-- ================= 検索結果 ================= -->
        <?php if ($isSearching): ?>
            <div class="card p-4 mt-4">
                <h4>検索結果（<?= count($searchResults) ?>件）</h4>

                <div class="list-group mt-3">
                    <?php foreach ($searchResults as $q): ?>
                        <div class="list-group-item d-flex justify-content-between">
                            <div>
                                <strong>#<?= $q->q_number ?>:</strong>
                                <?= htmlspecialchars(
                                    mb_strimwidth($q->q_content, 0, 100, '...')
                                ) ?>
                            </div>
                            <div>
                                <a
                                    href="?edit=<?= $q->q_number ?>"
                                    class="btn btn-sm btn-outline-primary"
                                >編集</a>
                                <button
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="confirmDelete(<?= $q->q_number ?>)"
                                >削除</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- ================= 一覧 ================= -->
        <div class="card p-4 mt-5">
            <h4>
                登録済み問題一覧
                <button
                    class="btn btn-sm btn-outline-secondary ms-2"
                    data-bs-toggle="collapse"
                    data-bs-target="#questionList"
                >
                    表示切替
                </button>
            </h4>

            <div
                id="questionList"
                class="collapse <?= $isSearching ? '' : 'show' ?>"
            >
                <div class="list-group mt-3">
                    <?php foreach ($questions as $q): ?>
                        <div class="list-group-item d-flex justify-content-between">
                            <div>
                                <strong>#<?= $q->q_number ?>:</strong>
                                <?= htmlspecialchars(
                                    mb_strimwidth($q->q_content, 0, 100, '...')
                                ) ?>
                            </div>
                            <div>
                                <a
                                    href="?edit=<?= $q->q_number ?>"
                                    class="btn btn-sm btn-outline-primary"
                                >編集</a>
                                <button
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="confirmDelete(<?= $q->q_number ?>)"
                                >削除</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </main>
</div>

<button id="theme-toggle-btn" class="btn btn-primary theme-toggle-btn">
    <i class="bi <?= $theme === 'dark' ? 'bi-sun' : 'bi-moon' ?>"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/theme-toggle.js"></script>

<script>
function confirmDelete(id) {
    if (confirm(`問題 #${id} を削除しますか？`)) {
        location.href = `?delete=${id}`;
    }
}
</script>

<footer>
    <?php include '../template/footer.php'; ?>
</footer>

</body>
</html>
