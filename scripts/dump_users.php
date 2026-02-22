<?php
$db = new PDO('sqlite:C:/Users/Admin/Desktop/antojes/var/data.db');
$stmt = $db->query('SELECT id,email,password FROM user');
foreach ($stmt as $row) {
    echo implode('|', $row) . "\n";
}
