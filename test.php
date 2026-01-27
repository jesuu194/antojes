<?php

try {
    $db = new PDO('sqlite:./var/data.db');
    echo "SQLite connection successful\n";
    $db->exec("CREATE TABLE test (id INTEGER PRIMARY KEY, name TEXT)");
    echo "Table created\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}