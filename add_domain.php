<?php
require 'db.php';

// 读取传入的 JSON 数据
$data = json_decode(file_get_contents('php://input'), true);
$domains = $data['domains'] ?? [];

$adminPassword = "111";  // 定义管理员密码
$password = $_POST['password'] ?? '';  // 获取传入的密码

// 验证密码
if ($password !== $adminPassword) {
    echo json_encode(['message' => '密码错误，无法添加域名！']);
    exit;
}

if (empty($domains)) {
    echo json_encode(['message' => '没有提供任何域名']);
    exit;
}

// 将域名添加到数据库
try {
    $stmt = $pdo->prepare("INSERT INTO domain_pool (domain, status) VALUES (?, ?)");
    foreach ($domains as $domain) {
        $stmt->execute([$domain, 'valid']);
    }
    echo json_encode(['message' => '域名添加成功']);
} catch (PDOException $e) {
    echo json_encode(['message' => '添加域名时发生错误：' . $e->getMessage()]);
}
