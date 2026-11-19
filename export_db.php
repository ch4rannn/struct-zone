<?php
/**
 * StructZone Database Export Script
 * Exports the entire database to a SQL file
 */
require_once __DIR__ . '/admin/includes/db.php';
$pdo = getDB();

// Get database name
$stmt = $pdo->query("SELECT DATABASE()");
$dbName = $stmt->fetchColumn();

$outputFile = __DIR__ . '/structzone_data_' . date('Y-m-d_H-i') . '.sql';
$handle = fopen($outputFile, 'w');

// Header
fwrite($handle, "-- StructZone Database Export\n");
fwrite($handle, "-- Date: " . date('Y-m-d H:i:s') . "\n");
fwrite($handle, "-- Database: " . $dbName . "\n\n");
fwrite($handle, "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n");
fwrite($handle, "SET time_zone = \"+00:00\";\n\n");

// Get all tables
$tables = [];
$stmt = $pdo->query("SHOW TABLES");
while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
    $tables[] = $row[0];
}

foreach ($tables as $table) {
    echo "Exporting table: $table\n";
    
    // Drop table if exists
    fwrite($handle, "DROP TABLE IF EXISTS `$table`;\n");
    
    // Create table structure
    $stmt = $pdo->query("SHOW CREATE TABLE `$table`");
    $row = $stmt->fetch(PDO::FETCH_NUM);
    fwrite($handle, $row[1] . ";\n\n");
    
    // Insert data
    $stmt = $pdo->query("SELECT * FROM `$table`");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Build INSERT statement
        $keys = array_keys($row);
        $keys = array_map(function($k) { return "`$k`"; }, $keys);
        
        $values = array_values($row);
        $values = array_map(function($v) use ($pdo) {
            if ($v === null) return "NULL";
            return $pdo->quote($v);
        }, $values);
        
        $sql = "INSERT INTO `$table` (" . implode(", ", $keys) . ") VALUES (" . implode(", ", $values) . ");\n";
        fwrite($handle, $sql);
    }
    fwrite($handle, "\n");
}

fclose($handle);

echo "\nDatabase exported successfully to:\n" . basename($outputFile);
?>
