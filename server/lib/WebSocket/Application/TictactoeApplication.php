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
    public $server = null;

    public function __construct() {
        $this->list_users = array();
        $this->list_rooms = array();
    }

    public function setServer($server) {
        $this->server = $server;
        $this->log('ready!!');
    }

    public function onConnect($connection) {
        $id = $connection->getClientId();
        $user = new \Phitana\User($id, $connection);
        $this->list_users[$id] = $user;

        // TODO enviar peticion de conexion exitosa solo al usuario
        $user->sendMessage(
            $this->_encodeData('connect', true)
        );

        $this->log('[connect] ' . $id);
    }

    public function onDisconnect($connection) {
        $id = $connection->getClientId();
        $ready = $this->list_users[$id]->isReady();

        unset($this->list_users[$id]);

        // TODO si establecio su nombre de usuario =>
        // TODO enviar un evento user-delete a todos los usuarios
        if ($ready) {
            $this->_broadcast(
                $this->_encodeData('user-delete', $id), $id
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
                // TODO generar lista de usuarios
                $list = array();
                foreach ($this->list_users as $_user) {
                    if ($_user->getId() !== $user->getId()) {
                        $list[] = $_user->getStdClass();
                    }
                }
                $user->sendMessage(
                    $this->_encodeData('users-list', $list)
                );
                $this->log('[users-list] ' . count($this->list_users) . ' - ' . $id);
                break;
            case 'user-nick':
                $ready = $user->isReady();
                $user->setNick($data);
                // TODO notificar el cambio de nombre a los usuarios
                if (!$ready) {
                    $this->_broadcast(
                        $this->_encodeData('user-new', $user->getStdClass()), $id
                    );
                    $this->log('[user-new]* ' . $id);
                } else {
                    $this->_broadcast(
                        $this->_encodeData('user-nick', $user->getStdClass()), $id
                    );
                    $this->log('[user-nick]* ' . $id);
                }
                break;
            case 'user-challenge';
                if ($data <> $id) {
                    // TODO marcar el desafio de $user a data.id
                    $opponent = $this->list_users[$data];
                    $user->addRequest($opponent);
                    $opponent->addChallenge($user);

                    $opponent->sendMessage(
                        $this->_encodeData('user-challenge', $user->getId())
                    );
                    $this->log('[user-challenge] ' . $id);
                }
                break;
            case 'user-accept':
                if ($data <> $id) {
                    // TODO jugar
                    $opponent = $this->list_users[$data];
                    $opponent->sendMessage(
                        $this->_encodeData('user-accept', $user->getId())
                    );
                    $this->log('[user-accept] ' . $id);
                }
                break;
        }
    }

    private function log($message) {
        if (!empty($this->server)) {
            $this->server->log('[TICTACTOE] ' . $message);
        }
    }

    private function _broadcast($message, $id) {
        foreach ($this->list_users as $user) {
            if ($user->getId() <> $id) {
                $user->sendMessage($message);
            }
        }
    }
}
