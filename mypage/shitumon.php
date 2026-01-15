<?php
require_once '../helpers/MemberDAO.php';
require_once '../helpers/ShitumonDAO.php';

session_start();

// 未ログインの場合、ログインページにリダイレクト
if (!isset($_SESSION['member'])) {
    header('Location: login.php');
    exit;
}

// テーマ（Cookie なければ light）
$theme = $_COOKIE['theme'] ?? 'light';

// Memberオブジェクト
$member = $_SESSION['member'];

// ShitumonDAOインスタンス
$shitumonDAO = new ShitumonDAO();
$user_id      = $member->user_id;

// ユーザーの質問一覧取得
$questions = $shitumonDAO->getAllByUser($user_id);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- カスタムCSS -->
    <link href="../css/BaseDesignData.css" rel="stylesheet">
    <link href="../css/side.css" rel="stylesheet">
    <link id="theme-css" rel="stylesheet" href="../css_theme/<?= htmlspecialchars($theme) ?>.css">
    <link href="../css_theme/toggle-button.css" rel="stylesheet">

    <title>マイページ</title>

    <style>
        /* ===== マイページデザイン ===== */
        .main-content {
            flex: 1;
            padding: 2rem;
        }

        h1 {
            font-weight: bold;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 0.5rem;
            margin-bottom: 2rem;
        }

        .question-card {
            margin-bottom: 1rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }

        .question-card th,
        .question-card td {
            vertical-align: middle;
        }

        .question-title a {
            text-decoration: none;
            color: #0d6efd;
            font-weight: 500;
        }

        .question-title a:hover {
            text-decoration: underline;
        }

        .status-badge {
            font-size: 0.85rem;
            padding: 0.35em 0.65em;
            border-radius: 0.5rem;
        }

        .status-open {
            background-color: #198754;
            color: #fff;
        }

        .status-closed {
            background-color: #6c757d;
            color: #fff;
        }

        .end-reception-btn {
            min-width: 100px;
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 1rem;
            }

            .table-responsive {
                overflow-x: auto;
            }
        }
    </style>
</head>

<body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">

<?php include '../template/header.php'; ?>

<div class="d-flex w-100 min-vh-100">
    <!-- サイドバー -->
    <aside class="d-none d-md-block">
        <?php include 'side.php'; ?>
    </aside>

    <!-- メイン -->
    <main class="main-content container">
        <h1>質問箱の管理</h1>

        <section class="mb-5">
            <h2 class="mb-3">あなたの投稿した質問</h2>

            <div class="table-responsive">
                <table class="table table-striped table-hover question-card">
                    <thead class="table-light">
                        <tr>
                            <th>質問タイトル</th>
                            <th>質問内容</th>
                            <th>受付状態</th>
                            <th>投稿日時</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($questions)): ?>
                            <?php foreach ($questions as $q): 
                                $askedDate   = (new DateTime($q->asked_date))->format('Y/m/d H:i');
                                $statusClass = $q->reception_status == 1 ? 'status-open' : 'status-closed';
                            ?>
                                <tr id="question-row-<?= $q->shitu_number ?>">
                                    <td class="question-title">
                                        <a href="../Shitsumonbako/question_answer.php?shitu_number=<?= $q->shitu_number ?>">
                                            <?= htmlspecialchars($q->shitu_title) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($q->shitu_content) ?></td>
                                    <td>
                                        <span class="status-badge <?= $statusClass ?>">
                                            <?= $q->reception_status == 1 ? '受付中' : '受付終了' ?>
                                        </span>
                                    </td>
                                    <td><?= $askedDate ?></td>
                                    <td>
                                        <?php if ($q->reception_status == 1): ?>
                                            <button class="btn btn-warning btn-sm end-reception-btn" data-shitu_number="<?= $q->shitu_number ?>">
                                                受付終了
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted">受付終了済み</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">投稿した質問はありません。</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<!-- テーマ切替ボタン -->
<button id="theme-toggle-btn" class="btn theme-toggle-btn">
    <i id="theme-icon" class="bi <?= $theme === 'dark' ? 'bi-sun' : 'bi-moon' ?>"></i>
</button>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/theme-toggle.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    $('.end-reception-btn').click(function() {
        const shitu_number = $(this).data('shitu_number');

        $.post('update_status.php', { action: 'end_reception', shitu_number }, function(response) {
            const data = JSON.parse(response);
            if (data.success) {
                alert('受付終了しました');
                location.reload();
            } else {
                alert('受付終了に失敗しました: ' + (data.message || '未知のエラー'));
            }
        }).fail(function() {
            alert('通信に失敗しました。もう一度試してください。');
        });
    });
});
</script>

<footer>
    <?php include '../template/footer.php'; ?>
</footer>
</body>
</html>
