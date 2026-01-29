<?php
require_once '../helpers/DAO.php';
require_once '../helpers/QuestionDAO.php';
require_once '../helpers/CategoryDAO.php';
require_once '../helpers/MemberDAO.php';

session_start();

/* ===== ログインチェック ===== */
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
    $id = (int)$_GET['delete'];
    $message = $questionDAO->delete($id)
        ? "問題 #{$id} を削除しました。"
        : '削除に失敗しました。';
}

/* ===== 編集対象 ===== */
$editQuestion   = null;
$editCategories = [];

if (isset($_GET['edit'])) {
    $id           = (int)$_GET['edit'];
    $editQuestion = $questionDAO->findById($id);

    if ($editQuestion) {
        $editCategories = $categoryDAO->getCategoriesByQuestion(
            $editQuestion->q_number
        );
    }
}

/* ===== 登録・更新 ===== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $choices = $_POST['choices'] ?? ['', '', '', ''];
    $correct = $_POST['correct_answers'] ?? [];

    /* answers(JSON) を choices から生成 */
    $answersJson = json_encode($choices, JSON_UNESCAPED_UNICODE);

    $q_data = [
        'q_content'       => $_POST['q_content'] ?? '',
        'q_source'        => $_POST['q_source'] ?? '',
        'choices1'        => $choices[0] ?? '',
        'choices2'        => $choices[1] ?? '',
        'choices3'        => $choices[2] ?? '',
        'choices4'        => $choices[3] ?? '',
        'answers'         => $answersJson,
        'correct_answers' => json_encode($correct),
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
        if (move_uploaded_file(
            $_FILES['question_image']['tmp_name'],
            $uploadDir . $fileName
        )) {
            $q_data['image_path'] = $fileName;
        }

    } elseif (!empty($_POST['q_number']) && !empty($editQuestion->image_path)) {
        $q_data['image_path'] = $editQuestion->image_path;
    }

    /* ===== 登録 or 更新 ===== */
    if (!empty($_POST['q_number'])) {

        $q_number = (int)$_POST['q_number'];
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

    /* ===== カテゴリ紐付け ===== */
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
    <link href="../css_theme/base.css" rel="stylesheet">
    <link href="../css_theme/side.css" rel="stylesheet">
    <link id="theme-css" href="../css_theme/<?= htmlspecialchars($theme) ?>.css" rel="stylesheet">
</head>

<body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">

<?php include '../template/header2.php'; ?>

<div class="d-flex min-vh-100">
<?php include 'side.php'; ?>

<main class="flex-grow-1 p-4">

<h1><?= $editQuestion ? "問題編集 (#{$editQuestion->q_number})" : '問題登録' ?></h1>

<?php if ($message): ?>
<div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="card p-4">
<form method="post" enctype="multipart/form-data">

<input type="hidden" name="q_number"
       value="<?= htmlspecialchars($editQuestion->q_number ?? '') ?>">

<div class="mb-3">
<label class="form-label">問題文</label>
<textarea name="q_content" class="form-control" rows="3" required><?= htmlspecialchars($editQuestion->q_content ?? '') ?></textarea>
</div>

<div class="mb-3">
<label class="form-label">選択肢（4択）</label>

<?php
$choices = $editQuestion
    ? [$editQuestion->choices1, $editQuestion->choices2, $editQuestion->choices3, $editQuestion->choices4]
    : ['', '', '', ''];

$corrects = $editQuestion
    ? json_decode($editQuestion->correct_answers, true)
    : [];
?>

<?php for ($i = 1; $i <= 4; $i++): ?>
<div class="input-group mb-2">
    <span class="input-group-text"><?= $i ?></span>
    <input type="text" name="choices[]" class="form-control"
           value="<?= htmlspecialchars($choices[$i - 1]) ?>" required>
    <span class="input-group-text">
        <input type="checkbox" name="correct_answers[]"
               value="<?= $i ?>" <?= in_array($i, $corrects) ? 'checked' : '' ?>>
        正解
    </span>
</div>
<?php endfor; ?>
</div>

<div class="mb-3">
<label class="form-label">出典</label>
<input type="text" name="q_source" class="form-control"
       value="<?= htmlspecialchars($editQuestion->q_source ?? '') ?>">
</div>

<div class="mb-3">
<label class="form-label">画像</label>
<input type="file" name="question_image" class="form-control">

<?php if (!empty($editQuestion->image_path)): ?>
<img src="../uploads/<?= htmlspecialchars($editQuestion->image_path) ?>"
     class="img-thumbnail mt-2" style="max-width:200px;">
<div class="form-check mt-2">
<input class="form-check-input" type="checkbox" name="delete_image" value="1">
<label class="form-check-label">この画像を削除</label>
</div>
<?php endif; ?>
</div>

<button class="btn btn-primary"><?= $editQuestion ? '更新' : '登録' ?></button>

</form>
</div>

</main>
</div>
</body>
</html>
