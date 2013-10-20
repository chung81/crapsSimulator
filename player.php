<?php
include_once 'Bet.php';
class Player
{
    public $playerId;
    public $playerName;
    public $balance;
    public $message;
    public $winnings;
    public $tableLoss;
    public $netGain;

    function __construct($startingBalance =200)
    {
        $this->balance = $startingBalance;
        $this->winnings = 0;
    }

    function createBet($location, $betSize)
    {
        $bet = new Bet($location, $betSize, $this->playerId);
        $this->balance -= $betSize;
        return $bet;
    }
}
