<?php

$ifEmpty = in_array('--if-empty', $argv, true);

$projectRoot = realpath(__DIR__ . '/..');
$sqlitePath = $projectRoot . '/database/database.sqlite';
if (! file_exists($sqlitePath)) {
    if ($ifEmpty) {
        echo "Local SQLite file not found. Skipping optional SQLite import.\n";
        exit(0);
    }

    fwrite(STDERR, "ERROR: Local SQLite file not found at $sqlitePath\n");
    exit(1);
}

$databaseUrl = getenv('DATABASE_URL') ?: getenv('DB_URL') ?: getenv('DATABASE_PUBLIC_URL') ?: '';
$urlParts = $databaseUrl ? parse_url($databaseUrl) : [];

$pgHost = getenv('DB_HOST') ?: getenv('PGHOST') ?: ($urlParts['host'] ?? '');
$pgPort = getenv('DB_PORT') ?: getenv('PGPORT') ?: ($urlParts['port'] ?? '5432');
$pgDatabase = getenv('DB_DATABASE') ?: getenv('PGDATABASE') ?: (isset($urlParts['path']) ? ltrim($urlParts['path'], '/') : 'railway');
$pgUser = getenv('DB_USERNAME') ?: getenv('PGUSER') ?: ($urlParts['user'] ?? 'postgres');
$pgPassword = getenv('DB_PASSWORD') ?: getenv('PGPASSWORD') ?: getenv('DATABASE_PASSWORD') ?: ($urlParts['pass'] ?? '');
$sslMode = getenv('DB_SSLMODE') ?: 'require';

foreach ($argv as $arg) {
    if ($arg !== $argv[0] && $arg !== '--if-empty' && $arg !== '') {
        $pgPassword = $arg;
    }
}

if ($pgHost === '' || $pgPassword === '') {
    fwrite(STDERR, "ERROR: PostgreSQL connection variables are incomplete.\n");
    exit(1);
}

echo "Using local SQLite: $sqlitePath\n";
echo "Connecting to PostgreSQL: $pgUser@{$pgHost}:{$pgPort}/{$pgDatabase}\n";

$sqlite = new PDO('sqlite:' . $sqlitePath);
$sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sqlite->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$pgsql = new PDO("pgsql:host={$pgHost};port={$pgPort};dbname={$pgDatabase};sslmode={$sslMode}", $pgUser, $pgPassword);
$pgsql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pgsql->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$localTables = [];
foreach ($sqlite->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name") as $row) {
    $localTables[] = $row['name'];
}

$remoteTables = [];
foreach ($pgsql->query("SELECT table_name FROM information_schema.tables WHERE table_schema='public' ORDER BY table_name") as $row) {
    $remoteTables[] = $row['table_name'];
}

$orderedTables = [
    'migrations',
    'cache',
    'cache_locks',
    'personal_access_tokens',
    'sessions',
    'admins',
    'users',
    'destinations',
    'famous_tourist_spots',
    'tour_packages',
    'promo_packages',
    'bookings',
    'payments',
    'reviews',
    'report_histories',
];

$tables = array_values(array_intersect($orderedTables, $localTables, $remoteTables));

if ($ifEmpty) {
    $contentTables = array_values(array_intersect(['users', 'admins', 'destinations', 'tour_packages', 'promo_packages', 'famous_tourist_spots'], $remoteTables));
    foreach ($contentTables as $table) {
        $count = (int) $pgsql->query('SELECT COUNT(*) FROM "' . str_replace('"', '""', $table) . '"')->fetchColumn();
        if ($count > 0) {
            echo "PostgreSQL already contains app data. Skipping SQLite import.\n";
            exit(0);
        }
    }
}

if (empty($tables)) {
    fwrite(STDERR, "ERROR: No matching SQLite/PostgreSQL tables found to migrate.\n");
    exit(1);
}

echo "Truncating remote tables...\n";
$pgsql->beginTransaction();
$pgsql->exec('TRUNCATE TABLE ' . implode(', ', array_map(fn($t) => '"' . $t . '"', $tables)) . ' RESTART IDENTITY CASCADE');
$pgsql->commit();

echo "Migrating tables...\n";
$pgsql->beginTransaction();
foreach ($tables as $table) {
    echo "  - $table\n";
    $sqliteColumns = [];
    $stmt = $sqlite->query("PRAGMA table_info('" . str_replace("'", "''", $table) . "')");
    foreach ($stmt->fetchAll() as $row) {
        $sqliteColumns[] = $row['name'];
    }

    $remoteColumns = [];
    $columnStmt = $pgsql->prepare("SELECT column_name FROM information_schema.columns WHERE table_schema = 'public' AND table_name = ? ORDER BY ordinal_position");
    $columnStmt->execute([$table]);
    foreach ($columnStmt->fetchAll() as $row) {
        $remoteColumns[] = $row['column_name'];
    }

    $columns = array_values(array_intersect($sqliteColumns, $remoteColumns));

    if (empty($columns)) {
        echo "    Skipping: no matching columns found\n";
        continue;
    }

    $columnList = implode(', ', array_map(fn($c) => '"' . $c . '"', $columns));
    $placeholders = implode(', ', array_fill(0, count($columns), '?'));
    $insertSql = sprintf('INSERT INTO "%s" (%s) VALUES (%s)', $table, $columnList, $placeholders);
    $insertStmt = $pgsql->prepare($insertSql);

    $rows = $sqlite->query('SELECT ' . implode(', ', array_map(fn($c) => '"' . $c . '"', $columns)) . ' FROM "' . $table . '"')->fetchAll();
    echo "    Rows to copy: " . count($rows) . "\n";

    foreach ($rows as $row) {
        $insertStmt->execute(array_values($row));
    }
}
$pgsql->commit();

echo "Resetting sequences...\n";
foreach ($tables as $table) {
    $stmt = $pgsql->prepare("SELECT column_name FROM information_schema.columns WHERE table_schema = 'public' AND table_name = ? AND column_default LIKE 'nextval(%' LIMIT 1");
    $stmt->execute([$table]);
    $sequenceColumn = $stmt->fetchColumn();
    if ($sequenceColumn) {
        $seqNameStmt = $pgsql->query("SELECT pg_get_serial_sequence('" . str_replace("'", "''", $table) . "', '" . str_replace("'", "''", $sequenceColumn) . "')");
        $seqName = $seqNameStmt->fetchColumn();
        if ($seqName) {
            $maxStmt = $pgsql->query('SELECT MAX("' . str_replace('"', '""', $sequenceColumn) . '") AS max_id FROM "' . str_replace('"', '""', $table) . '"');
            $max = $maxStmt->fetchColumn();
            $max = $max !== null ? (int) $max : 0;
            $pgsql->exec("SELECT pg_catalog.setval('" . str_replace("'", "''", $seqName) . "', " . max($max, 1) . ', ' . ($max > 0 ? 'true' : 'false') . ')');
            echo "    Sequence reset for $table.$sequenceColumn\n";
        }
    }
}

echo "Migration complete.\n";

foreach ($tables as $table) {
    $count = $pgsql->query('SELECT COUNT(*) AS c FROM "' . str_replace('"', '""', $table) . '"')->fetchColumn();
    echo "  $table: $count\n";
}
