<?php

namespace Phitana;

class Room
{
    private $player1;
    private $player2;
    private $turn;
    private $board;

    public function __construct(\Phitana\User $player1, \Phitana\User $player 2) {
        $this->setPlayer($player, 1);
        $this->setPlayer($player, 2);
        $this->setTurn(1);
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
            $this->board[$x][$y] = $this->getTurn();
            $this->changeTurn();
        }
    }

    public function cleanBoard() {
        $this->board = array(
            array(0, 0, 0),
            array(0, 0, 0),
            array(0, 0, 0),
        );
    }

    
}

