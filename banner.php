<?php
require_once 'Hit.php';

$config = parse_ini_file('config.ini', true);

$hit = new Hit($config['DB']);
$hit->addOrIncreaseHit(
    $_SERVER['REMOTE_ADDR'],
    $_SERVER['HTTP_USER_AGENT'],
    $_SERVER['HTTP_REFERER'] ?? '/'
);

header('Content-type: image/png');
readfile($config['IMAGE']['name']);