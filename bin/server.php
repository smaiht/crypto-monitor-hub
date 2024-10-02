<?php
require __DIR__ . '/../vendor/autoload.php';

use Workerman\Worker;
use Workerman\Connection\TcpConnection;
use Workerman\Redis\Client as RedisClient;
use Channel\Server as ChannelServer;
use Channel\Client as ChannelClient;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();


$redisHost = $_ENV['REDIS_HOST'];
$redisPort = $_ENV['REDIS_PORT'];
$channelServerHost = $_ENV['CHANNEL_SERVER_HOST'];
$channelServerPort = $_ENV['CHANNEL_SERVER_PORT'];
$wsHost = $_ENV['WS_HOST'];
$wsPort = $_ENV['WS_PORT'];

$exchanges = explode(',', $_ENV['EXCHANGES']);

$redisChannels = array_map(function($exchange) {
    return "tickers:$exchange";
}, $exchanges);
// $redisChannels = ['tickers:*']; // a little bit slower, but no need to restart the main process after adding a new exchange

// Channel Server
$channel_server = new ChannelServer($channelServerHost, $channelServerPort);

// Redis Worker
$redisWorker = new Worker();
$redisWorker->name = 'RedisWorker';
$redisWorker->onWorkerStart = function() use ($redisWorker, $redisChannels, $channelServerHost, $channelServerPort, $redisHost, $redisPort) {
    ChannelClient::connect($channelServerHost, $channelServerPort);

    $redis = new RedisClient("redis://$redisHost:$redisPort");
    $redis->psubscribe($redisChannels, function ($pattern, $channel, $message) {
        ChannelClient::publish('redis_message', $message);
    });
};

// WS Worker
$wsWorker = new Worker("websocket://$wsHost:$wsPort");
$wsWorker->count = 1;
$wsWorker->name = 'WebSocketWorker';
$wsWorker->onWorkerStart = function() use ($wsWorker, $channelServerHost, $channelServerPort) {
    ChannelClient::connect($channelServerHost, $channelServerPort);

    ChannelClient::on('redis_message', function($data) use ($wsWorker) {
        foreach ($wsWorker->connections as $connection) {
            $connection->send($data);
        }
    });
};

$wsWorker->onConnect = function(TcpConnection $connection) {
    echo "New WebSocket connection: " . $connection->id . "\n";
};

$wsWorker->onMessage = function(TcpConnection $connection, $data) {
    echo "Received message from WebSocket client: " . $connection->id . "\n";
};

$wsWorker->onClose = function(TcpConnection $connection) {
    echo "WebSocket connection closed: " . $connection->id . "\n";
};

Worker::runAll();
