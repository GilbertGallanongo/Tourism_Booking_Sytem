<?php
$pg = new PDO('pgsql:host=acela.proxy.rlwy.net;port=11517;dbname=railway;sslmode=require', 'postgres', 'yscEJJBfLsOyBdZYQEDOwRTIHWHJquHs');
$pg->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pg->exec("ALTER TABLE bookings DROP CONSTRAINT IF EXISTS bookings_status_check;");
$pg->exec("ALTER TABLE bookings ADD CONSTRAINT bookings_status_check CHECK (status IN ('pending','confirmed','cancelled','completed','approved','declined','cancellation_pending')); ");
echo "Constraint updated\n";
$stmt = $pg->query("SELECT conname, pg_get_constraintdef(c.oid) AS definition FROM pg_constraint c JOIN pg_class t ON t.oid = c.conrelid WHERE t.relname='bookings' AND c.contype='c'");
foreach ($stmt as $row) {
    echo $row['conname']." => ".$row['definition']."\n";
}
