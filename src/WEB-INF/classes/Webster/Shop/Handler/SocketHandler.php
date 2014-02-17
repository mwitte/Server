<?php

namespace Webster\Shop\Handler;

use Ratchet\ConnectionInterface;
use TechDivision\WebSocketContainer\Handlers\HandlerConfig;
use TechDivision\WebSocketContainer\Handlers\AbstractHandler;


class SocketHandler extends AbstractHandler
{

    public function __construct()
    {

    }

    /**
     * @param HandlerConfig $config
     */
    public function init(HandlerConfig $config)
    {
        parent::init($config);
        error_log('SocketHandler: init');
    }

    /**
     * @see \Ratchet\ComponentInterface::onOpen()
     */
    public function onOpen(ConnectionInterface $connection)
    {
        error_log('SocketHandler: onOpen');
    }

    /**
     * @see \Ratchet\MessageInterface::onMessage()
     */
    public function onMessage(ConnectionInterface $connection, $message)
    {
        error_log('SocketHandler: onMessage');
        error_log(var_export($message, true));
    }

    /**
     * @see \Ratchet\ComponentInterface::onClose()
     */
    public function onClose(ConnectionInterface $connection)
    {
        error_log('SocketHandler: onClose');
    }

    /**
     * @see \Ratchet\ComponentInterface::onError()
     */
    public function onError(ConnectionInterface $connection, \Exception $e)
    {
        error_log('SocketHandler: onError');
        error_log($e->__toString());
        $connection->close();
    }
}