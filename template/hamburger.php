<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>トップページ</title>
        <link href="css/hamburger.css" rel="stylesheet" />
    </head>
    <body>
        <div class="hamburger" id="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <nav class="menu" id="menu">
            <ul>
                <li><a href="problem/problem.php">問題</a></li>
                <li><a href="book.php">書籍検索</a></li>
                <?php if (isset($member)) : ?>
                    <li><a href="board.php">掲示板</a></li>
                    <li><a href="mypage.php">マイページ</a></li>
                    <li><a href="question_list.php">質問箱</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        
        <script>
            const hamburger = document.getElementById("hamburger");
            const menu = document.getElementById("menu");

            hamburger.addEventListener("click", () => {
                hamburger.classList.toggle("active");
                menu.classList.toggle("active");
            });
        </script>
    </body>
</html>
