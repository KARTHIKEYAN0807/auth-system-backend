<?php

$redisHost = getenv('REDISHOST');
$redisPort = getenv('REDISPORT');
$redisPass = getenv('REDISPASSWORD');

if (!$redisHost || !$redisPort) {
    http_response_code(500);
    die("Redis env vars missing");
}

$redis = new Redis();

try {
    if ($redisPass) {
        $redis->connect($redisHost, (int)$redisPort);
        $redis->auth($redisPass);
    } else {
        $redis->connect($redisHost, (int)$redisPort);
    }
} catch (Exception $e) {
    http_response_code(500);
    die("Redis connection failed");
}
