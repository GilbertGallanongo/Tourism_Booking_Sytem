<?php
$projectRoot = realpath(__DIR__ . '/..');
$sqlitePath = $projectRoot . '/database/database.sqlite';
if (! file_exists($sqlitePath)) {
    echo "MISSING SQLITE\n";
    exit(1);
}
$sqlite = new PDO('sqlite:' . $sqlitePath);
$sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pgsql = new PDO('pgsql:host=acela.proxy.rlwy.net;port=11517;dbname=railway;sslmode=require', 'postgres', 'yscEJJBfLsOyBdZYQEDOwRTIHWHJquHs');
$pgsql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$tables = ['admins','bookings','cache','cache_locks','destinations','famous_tourist_spots','migrations','payments','personal_access_tokens','promo_packages','report_histories','reviews','sessions','tour_packages','users'];
echo "LOCAL COUNTS:\n";
foreach ($tables as $table) {
    $stmt = $sqlite->query('SELECT COUNT(*) AS c FROM "' . str_replace('"', '""', $table) . '"');
    echo sprintf("%s %d\n", $table, $stmt->fetch(PDO::FETCH_ASSOC)['c']);
}
echo "REMOTE COUNTS:\n";
foreach ($tables as $table) {
    $stmt = $pgsql->query('SELECT COUNT(*) AS c FROM "' . str_replace('"', '""', $table) . '"');
    echo sprintf("%s %d\n", $table, $stmt->fetch(PDO::FETCH_ASSOC)['c']);
}
