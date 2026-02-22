<?php

function httpRequest($method, $url, $body = null, $headers = []) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    $httpHeaders = [];
    foreach ($headers as $k => $v) {
        $httpHeaders[] = "$k: $v";
    }
    if ($body !== null) {
        $json = json_encode($body);
        $httpHeaders[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $resp = curl_exec($ch);
    $info = curl_getinfo($ch);
    $errno = curl_errno($ch);
    $error = curl_error($ch);
    curl_close($ch);

    return [
        'http_code' => $info['http_code'] ?? 0,
        'body' => $resp,
        'errno' => $errno,
        'error' => $error
    ];
}

$base = 'http://127.0.0.1:8000';
$apiKey = 'test-api-key';
$results = [];

// 1) Login (public + api key header)
$login = httpRequest('POST', $base . '/api/login', ['email'=>'user1@example.com','password'=>'password'], ['X-API-KEY'=>$apiKey]);
$results[] = ['name'=>'POST /api/login','request'=>['email'=>'user1@example.com','password'=>'password'],'response'=>$login];
$token = null;
$userId = null;
if ($login['http_code'] === 200) {
    $data = json_decode($login['body'], true);
    if (isset($data['token'])) {
        $token = $data['token'];
    }
    if (isset($data['user']['id'])) {
        $userId = $data['user']['id'];
    }
}

if (!$token) {
    file_put_contents(__DIR__ . '/endpoint_results.txt', "Login failed, stopping.\n" . print_r($results, true));
    echo "Login failed. Check server and API key.\n";
    exit(1);
}

$authHeader = ['X-API-KEY'=>$apiKey, 'Authorization'=>'Bearer ' . $token];

// helper to fetch numeric IDs from listing
$otherUserId = null;
$generalChatId = null;

// initial calls to fetch dynamic values
$resp = httpRequest('GET', $base . '/api/usuarios', null, $authHeader);
if ($resp['http_code'] === 200) {
    $list = json_decode($resp['body'], true);
    if (isset($list['users']) && is_array($list['users'])) {
        foreach ($list['users'] as $u) {
            if ($u['id'] !== $userId) {
                $otherUserId = $u['id'];
                break;
            }
        }
    }
}

$resp = httpRequest('GET', $base . '/api/general', null, $authHeader);
if ($resp['http_code'] === 200) {
    $g = json_decode($resp['body'], true);
    if (isset($g['chat']['id'])) {
        $generalChatId = $g['chat']['id'];
    }
}

$tests = [
    ['method'=>'GET','path'=>'/api/home','auth'=>true],
    ['method'=>'GET','path'=>'/api/general','auth'=>true],
    ['method'=>'GET','path'=>'/api/usuarios','auth'=>true],
];
// show current user if we know id
if ($userId) {
    $tests[] = ['method'=>'GET','path'=>'/api/usuarios/' . $userId,'auth'=>true];
    $tests[] = ['method'=>'PUT','path'=>'/api/usuarios/' . $userId,'auth'=>true,'body'=>['name'=>'User One Updated From Script']];
}

// create another user
$tests[] = ['method'=>'POST','path'=>'/api/usuarios','auth'=>false,'body'=>['name'=>'Test User','email'=>'testuser_for_script@example.com','password'=>'pwd']];

// messaging
if ($generalChatId) {
    $tests[] = ['method'=>'POST','path'=>'/api/mensaje?chat_id=' . $generalChatId,'auth'=>true,'body'=>['text'=>'Script test message']];
    $tests[] = ['method'=>'GET','path'=>'/api/mensaje?chat_id=' . $generalChatId,'auth'=>true];
}

// follow / block / invite using otherUserId
if ($otherUserId) {
    $tests[] = ['method'=>'POST','path'=>'/api/seguir','auth'=>true,'body'=>['user_id'=>$otherUserId]];
    $tests[] = ['method'=>'POST','path'=>'/api/bloquear','auth'=>true,'body'=>['user_id'=>$otherUserId]];
    $tests[] = ['method'=>'POST','path'=>'/api/invitar','auth'=>true,'body'=>['user_id'=>$otherUserId]];
    $tests[] = ['method'=>'POST','path'=>'/api/amistad/solicitar','auth'=>true,'body'=>['user_id'=>$otherUserId]];
}

// remaining static tests
$tests[] = ['method'=>'GET','path'=>'/api/seguidos','auth'=>true];
$tests[] = ['method'=>'GET','path'=>'/api/bloqueados','auth'=>true];
$tests[] = ['method'=>'GET','path'=>'/api/privado','auth'=>true];
$tests[] = ['method'=>'POST','path'=>'/api/actualizar','auth'=>true,'body'=>['lat'=>40.7128,'lng'=>-74.0060]];
$tests[] = ['method'=>'GET','path'=>'/api/perfil','auth'=>true];
$tests[] = ['method'=>'GET','path'=>'/api/amistad/pendientes','auth'=>true];
$tests[] = ['method'=>'POST','path'=>'/api/logout','auth'=>true,'body'=>new stdClass()];


foreach ($tests as $t) {
    $url = $base . $t['path'];
    $headers = $t['auth'] ? $authHeader : ['X-API-KEY'=>$apiKey];
    $body = $t['body'] ?? null;
    $res = httpRequest($t['method'], $url, $body, $headers);
    $results[] = ['name' => $t['method'] . ' ' . $t['path'], 'request' => $body, 'response' => $res];
    echo $t['method'] . ' ' . $t['path'] . ' => ' . $res['http_code'] . "\n";
}

// Save full results
file_put_contents(__DIR__ . '/endpoint_results.txt', json_encode($results, JSON_PRETTY_PRINT));

echo "Resultados guardados en tests/endpoint_results.txt\n";

?>