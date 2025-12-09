<?php
require_once '../helpers/MemberDAO.php';

// セッションを開始する
session_start();

// 未ログインの場合はログインページへリダイレクト（統一したリダイレクト先）
if (!isset($_SESSION['member'])) {
    header('Location: login.php');
    exit;
}

// ログイン中の会員データを取得
$member = $_SESSION['member'];

// ユーザーIDの取得（セッションデータがオブジェクトであることを前提とする）
// もしMemberDAOがオブジェクトを返す場合、通常はこれで十分です。
$user_id = $member->user_id ?? null; 

// ユーザーIDが取得できない場合、ログインページにリダイレクト
if ($user_id === null) {
    // ログイン画面へリダイレクト
    header('Location: login.php'); 
    exit;
}

// メッセージ類の初期化（成功・エラー用）
$message = '';
$error = '';

// MemberDAOインスタンスを作成
$dao = new MemberDAO();

// ユーザー情報を取得
try {
    $userData = $dao->getMemberById($user_id);
    if (!$userData) {
        $error = 'ユーザー情報が見つかりませんでした。';
    }
} catch (Exception $e) {
    $error = 'ユーザー情報取得中にエラーが発生しました：' . $e->getMessage();
    // エラー時は処理を続行せずHTMLを表示
}


// POST処理（プロフィール更新）
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userData) {
    try {
        // POSTデータの取得
        $user_name = trim($_POST['user_name'] ?? '');
        $mail_address = trim($_POST['mail_address'] ?? '');
        $pass_word = $_POST['pass_word'] ?? ''; // trimしない

        // 必須チェック
        if (empty($user_name) || empty($mail_address)) {
            $error = 'ユーザー名とメールアドレスは必須項目です。';
        }

        if (empty($error)) {
            // パスワードが入力されているかチェック
            $password_hashed = null;
            if (!empty($pass_word)) {
                // パスワードが入力されている場合のみハッシュ化
                $password_hashed = password_hash($pass_word, PASSWORD_DEFAULT);
            }
            
            // プロフィール更新処理
            // パスワードが入力されていない場合 ($password_hashed === null)、DAO側で更新をスキップするように実装されている必要があります。
            $success = $dao->updateMemberSelf(
                $user_id,
                $user_name,
                $mail_address,
                $password_hashed // nullの場合はパスワードの更新を行わない（DAO側の実装依存）
            );

            // 更新が成功した場合
            if ($success) {
                $message = 'プロフィールを更新しました。';

                // セッションの会員データを更新
                $updated = $dao->getMemberById($user_id);
                // DAOがオブジェクトを返すことを前提としています
                $_SESSION['member'] = $updated; 

                // 表示用データも更新
                // $userDataは配列形式で使われているため、ここで配列に変換して保持
                if ($updated) {
                    $userData = (array)$updated; 
                }
            } else {
                $error = 'プロフィール更新に失敗しました。メールアドレスが既に使用されている可能性があります。';
            }
        }

    } catch (Exception $e) {
        $error = '更新中にエラーが発生しました：' . $e->getMessage();
    }
}
?>


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
        <link href="../css/BaseDesignData.css" rel="stylesheet" />
        <link href="../css/side.css" rel="stylesheet" />
        <?php include './header.php'; ?>
        <title>プロフィール編集</title>
    </head>

    <body>
        <div class="d-flex w-100 min-vh-100">
            <?php include 'side.php'; ?>

            <main class="main-content flex-grow-1 p-4">
                <h1>プロフィール編集</h1>

                <?php if ($message): ?>
                <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <?php if ($userData): ?>
                <div class="card p-4 mt-4">
                    <h4>登録情報</h4>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">ユーザー名</label>
                            <input
                                type="text"
                                class="form-control"
                                name="user_name"
                                value="<?= htmlspecialchars($userData['user_name']) ?>"
                                required
                            />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">メールアドレス</label>
                            <input
                                type="email"
                                class="form-control"
                                name="mail_address"
                                value="<?= htmlspecialchars($userData['mail_address']) ?>"
                                required
                            />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">パスワード（変更時のみ入力）</label>
                            <input
                                type="password"
                                class="form-control"
                                name="pass_word"
                                placeholder="変更する場合のみ入力"
                            />
                        </div>

                        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> 更新</button>
                    </form>
                </div>
                <?php endif; ?>
            </main>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>