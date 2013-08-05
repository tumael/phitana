<?php

namespace WebSocket\Application;

use Phitana;

/**
 * Websocket-Server tic-tac-toe.
 *
 * @author Carlos E. Caballero B. <cijkb.j@gmail.com>
 */
class WhatchatApplication extends Application
{
    public $list_users;
    public $server = null;

    public function __construct() {
        $this->list_users = array();
    }

    public function setServer($server) {
        $this->server = $server;
        $this->log('ready!!');
    }

    public function onConnect($connection) {
        $id = $connection->getClientId();
        $user = new \Phitana\Whatchat\User($id, $connection);
        $this->list_users[$id] = $user;

        // Enviar peticion de conexion exitosa solo al usuario
        $user->sendMessage(
            $this->_encodeData('connect', true)
        );

        $this->log('[connect] ' . $id);
    }

    public function onDisconnect($connection) {
        $id = $connection->getClientId();
        $user = $this->list_users[$id];
        $ready = $user->isReady();

        unset($this->list_users[$id]);

        // Si establecio su nombre de usuario =>
        // Enviar un evento user-delete a todos los usuarios
        if ($ready) {
            $this->_broadcast(
                $this->_encodeData('user-delete', $id), $user
            );
        }

        $this->log('[user-delete]* ' . $id);
    }

    public function onData($data, $connection) {
        $payload = $this->_decodeData($data);
        $action = $payload['action'];
        $data = $payload['data'];

        $id = $connection->getClientId();
        $user = $this->list_users[$id];

        switch ($action) {
            case 'users-list':
                // Generar lista de usuarios
                $list = array();
                foreach ($this->list_users as $_user) {
                    if ($_user->id !== $user->id) {
                        $list[] = $_user;
                    }
                }
                $user->sendMessage(
                    $this->_encodeData('users-list', $list)
                );
                $this->log(
                    '[users-list] [' . count($this->list_users) . '] - ' . $id);
                break;
            case 'user-nick':
                $ready = $user->isReady();
                $user->nick = $data;
                // Notificar el cambio de nombre a los usuarios
                if (!$ready) {
                    $this->_broadcast(
                        $this->_encodeData(
                            'user-new',
                            $user
                        ), $user
                    );
                    $this->log('[user-new]* ' . $id);
                } else {
                    $this->_broadcast(
                        $this->_encodeData(
                            'user-nick',
                            $user
                        ), $user
                    );
                    $this->log('[user-nick]* ' . $id);
                }
                break;
        }
    }

    private function log($message) {
        if (!empty($this->server)) {
            $this->server->log('[WHATCHAT] ' . $message);
        }
    }

    private function _broadcast($message, $user) {
        foreach ($this->list_users as $_user) {
            if ($user->id <> $_user->id) {
                $_user->sendMessage($message);
            }
        }
    }
}
