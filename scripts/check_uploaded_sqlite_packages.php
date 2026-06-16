<?php

$path = __DIR__ . '/../database/database.sqlite';

$pdo = new PDO('sqlite:' . $path);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$counts = [
    'sqlite_total' => (int) $pdo->query('select count(*) from tour_packages')->fetchColumn(),
    'sqlite_active' => (int) $pdo->query("select count(*) from tour_packages where status = 'active'")->fetchColumn(),
    'sqlite_bolinao' => (int) $pdo->query("select count(*) from tour_packages where status = 'active' and location like '%Bolinao%'")->fetchColumn(),
];

foreach ($counts as $key => $value) {
    echo $key . '=' . $value . PHP_EOL;
}

