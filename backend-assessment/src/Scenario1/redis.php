<?php


require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;


$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

function redis()
{
    $redisHost = $_ENV['REDIS_HOST'] ?? 'localhost';
    $redisPort = $_ENV['REDIS_PORT'] ?? 6379;

    $redis = new Redis();
    $redis->connect($redisHost, $redisPort);
    return $redis;
}


// function redis()
// {
//     $redis = new Redis();
//     $redis->connect('localhost', 6379);
//     return $redis;
// }
