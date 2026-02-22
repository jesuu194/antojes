<?php
$db = new PDO('sqlite:C:/Users/Admin/Desktop/antojes/var/data.db');
foreach($db->query('PRAGMA table_info(chat)') as $row){
    echo implode(' | ', $row) . "\n";
}
