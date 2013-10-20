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

checkTableBalance($crapstable,$playerId,0);

// Place a passline bet
$bet = $crapstable->players[$playerId]->createBet('passLine',5);
$crapstable->addBet($bet);

checkTableBalance($crapstable,$playerId,5);

if($crapstable->point != 0)
{
    print "error point is already set";
}

$crapstable->roll(2);

checkTableBalance($crapstable,$playerId,0);
checkPlayerBalance($crapstable,$playerId,295);

// Place a passline bet
$bet = $crapstable->players[$playerId]->createBet('passLine',5);
$crapstable->addBet($bet);


// Place a field bet
$bet = $crapstable->players[$playerId]->createBet('field',5);
$crapstable->addBet($bet);

checkTableBalance($crapstable,$playerId,10);
checkPlayerBalance($crapstable,$playerId,285);
checkBet($crapstable,$playerId,'field',5);

$crapstable->roll(5);

// Place odds bet
$bet = $crapstable->players[$playerId]->createBet('passLineOdds',10);
$crapstable->addBet($bet);

checkTableBalance($crapstable,$playerId,15);
checkPlayerBalance($crapstable,$playerId,275);

// Place  six
$bet = $crapstable->players[$playerId]->createBet('placeSix',6);
$crapstable->addBet($bet);

checkTableBalance($crapstable,$playerId,21);
checkPlayerBalance($crapstable,$playerId,269);

$crapstable->roll(6);

checkTableBalance($crapstable,$playerId,21);
checkPlayerBalance($crapstable,$playerId,276);


// Place  five
$bet = $crapstable->players[$playerId]->createBet('placeNine',5);
$crapstable->addBet($bet);

checkTableBalance($crapstable,$playerId,26);
checkPlayerBalance($crapstable,$playerId,271);

$crapstable->roll(9);

checkTableBalance($crapstable,$playerId,26);
checkPlayerBalance($crapstable,$playerId,278);

$crapstable->roll(7);
checkTableBalance($crapstable,$playerId,0);
checkPlayerBalance($crapstable,$playerId,278);
if($crapstable->point != 0)
{
    print "error point is set";
}

// Place  six
$bet = $crapstable->players[$playerId]->createBet('placeSix',6);
$crapstable->addBet($bet);

checkTableBalance($crapstable,$playerId,6);
checkPlayerBalance($crapstable,$playerId,272);

// Turn off the bet
$crapstable->bets['placeSix'][$playerId]->on = false;

$crapstable->roll(6);
checkTableBalance($crapstable,$playerId,6);
checkPlayerBalance($crapstable,$playerId,272);

// turn the bet back on
$crapstable->bets['placeSix'][$playerId]->on = true;

$crapstable->roll(6);
checkTableBalance($crapstable,$playerId,6);
checkPlayerBalance($crapstable,$playerId,279);
checkBet($crapstable,$playerId,'placeSix',6);

$crapstable->betUpDown('placeSix',$playerId);
checkBet($crapstable,$playerId,'placeSix',12);



$crapstable->printTableBets();
$crapstable->roll(4);
$crapstable->roll(6);
checkPlayerBalance($crapstable,$playerId,287);

$bet = $crapstable->players[$playerId]->createBet('placeFive',5);
$crapstable->addBet($bet);
checkBet($crapstable,$playerId,'placeFive',5);
checkPlayerBalance($crapstable,$playerId,282);

$crapstable->roll(5);
checkPlayerBalance($crapstable,$playerId,289);

$crapstable->betUpDown('placeFive',$playerId);
checkPlayerBalance($crapstable,$playerId,284);
checkBet($crapstable,$playerId,'placeFive',10);

$crapstable->roll(5);
checkPlayerBalance($crapstable,$playerId,298);
checkBet($crapstable,$playerId,'placeFive',10);

$crapstable->betUpDown('placeFive',$playerId,false);
checkBet($crapstable,$playerId,'placeFive',5);
$crapstable->betUpDown('placeFive',$playerId,false);
checkBet($crapstable,$playerId,'placeFive',0);
$crapstable->roll(7);

print "\n";
print "Table Bets:\n";
$crapstable->printTableBets();
print_r($crapstable->shooterRollCountHistory);


function checkTableBalance($crapstable,$playerId, $balance)
{
    if($crapstable->tableBalance($playerId) != $balance)
    {
        print "\nError tablebalance is not " . $balance . " actual balance " . $crapstable->tableBalance($playerId) . "\n\n";
    }
}

function checkPlayerBalance($crapstable,$playerId,$balance)
{
    if($crapstable->players[$playerId]->balance != $balance)
    {
        print "\nError balance is not " . $balance . " actual balance " . $crapstable->players[$playerId]->balance . "\n\n";
    }
}

function checkBet($crapstable,$playerId,$location,$balance)
{
    if(!($crapstable->getBet($location,$playerId) == $balance))
    {
        print "\nError Bet is not " . $balance . " actual Bet " . $crapstable->getBet($location,$playerId) . "\n\n";
    }
}
