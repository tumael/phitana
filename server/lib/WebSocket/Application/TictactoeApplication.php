<?php

namespace WebSocket\Application;

use Phitana;

/**
 * Websocket-Server tic-tac-toe.
 *
 * @author Carlos E. Caballero B. <cijkb.j@gmail.com>
 * @author Vladimir Cespedes L. <vladwelt@gmail.com>
 */
class TictactoeApplication extends Application
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

        // TODO enviar peticion de conexion exitosa solo al usuario
        $user->sendMessage(
            $this->_encodeData('connect', true)
        );
    }

    public function onDisconnect($connection) {
        $id = $connection->getClientId();
        $ready = $this->list_users[$id]->isReady();

        unset($this->list_users[$id]);

        // TODO si establecio su nombre de usuario =>
        // TODO enviar un evento user-delete a todos los usuarios
        if ($ready) {
            $this->_broadcast(
                $this->_encodeData('user-delete', $id)
            );
        }
    }

    public function onData($data, $connection) {
        $payload = $this->_decodeData($data);
        $action = $payload['action'];
        $data = $payload['data'];

        $id = $connection->getClientId();
        $user = $this->list_users[$id];

        switch ($action) {
            case 'users-list':
                // TODO generar lista de usuarios
                $list = array();
                foreach ($this->list_users as $user) {
                    $list[] = $user->getStdClass();
                }
                $user->sendMessage('user-list', $list);
                break;
            case 'user-nick':
                $ready = $user->isReady();
                $user->setNick($data);
                // TODO notificar el cambio de nombre a los usuarios
                if (!$ready) {
                    $this->_broadcast(
                        $this->_encodeData('user-new', $user->getStdClass())
                    );
                } else {
                    $this->_broadcast(
                        $this->_encodeData('user-nick', $user->getStdClass())
                    );
                }
                break;
            case 'user-challenge';
                // TODO marcar el desafio de $user a data.id
                $opponent = $this->list_users[$data];
                $user->addRequest($opponent);
                $opponent->addChallenge($user);

                $opponent->sendMessage(
                    $this->_encodeData('user-challenge', $user->getId())
                );
                break;
            case 'user-accept':
                // TODO jugar
                $opponent = $this->list_users[$data];
                $opponent->sendMessage(
                    $this->_encodeData('user-accept', $user->getId())
                );
                break;
        }
    }

    private function _broadcast($message) {
        foreach ($this->list_users as $user) {
            $user->sendMessage($message);
        }
    }
}
