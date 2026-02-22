<?php
$db = new PDO('sqlite:C:/Users/Admin/Desktop/antojes/var/data.db');
$stmt = $db->query('SELECT id,type,is_active FROM chat');
foreach ($stmt as $row) {
    echo implode('|', $row) . "\n";
}
