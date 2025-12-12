<?php
// DB接続設定
define('DSN', 'sqlsrv:server=10.32.97.1\SOTSU;database=24yn01_G02');
define('DB_USER', '24yn01_G02');
define('DB_PASSWORD', '24yn01_G02');

// プロジェクトのルートURLを動的に検出
$document_root = rtrim($_SERVER['DOCUMENT_ROOT'], '/'); // サーバーのルートディレクトリ
$request_uri = $_SERVER['REQUEST_URI']; // 現在のリクエストURI

// プロジェクトのベースディレクトリ（例えば /Capstone_Project）
$base_path = '/Capstone_Project'; // この部分は、プロジェクトのパスが変わらない場合は直接指定します。

// BASE_URLを計算（DOCUMENT_ROOT と REQUEST_URI の差分からベースURLを抽出）
$base_url = $base_path;

// ベースURLを定義
define('BASE_URL', $base_url);

