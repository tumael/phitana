<?php

namespace Phitana;

class Room
{
    private $turn;
    private $collisions;
    private $player1;
    private $board1;
    private $player2;
    private $board2;

    public function __construct(\Phitana\User $player1, \Phitana\User $player2) {
        $this->setPlayer($player1, 1);
        $this->setPlayer($player2, 2);
        $this->setTurn(1);
        $this->cleanBoard();
    }

    public function setPlayer(\Phitana\User $player, $number) {
        $attr = 'player' . $number;
        $this->$attr = $player;
    }

    public function setTurn($turn) {
        $this->turn = $turn;
    }

    public function getTurn() {
        return $this->turn;
    }

    private function changeTurn() {
        switch ($this->turn) {
            case 1:
                $this->turn = 2;
                break;
            case 2:
                $this->turn = 1;
                break;
        }
    }

    private function getUserTurn() {
        switch ($this->turn) {
            case 1:
                return $this->player1;
            case 2:
                return $this->player2;
        }
    }

    public function play(\Phitana\User $player, $x, $y) {
        if ($player->getId() === $this->getUserTurn()->getId()) {
            if (!$this->collisions[$x][$y]) {
                $this->collisions[$x][$y] = true;
                $this->board[$x.$y] = array($x, $y);
                $this->changeTurn();
            }
        }
    }

    public function cleanBoard() {
        $this->board1 = array();
        $this->board2 = array();
        $this->collisions = array(
            array(false, false, false),
            array(false, false, false),
            array(false, false, false),
        );
    }

    public function validate($number) {
        $str = 'board' . $number;
        $array = $this->$str;
        
        if (count($array) < 3) {
            return false;
        }

        if ((isset($this->board['00']) && isset($this->board['01']) && isset($this->board['02']))
            || (isset($this->board['10']) && isset($this->board['11']) && isset($this->board['12']))
            || (isset($this->board['20']) && isset($this->board['21']) && isset($this->board['22']))
            || (isset($this->board['00']) && isset($this->board['10']) && isset($this->board['20']))
            || (isset($this->board['01']) && isset($this->board['11']) && isset($this->board['21']))
            || (isset($this->board['02']) && isset($this->board['12']) && isset($this->board['22']))
            || (isset($this->board['00']) && isset($this->board['11']) && isset($this->board['22']))
            || (isset($this->board['02']) && isset($this->board['11']) && isset($this->board['20']))) {
            return true;
        }
        return false;
    }
}
