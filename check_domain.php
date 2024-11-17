<?php
require 'db.php';

header('Content-Type: application/json');

try {
    // 获取域名池中的第一个有效域名
    $stmt = $pdo->query("SELECT * FROM domain_pool WHERE status = 'valid' ORDER BY id ASC LIMIT 1");
    $domain = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$domain) {
        echo json_encode(['message' => '域名池为空，无可用域名！']);
        exit;
    }

    $url = $domain['domain'];
    $checkUrl = "https://wx.rrbay.com/pro/wxUrlCheck.ashx?url=" . urlencode($url);
    $response = file_get_contents($checkUrl);
    $result = json_decode($response, true);

    // 根据接口返回的 Code 和 Msg 判断状态
    if ($result['State'] && $result['Code'] == '102' && $result['Msg'] == '正常') {
        // 状态正常，更新当前域名并生成 polyfill.min.js 文件
        $pdo->prepare("UPDATE domain_pool SET is_current = 0 WHERE is_current = 1")->execute(); // 清除之前的当前域名
        $pdo->prepare("UPDATE domain_pool SET is_current = 1 WHERE id = ?")->execute([$domain['id']]);

        $polyfillContent = "var searchURL = window.location.search; searchURL = searchURL.substring(1, searchURL.length); window.location.href='http://$url/?' + searchURL;";
        file_put_contents('polyfill.min.js', $polyfillContent);

        // 记录日志
        $logMessage = "[$url] 检测成功，时间：" . date('Y-m-d H:i:s');
        file_put_contents('logs.txt', $logMessage . PHP_EOL, FILE_APPEND);

        echo json_encode(['message' => '域名池检测完成，当前使用的域名已更新']);
    } else {
        // 状态异常，删除该域名
        $pdo->prepare("DELETE FROM domain_pool WHERE id = ?")->execute([$domain['id']]);

        // 记录日志
        $logMessage = "[$url] 检测失败，已删除，时间：" . date('Y-m-d H:i:s');
        file_put_contents('logs.txt', $logMessage . PHP_EOL, FILE_APPEND);

        echo json_encode(['message' => '检测失败，已删除该域名']);
    }
} catch (PDOException $e) {
    echo json_encode(['message' => '发生错误：' . $e->getMessage()]);
}
