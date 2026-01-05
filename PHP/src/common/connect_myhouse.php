<?php
define('cDBname', 'sotuken'); // DB名
define('cSRVNAME', '(local)'); // または '.' を使用

$srvName = cDBname;
$srvNameServer = cSRVNAME;

try {
    $conn = new PDO("sqlsrv:Server=$srvNameServer;Database=$srvName", null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch(PDOException $e) {
    // エラーの詳細を表示
    echo "接続エラー: " . $e->getMessage();
}
?>