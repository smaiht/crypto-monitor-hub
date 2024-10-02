<?php

require __DIR__ . '/../vendor/autoload.php';

use Workerman\Worker;
use Workerman\Connection\AsyncTcpConnection;
use Workerman\Lib\Timer;



$worker = new Worker('websocket://0.0.0.0:8080');
$worker->count = 1;

$worker->onWorkerStart = function($worker) {
    $context = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
        ]
    ];
    $con = new AsyncTcpConnection('ws://ws.okx.com:8443/ws/v5/public', $context);
    $con->transport = 'ssl';

    $con->onConnect = function($con) {
        echo "Connected to OKX WebSocket\n";

        $subscription = json_encode([
            'op' => 'subscribe',
            'args' => [
                [
                    'channel' => 'tickers',
                    'instId' => 'BTC-USDT'
                ]
            ]
        ]);
        $con->send($subscription);
    };

    $con->onMessage = function($con, $data) use ($worker) {
        $message = json_decode($data, true);
        if (isset($message['data'])) {

            echo '<pre>';
            var_dump($data);
            echo '<pre>';
        }
    };

    $con->onError = function($con, $code, $msg) {
        echo "Error: $msg\n";
    };

    $con->onClose = function($con) {
        echo "Connection closed\n";
    };

    $con->connect();

    Timer::add(30, function() use ($con) {
        $con->send('ping');
    });
};

Worker::runAll();
