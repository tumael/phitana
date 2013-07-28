<?php

namespace WebSocket\Application;

use Phitana;

/**
 * Websocket-Server tic-tac-toe.
 *
 * @author Carlos E. Caballero B. <cijkb.j@gmail.com>
 * @author Vladimir Cespedes L. <vladwelt@gmail.com>
 */
class PhitanaApplication extends Application
{
    public $list_users;
    public $list_rooms;

    public function __construct() {
        $this->list_users = array();
        $this->list_rooms = array();
    }

    public function onConnect($connection) {
        $id = $connection->getClientId();
        $user = new \Phitana\User($id, $connection);
        $this->list_users[$id] = $user;
        $this->broadcast($this->list_users());
    }

    public function onDisconnect($connection) {
        $id = $connection->getClientId();
        unset($this->list_users[$id]);
        $this->broadcast($this->disconnect($id));
    }

    public function onData($data, $connection) {
        $payload = $this->_decodeData($data);
        $action = $payload['action'];

        $id = $connection->getClientId();
        $user = $this->list_users[$id];

        echo ' --> ' . $data;

        $method = 'execute' . ucfirst($action);
        if (method_exists($this, $method)) {
            $connection->send($this->$method($payload['data'], $user));
        }
    }

    private function list_users() {
        $data = array();
        foreach ($this->list_users as $user) {
            $_user = new \stdClass();
            $_user->id = $user->getId();
            $_user->nick = $user->getNick();
            $_user->status = ($user->getStatus() == WAITING) ? 'waiting' : 'playing';
            $data[] = $_user;
        }

        return $this->_encodeData('list', $data);
    }

    private function disconnect($id) {
        return $this->_encodeData('logout', $id);
    }

    private function executeAuth($message, $user) {
        $user->setNick($message);

        $payload = new \stdClass();
        $payload->id = $user->getId();
        $payload->nick = $user->getNick();

        $this->broadcast($this->_encodeData('login', $payload));
        return $this->_encodeData('auth', 'success');
    }

    private function broadcast($message) {
        foreach ($this->list_users as $user) {
            $user->getConnection()->send($message);
        }
    }
}
