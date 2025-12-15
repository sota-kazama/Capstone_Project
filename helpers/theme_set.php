<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["theme"])) {
    $theme = $_POST["theme"];

    // ★ 重要：パスを "/" にすることで全ページで Cookie が読める
    setcookie("theme", $theme, time() + 60*60*24*30, "/");

    echo json_encode(["status" => "success", "theme" => $theme]);
    exit;
}

echo json_encode(["status" => "error"]);
?>