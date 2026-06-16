<?php
$projectRoot = realpath(__DIR__ . '/..');
$sqlitePath = $projectRoot . '/database/database.sqlite';
if (! file_exists($sqlitePath)) { echo "MISSING SQLITE\n"; exit(1); }
$sqlite = new PDO('sqlite:' . $sqlitePath);
$sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sqlite->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
$pgsql = new PDO('pgsql:host=acela.proxy.rlwy.net;port=11517;dbname=railway;sslmode=require', 'postgres', 'yscEJJBfLsOyBdZYQEDOwRTIHWHJquHs');
$pgsql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pgsql->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
$localTables = [];
foreach ($sqlite->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name") as $row) { $localTables[] = $row['name']; }
$remoteTables = [];
foreach ($pgsql->query("SELECT table_name FROM information_schema.tables WHERE table_schema='public' ORDER BY table_name") as $row) { $remoteTables[] = $row['table_name']; }
echo "LOCAL TABLES: " . implode(', ', $localTables) . "\n";
echo "REMOTE TABLES: " . implode(', ', $remoteTables) . "\n";
$missingInRemote = array_diff($localTables, $remoteTables);
$extraInRemote = array_diff($remoteTables, $localTables);
if ($missingInRemote) { echo "MISSING IN REMOTE: " . implode(', ', $missingInRemote) . "\n"; }
if ($extraInRemote) { echo "EXTRA IN REMOTE: " . implode(', ', $extraInRemote) . "\n"; }
$ignore = ['cache','sessions'];
$allPassed = true;
foreach ($localTables as $table) {
    $localCount = (int) $sqlite->query('SELECT COUNT(*) AS c FROM "' . str_replace('"', '""', $table) . '"')->fetchColumn();
    $remoteCount = (int) $pgsql->query('SELECT COUNT(*) AS c FROM "' . str_replace('"', '""', $table) . '"')->fetchColumn();
    if ($localCount !== $remoteCount) {
        $status = in_array($table, $ignore, true) ? 'OK (runtime diff)' : 'MISMATCH';
        echo sprintf("%s: local=%d remote=%d => %s\n", $table, $localCount, $remoteCount, $status);
        if (! in_array($table, $ignore, true)) { $allPassed = false; }
    } else {
        echo sprintf("%s: local=%d remote=%d => MATCH\n", $table, $localCount, $remoteCount);
    }
}
if ($allPassed) { echo "ALL PERSISTENT TABLES MATCH.\n"; } else { echo "PERSISTENT TABLE MISMATCH FOUND.\n"; exit(1); }
