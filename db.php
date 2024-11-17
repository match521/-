<?php
// 数据库连接配置
$host = 'localhost';
$dbname = 'fangfeng';
$username = 'fangfeng';
$password = 'CcJi3E42aPHnTNCh'; // 替换为你的数据库密码

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("数据库连接失败: " . $e->getMessage());
}
?>
