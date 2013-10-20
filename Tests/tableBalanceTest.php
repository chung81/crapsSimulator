<?php
include_once "player.php";
include_once "crapsTable.php";

$player0 = new Player(300);

if ($player0->balance != 300)
{
    print "error balance incorrect \n";
}

$crapstable = new crapsTable;

$playerId = $crapstable->addPlayer($player0);

if($crapstable->tableBalance($playerId) != 0)
{
    print "error tablebalance is not 0";
}

$bet = $crapstable->players[$playerId]->createBet('passLine',5);
$crapstable->addBet($bet);

if($crapstable->tableBalance($playerId) != 5)
{
    print "error tablebalance is not 5";
}

if(count($crapstable->bets['passLine']) != 1)
{
    print "error too many pass line bets: " . count($crapstable->bets['passLine']);
}

foreach($crapstable->bets['passLine'] as $bet)
{
    if($bet->playerId == $playerId)
    {
        if($bet->betSize != 5)
        {
            print "Error bet not equal to 5";
        }
    }
}

$bet = $crapstable->players[$playerId]->createBet('passLineOdds',10);
$crapstable->addBet($bet);

if($crapstable->tableBalance($playerId) != 15)
{
    print "error tablebalance is not 15";
}

$bet = $crapstable->players[$playerId]->createBet('field',10);
$crapstable->addBet($bet);

if($crapstable->tableBalance($playerId) != 25)
{
    print "error tablebalance is not 25";
}
