<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link href="../css/BaseDesignData.css" rel="stylesheet" />
        <title>ログイン</title>
        <?php include 'template/header2.php'; ?>
    </head>
    <body>
        

        <form action="" method="POST">
            <table id="../css/BaseDesignData.css">
                <tr>
                    <th colspan="2">ログイン</th>
                </tr>
                <tr>
                    <td>メールアドレス</td>
                    <td>
                        <input type="text" name="email" />
                    </td>
                </tr>
                <tr>
                    <td>パスワード</td>
                    <td>
                        <input type="password" name="password" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="submit" value="ログイン" />
                    </td>
                    <td></td>
                </tr>

            </table>
        </form>

        <!-- <table class="box">
        <tr>
            <th>初めての利用の方</th>
        </tr>
        <tr>
            <td>ログインするには会員登録が必要です。</td>
        </tr>
        <tr>
            <td><a href="signup.php">新規会員登録はこちら</a></td>
        </tr>
    </table> -->
    </body>
</html>
