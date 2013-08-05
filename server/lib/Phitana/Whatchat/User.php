<?php

namespace Phitana\Whatchat;

class User
{
    public $id;
    public $connection;
    public $nick;
    public $status;

    public function __construct($id, $connection) {
        $this->id = $id;
        $this->connection = $connection;
        $this->nick = '';
        $this->status = '';
    }

    public function isReady() {
        return !empty($this->nick);
    }

    public function sendMessage($message) {
        $this->connection->send($message);
    }

//    public function getStdClass() {
//        $std = new \stdClass();
//        $std->id = $this->getId();
//        $std->nick = $this->getNick();
//        $std->status = $this->getStatus();
//        return $std;
//    }
}
