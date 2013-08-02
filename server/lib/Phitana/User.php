<?php

namespace Phitana;

define('WAITING', false);
define('PLAYING', true);

class User
{
    private $id;
    private $connection;
    private $nick;
    private $challenges;
    private $requests;

    // false -> waiting
    // true -> playing
    private $status;

    public function __construct($id, $connection) {
        $this->setId($id);
        $this->setConnection($connection);
        $this->setNick('');
        $this->setStatus(WAITING);
        $this->setChallenges(array());
        $this->setRequests(array());
    }

    public function getId() {
        return $this->id;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function getNick() {
        return $this->nick;
    }

    public function getChallenges() {
        return $this->challenges;
    }

    public function getRequests() {
        return $this->requests;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setConnection($connection) {
        $this->connection = $connection;
    }

    public function setNick($nick) {
        $this->nick = $nick;
    }

    public function setChallenges($challenges) {
        $this->challenges = $challenges;
    }

    public function setRequests($requests) {
        $this->requests = $requests;
    }

    private function setStatus($status) {
        $this->status = $status;
    }

    public function waiting() {
        $this->setStatus(FALSE);
    }

    public function playing() {
        $this->setStatus(TRUE);
    }

    public function addChallenge(\Phitana\User $user) {
        $this->challenges[$user->getId()] = $user;
    }

    public function addRequest(\Phitana\User $user) {
        $this->requests[$user->getId()] = $user;
    }

    public function removeChallenge($id) {
        unset($this->challenges[$id]);
    }

    public function removeRequest($id) {
        unset($this->requests[$id]);
    }

    public function clearChallenges() {
        $this->setChallenges(array());
    }

    public function clearRequests() {
        $this->setRequests(array());
    }

    public function isReady() {
        return !empty($this->nick);
    }

    public function sendMessage($message) {
        $this->connection->send($message);
    }

    public function getStdClass() {
        $std = new \stdClass();
        $std->id = $this->getId();
        $std->nick = $this->getNick();
        $std->status = $this->getStatus();
        return $std;
    }
}
