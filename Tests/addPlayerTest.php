<?php
include_once "player.php";
include_once "crapsTable.php";

$player0 = new Player(300);

if ($player0->balance != 300)
{
    print "error balance incorrect \n";
}

$crapstable = new crapsTable;

$crapstable->addPlayer($player0);

if(count($crapstable->players)!=1)
{
    print "error player count too high";
}

foreach($crapstable->players as $player)
{
    if($player->playerId != 0)
    {
        print "error player id not 0";
    }

}
