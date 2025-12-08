<?php
require_once '../helpers/MemberDAO.php';

session_start();

// 未ログインの場合
if (!isset($_SESSION['member'])) {
    header('Location: login.php');
    exit;
}

$member = $_SESSION['member'];
$theme = $_COOKIE['theme'] ?? 'light';
?>


<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
            rel="stylesheet"
        />

        <link href="../css/BaseDesignData.css" rel="stylesheet" />
        <link href="../css/side.css" rel="stylesheet" />
        <link id="theme-css" rel="stylesheet" href="../css_theme/light.css" />
        <link rel="stylesheet" href="../css_theme/toggle.css" />

        <title>アカウント情報編集</title>
        <script src="../js/theme.js" defer></script>
    </head>
    <body>
        <?php include '../template/header2.php'; ?>

        <div class="theme-toggle">
            <label class="switch">
                <input type="checkbox" id="themeSwitch" />
                <span class="slider round"></span>
            </label>
        </div>

        <div class="d-flex w-100 min-vh-100">
            <?php include 'side.php'; ?>

            <main class="main-content flex-grow-1 p-4">
                <h1>アカウント情報編集 (管理者用)</h1>

                <?php if ($message): ?>
                <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <div class="card p-3 mt-4">
                    <h4>アカウント選択と編集</h4>
                    <form method="POST">
                        <div class="mb-3">
                            <label for="user_id_select" class="form-label">編集対象アカウント</label>
                            <select
                                id="user_id_select"
                                name="user_id"
                                class="form-select"
                                required
                                onchange="loadMemberData()"
                            >
                                <option value="">-- ユーザーを選択してください --</option>
                                <?php foreach ($allMembers as $m): ?>
                                <option
                                    value="<?= $m['user_id'] ?>"
                                    data-user-name="<?= htmlspecialchars($m['user_name']) ?>"
                                    data-mail-address="<?= htmlspecialchars($m['mail_address']) ?>"
                                    data-u-admin="<?= htmlspecialchars($m['u_admin']) ?>"
                                    data-member-type="<?= htmlspecialchars($m['member_type'] ?? 0) ?>"
                                >
                                    ID:
                                    <?= $m['user_id'] ?>
                                    -
                                    <?= htmlspecialchars($m['user_name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ユーザー名</label>
                            <input type="text" id="user_name" name="user_name" class="form-control" required />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">メールアドレス</label>
                            <input type="email" id="mail_address" name="mail_address" class="form-control" required />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">パスワード（変更時のみ）</label>
                            <input type="password" id="pass_word" name="pass_word" class="form-control" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">管理者フラグ</label><br />
                            <label class="form-check form-check-inline">
                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="u_admin"
                                    id="u_admin_0"
                                    value="0"
                                    required
                                />
                                一般
                            </label>
                            <label class="form-check form-check-inline">
                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="u_admin"
                                    id="u_admin_1"
                                    value="1"
                                    required
                                />
                                管理者
                            </label>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">アカウント状態</label><br />
                            <label class="form-check form-check-inline">
                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="member_type"
                                    id="member_type_0"
                                    value="0"
                                    required
                                />
                                有効
                            </label>
                            <label class="form-check form-check-inline">
                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="member_type"
                                    id="member_type_1"
                                    value="1"
                                    required
                                />
                                無効
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary">更新</button>
                    </form>

                    <?php if ($totalPages >
                    1): ?>
                    <nav>
                        <ul class="pagination justify-content-center mt-4">
                            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page - 1 ?>">&laquo;</a>
                            </li>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                            <?php endfor; ?>

                            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page + 1 ?>">&raquo;</a>
                            </li>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </main>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <script>
            function loadMemberData() {
                const select = document.getElementById("user_id_select");
                const opt = select.options[select.selectedIndex];
                if (!opt.value) {
                    document.getElementById("user_name").value = "";
                    document.getElementById("mail_address").value = "";
                    document.getElementById("pass_word").value = "";
                    document.getElementById("u_admin_0").checked = false;
                    document.getElementById("u_admin_1").checked = false;
                    document.getElementById("member_type_0").checked = false;
                    document.getElementById("member_type_1").checked = false;
                    return;
                }

                document.getElementById("user_name").value = opt.getAttribute("data-user-name");
                document.getElementById("mail_address").value = opt.getAttribute("data-mail-address");
                document.getElementById("pass_word").value = "";
                document.getElementById("u_admin_0").checked = opt.getAttribute("data-u-admin") == "0";
                document.getElementById("u_admin_1").checked = opt.getAttribute("data-u-admin") == "1";
                document.getElementById("member_type_0").checked = opt.getAttribute("data-member-type") == "0";
                document.getElementById("member_type_1").checked = opt.getAttribute("data-member-type") == "1";
            }
        </script>
    </body>

    <footer>
        <?php include '../template/footer.php'; ?>
    </footer>
</html>

