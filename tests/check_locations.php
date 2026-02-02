<?php
$base='http://127.0.0.1:8000';
$apiKey='test-api-key';
$ch=curl_init();
curl_setopt($ch, CURLOPT_URL, $base.'/api/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-API-KEY: '.$apiKey, 'Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['email'=>'user1@example.com','password'=>'password']));
$resp=curl_exec($ch);
$data=json_decode($resp,true);
$token=$data['token'] ?? null;
if(!$token){ echo "Login failed\n"; exit(1);} 
$ch2=curl_init();
curl_setopt($ch2, CURLOPT_URL, $base.'/api/usuarios/locations');
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_HTTPHEADER, ['X-API-KEY: '.$apiKey, 'Authorization: Bearer '.$token]);
$r2=curl_exec($ch2);
echo $r2 . PHP_EOL;
