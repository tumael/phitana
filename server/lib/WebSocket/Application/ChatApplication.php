<?php

namespace WebSocket\Application;

use Phitana;

/**
 * Websocket-Server tic-tac-toe.
 *
 * @author Carlos E. Caballero B. <cijkb.j@gmail.com>
 * @author Vladimir Cespedes L. <vladwelt@gmail.com>
 */
class ChatApplication extends Application
{
    public $list_users;

    public function __construct() {
        $this->list_users = array();
    }

    public function onConnect($connection) {
        $id = $connection->getClientId();

        $user = new \stdClass();
        $user->id = $id;
        $user->connection = $connection;

        $this->list_users[$id] = $user;

        $this->broadcast($this->_encodeData('message', 'nuevo usuario conectado'));
    }

    public function onDisconnect($connection) {
        $id = $connection->getClientId();
        unset($this->list_users[$id]);

        $this->broadcast($this->_encodeData('logout', $id));
    }

    public function onData($data, $connection) {
        $payload = $this->_decodeData($data);
        $this->broadcast($this->_encodeData('message', $payload['data']));
    }

    private function broadcast($message) {
        foreach ($this->list_users as $user) {
            $user->connection->send($message);
        }
    }
}
