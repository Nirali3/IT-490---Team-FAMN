<?php
require_once('get_host_info.inc');
require 'vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class rabbitMQServer
{
    private $connection;
    private $channel;
    private $exchange;
    private $queue;
    private $callback;

    function __construct($iniFile, $server)
    {
        $config = getHostInfo([$iniFile]);

        if (!isset($config[$server])) {
            die("Error: Missing server configuration in $iniFile.");
        }

        $serverConfig = $config[$server];

        try {
            $this->connection = new AMQPStreamConnection(
                $serverConfig["BROKER_HOST"],
                $serverConfig["BROKER_PORT"],
                $serverConfig["USER"],
                $serverConfig["PASSWORD"],
                $serverConfig["VHOST"]
            );

            $this->channel = $this->connection->channel();
            $this->exchange = $serverConfig["EXCHANGE"];
            $this->queue = $serverConfig["QUEUE"];

            // Declare queue
            $this->channel->queue_declare($this->queue, false, true, false, false);
        } catch (Exception $e) {
            die("RabbitMQ Server Connection Error: " . $e->getMessage());
        }
    }

    function process_requests($callback)
    {
        $this->callback = $callback;

        echo "Waiting for messages...\n";

        $this->channel->basic_consume(
            $this->queue,
            '',
            false,
            true,
            false,
            false,
            function ($msg) {
                $request = json_decode($msg->body, true);
                $response = call_user_func($this->callback, $request);

                if (!empty($msg->get('reply_to'))) {
                    $replyMsg = new AMQPMessage(json_encode($response), [
                        'correlation_id' => $msg->get('correlation_id')
                    ]);
                    $this->channel->basic_publish($replyMsg, '', $msg->get('reply_to'));
                }
            }
        );

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }
}

class rabbitMQClient
{
    private $connection;
    private $channel;
    private $exchange;
    private $queue;
    private $response;
    private $correlationId;

    function __construct($iniFile, $server)
    {
        $config = getHostInfo([$iniFile]);

        if (!isset($config[$server])) {
            die("Error: Missing server configuration in $iniFile.");
        }

        $serverConfig = $config[$server];

        try {
            $this->connection = new AMQPStreamConnection(
                $serverConfig["BROKER_HOST"],
                $serverConfig["BROKER_PORT"],
                $serverConfig["USER"],
                $serverConfig["PASSWORD"],
                $serverConfig["VHOST"]
            );

            $this->channel = $this->connection->channel();
            $this->exchange = $serverConfig["EXCHANGE"];
            $this->queue = $serverConfig["QUEUE"];

            $this->channel->queue_declare($this->queue, false, true, false, false);
        } catch (Exception $e) {
            die("RabbitMQ Client Connection Error: " . $e->getMessage());
        }
    }

    function send_request($message)
    {
        $this->correlationId = uniqid();
        $callback_queue = $this->queue . "_response";

        $this->channel->queue_declare($callback_queue, false, false, true, true);
        $responseQueue = function ($msg) {
            if ($msg->get('correlation_id') == $this->correlationId) {
                $this->response = json_decode($msg->body, true);
            }
        };

        $this->channel->basic_consume(
            $callback_queue,
            '',
            false,
            true,
            false,
            false,
            $responseQueue
        );

        $json_message = json_encode($message);
        $msg = new AMQPMessage($json_message, [
            'correlation_id' => $this->correlationId,
            'reply_to' => $callback_queue
        ]);

        $this->channel->basic_publish($msg, '', $this->queue);

        while (!$this->response) {
            $this->channel->wait();
        }

        return $this->response;
    }
}
?>

