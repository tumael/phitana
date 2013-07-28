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
        $this->broadcast($this->list_users());
    }

    public function onData($data, $connection) {
        $obj = json_decode($data);

        $id = $connection->getClientId();
        $user = $this->list_users[$id];

        $action = $obj->type;
        $method = 'execute' . ucfirst($action);

        $connection->send($this->$method($obj->message, $user));
    }

    private function list_users() {
        $stdClass = new \stdClass();

        $list = array();
        foreach ($this->list_users as $user) {
            $_user = new \stdClass();
            $_user->id = $user->getId();
            $_user->nick = $user->getNick();
            $_user->status = ($user->getStatus()) ? 'waiting' : 'playing';
            $list[] = $_user;
        }

        $stdClass->type = 'list';
        $stdClass->message = $list;

        return json_encode($stdClass);
    }

    private function executeAuth($message, $user) {
        $obj = json_decode($message);
        $user->setNick($obj->nick);
        
        $stdClass = new \stdClass();
        $stdClass->type = 'auth';
        $stdClass->message = 'success';

        return json_encode($stdClass);
    }

    private function broadcast($message) {
        foreach ($this->list_users as $user) {
            $user->getConnection()->send($message);
        }
    }
}
