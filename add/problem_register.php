<?php
require_once '../helpers/ProblemDAO.php';
require_once '../helpers/ShikakuDAO.php';

session_start();

// ログインチェック
if (!isset($_SESSION['member'])) {
    header('Location: login.php');
    exit;
}
$member = $_SESSION['member'];

// タブ判定
$activeTab = 'category';
if (isset($_GET['tab'])) {
    $activeTab = $_GET['tab'];
}

// DAO初期化
$problemDAO = new ProblemDAO();
$shikakuDAO = new ShikakuDAO();
$shikakuList = $shikakuDAO->getAll();

$errors = [];
$success = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']);

// 登録処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['area_register'])) {
    $area_number = trim($_POST['area_number'] ?? '');
    $area_name = trim($_POST['area_name'] ?? '');
    $s_number = trim($_POST['s_number'] ?? '');

    $errors = [];

    // 分野番号必須＆バリデーション（英数字10文字以内）
    if ($area_number === '') {
        $errors[] = '分野番号は必須です。';
    } else {
        if (!ctype_alnum($area_number)) {
            $errors[] = '分野番号は英数字のみで入力してください。';
        }
        if (mb_strlen($area_number) > 10) {
            $errors[] = '分野番号は10文字以内で入力してください。';
        }
    }

    // 資格選択必須
    if ($s_number === '') {
        $errors[] = '資格を選択してください。';
    }

    // 分野名未入力時は資格名を使用
    if ($area_name === '' && $s_number !== '') {
        foreach ($shikakuList as $s) {
            if ($s->s_number == $s_number) {
                $area_name = $s->s_name;
                break;
            }
        }
    }

    // エラーがなければDB登録
    if (empty($errors)) {
        $result = $problemDAO->insertCategory($area_name, $s_number, $area_number);
        if ($result) {
            $_SESSION['success_message'] = '分野を登録しました。';
            header('Location: problem_register.php?tab=category');
            exit;
        } else {
            $errors[] = '登録に失敗しました。';
        }
    }
}

// 既存分野一覧取得
$categories = $problemDAO->getAllCategories();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
        <link href="../css/side.css" rel="stylesheet" />
    <title>問題登録ページ</title>
</head>
<body>
<div class="container mt-5">
    <div class="d-flex w-100 min-vh-100">
    <?php include 'side.php'; ?>
    <h1>問題登録ページ</h1>

    <!-- タブ -->
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link <?= $activeTab === 'category' ? 'active' : '' ?>" href="#category" data-bs-toggle="tab">分野登録・編集</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $activeTab === 'problem' ? 'active' : '' ?>" href="#problem" data-bs-toggle="tab">問題登録</a>
        </li>
    </ul>

    <!-- タブコンテンツ -->
    <div class="tab-content mt-3">
        <!-- 分野登録タブ -->
        <div class="tab-pane fade <?= $activeTab === 'category' ? 'show active' : '' ?>" id="category">
            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $err): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="area_register" value="1">
                <div class="mb-3">
                    <label>分野番号</label>
                    <input type="text" name="area_number" class="form-control">
                </div>
                <div class="mb-3">
    <label>分野名</label>
    <input type="text" name="area_name" class="form-control" maxlength="10" 
           placeholder="未入力の場合、資格名が自動で入ります">
</div>
                <div class="mb-3">
                    <label>資格</label>
                    <select name="s_number" class="form-select">
                        <option value="">選択してください</option>
                        <?php foreach ($shikakuList as $s): ?>
                            <option value="<?= htmlspecialchars($s->s_number) ?>">
                                <?= htmlspecialchars($s->s_name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">登録</button>
            </form>

            <!-- 登録済分野一覧（編集用） -->
            <hr>
            <h5>登録済み分野</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>分野番号</th>
                        <th>分野名</th>
                        <th>資格名</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><?= htmlspecialchars($cat->area_number) ?></td>
                            <td><?= htmlspecialchars($cat->area_name) ?></td>
                            <td><?= htmlspecialchars($cat->s_name) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- 問題登録タブ -->
        <div class="tab-pane fade <?= $activeTab === 'problem' ? 'show active' : '' ?>" id="problem">
            <p>ここに問題登録フォームを作成してください。</p>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
