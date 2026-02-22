<?php
echo "PHP funciona en Render<br>";

try {
    $db = new PDO('sqlite:/tmp/test.db');
    echo "SQLite connection successful<br>";
    $db->exec("CREATE TABLE IF NOT EXISTS test (id INTEGER PRIMARY KEY, name TEXT)");
    echo "Table created or already exists<br>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}