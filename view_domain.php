<?php
require 'db.php';

// 获取所有域名
$stmt = $pdo->query("SELECT * FROM domain_pool");
$domains = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($domains as &$domain) {
    $domain['is_current'] = $domain['is_current'] == 1 ? 'current' : 'not-using'; // 标记当前域名
}

echo json_encode(['domains' => $domains]);
