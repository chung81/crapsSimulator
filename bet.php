<?php
class Bet
{
    public $location;
    public $betSize;
    public $playerId;
    public $off;
    public $numWins;

    function __construct($location, $betSize, $playerId, $on=true)
    {
        $this->location = $location;
        $this->betSize = $betSize;
        $this->playerId = $playerId;
        $this->on = $on;
        $this->numWins = 0;
    }
}
