<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Configurações RabbitMQ
$RABBITMQ_HOST = 'localhost';
$RABBITMQ_PORT = 5672;
$RABBITMQ_USER = 'guest';
$RABBITMQ_PASS = 'guest';
$QUEUE_NAME = 'alerts';

// Função para publicar mensagem na fila
function publishMessage($messageBody) {
    global $RABBITMQ_HOST, $RABBITMQ_PORT, $RABBITMQ_USER, $RABBITMQ_PASS, $QUEUE_NAME;

    $connection = new AMQPStreamConnection($RABBITMQ_HOST, $RABBITMQ_PORT, $RABBITMQ_USER, $RABBITMQ_PASS);
    $channel = $connection->channel();

    // Declara a fila
    $channel->queue_declare('alerts', false, false, false, false);

    $msg = new AMQPMessage($messageBody, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
    $channel->basic_publish($msg, '', $QUEUE_NAME);

    $channel->close();
    $connection->close();
}

// Roteamento simples
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Rota GET /equipments
if ($method === 'GET' && $path === '/equipments') {
    header('Content-Type: application/json');

    $equipments = [
        ['id' => 1, 'name' => 'Compressor', 'status' => 'available'],
        ['id' => 2, 'name' => 'Generator', 'status' => 'in use'],
        ['id' => 3, 'name' => 'Drill', 'status' => 'maintenance'],
    ];

    echo json_encode($equipments);
    exit;
}

// Rota POST /dispatch
if ($method === 'POST' && $path === '/dispatch') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['message']) || !isset($input['level'])) {
        http_response_code(400);
        echo json_encode(['error' => 'message and level are required']);
        exit;
    }

    $payload = json_encode([
        'message' => $input['message'],
        'level' => $input['level'],
        'timestamp' => date('c')
    ]);

    publishMessage($payload);

    header('Content-Type: application/json');
    echo json_encode(['status' => 'Message dispatched to RabbitMQ']);
    exit;
}

// Caso rota não exista
http_response_code(404);
echo json_encode(['error' => 'Not Found']);
exit;
