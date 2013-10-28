<?php
include_once "dice.php";
include_once "player.php";

class CrapsTable
{
    public $point;
    public $dice;
    public $lastRoll;

    // Point stats
    public $pointsHit;
    public $crapOuts;

    // Number stats
    public $rollHistory;
    public $shooterRollHistory;
    public $shooterRollCountHistory;
    public $shooterRollCount;

    public $tableMessage;


    // 
    // Bets
    //
    public $tableMinimum;
    public $bets = array();
    public $betsOnOff = array();

    public $playerCount;
    public $players = array();

    function __construct()
    {
        $this->tableMinimum = 5;
        $this->point = 0;
        $this->dice = new Dice;

        $this->playerCount = 0;

        $this->pointsHit = 0;
        $this->crapOuts = 0;

        $this->two =0;
        $this->three =0;
        $this->four =0;
        $this->five =0;
        $this->six =0;
        $this->seven =0;
        $this->eight =0;
        $this->nine =0;
        $this->ten =0;
        $this->eleven =0;
        $this->twelve =0;

        $this->bets = array(
            'passLine' => array(),
            'passLineOdds' => array(),
            'field' => array(),
            'placeFour' => array(),
            'placeFive' => array(),
            'placeSix' => array(),
            'placeEight' => array(),
            'placeNine' => array(),
            'placeTen' => array(),
            'come' => array(),
            'come4' => array(),
            'come5' => array(),
            'come6' => array(),
            'come8' => array(),
            'come9' => array(),
            'come10' => array(),
            'comeOdds4' => array(),
            'comeOdds5' => array(),
            'comeOdds6' => array(),
            'comeOdds8' => array(),
            'comeOdds9' => array(),
            'comeOdds10' => array(),
        );

        $this->betsOnOff = array(
            'placeFour',
            'placeFive',
            'placeSix',
            'placeEight',
            'placeNine',
            'placeTen',
        );

        foreach($this->players as $player)
        {
            $player->message = '';
        }

    }

    function addBet($bet)
    {
        if(array_key_exists($bet->location,$this->bets))
        {
            if(empty($this->bets[$bet->location][$bet->playerId]))
            {
                $this->bets[$bet->location][$bet->playerId] = $bet;
            }
            else
            {
                // replace the bet with the new one and credit back the old bet to the player
                //
                print "Bet already on the table, replacing with new bet";
                $this->players[$bet->playerId]->balance += $this->bets[$bet->location][$bet->playerId]->betSize;
                $this->bets[$bet->location][$bet->playerId] = $bet;
            }
        }
        else
        {
            print "Bet location does not exist: ". $bet->location . "\n";
        }
    }

    function betUpDown($location, $playerId,$up=true)
    {
        if(array_key_exists($location,$this->bets))
        {
            if(!empty($this->bets[$location][$playerId]))
            {
                if($up == true)
                {
                    if($location == 'placeSix' || $location == 'placeEight')
                    {
                        $this->players[$playerId]->balance -= $this->tableMinimum+1;
                        $this->bets[$location][$playerId]->betSize += $this->tableMinimum+1;
                    }
                    else
                    {
                        $this->players[$playerId]->balance -= $this->tableMinimum;
                        $this->bets[$location][$playerId]->betSize += $this->tableMinimum;
                    }
                }
                else
                {
                    if($location == 'placeSix' || $location == 'placeEight')
                    {
                        if($this->bets[$location][$playerId]->betSize > $this->tableMinimum+1)
                        {
                            $this->players[$playerId]->balance += $this->tableMinimum+1;
                            $this->bets[$location][$playerId]->betSize -= $this->tableMinimum+1;
                        }
                        else
                        {
                            $this->players[$playerId]->balance += $this->tableMinimum+1;
                            unset($this->bets[$location][$playerId]);
                        }
                    }
                    else
                    {
                        if($this->bets[$location][$playerId]->betSize > $this->tableMinimum)
                        {
                            $this->players[$playerId]->balance += $this->tableMinimum;
                            $this->bets[$location][$playerId]->betSize -= $this->tableMinimum;
                        }
                        else
                        {
                            $this->players[$playerId]->balance += $this->tableMinimum;
                            unset($this->bets[$location][$playerId]);
                        }
                    }
                }

            }
            else
            {
                //
                // no bet to press up or down
                //
                print "No bet to press";
            }
        }
        else
        {
            print "Bet location does not exist: ". $bet->location . "\n";
        }


    }


    function checkPoint($rollResult)
    {
        if ($this->point == $rollResult)
        {
            // Pay the line
            $this->passWin();
            $this->PassWinOdds($this->point);

            $this->pointsHit++;
            $this->point = 0;


            print "turning bets off\n";
            $this->turnBets($this->betsOnOff, false);

            return 1;
        }
        else
        {
            return 0;
        }
    }

    function turnBets($locations, $on = true)
    {
        foreach($locations as $location)
        {
            foreach($this->bets[$location] as $bet)
            {
                if(!empty($bet))
                {
                    if($on == false)
                    {
                        $bet->on = false;
                    }
                    elseif($on == true)
                    {
                        $bet->on = true;
                    }
                }
            }
        }
    }

    function countRoll($rollResult)
    {

        $this->rollHistory[] = $rollResult;
        $this->shooterRollCount++;

    }

    function setPoint()
    {
        if($this->point == 0 && ($this->checkCrap($this->lastRoll) != 1))
        {
            $this->point = $this->lastRoll;
            $this->turnBets($this->betsOnOff, true);
            print "Setting Point: " . $this->point . "\n";
        }
    }

    // Checks to see if the number can be a point
    function checkCrap($number)
    {
        if($number == 2 || 
            $number == 3 ||
            $number == 7 ||
            $number == 11 ||
            $number ==12)
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    function roll($rollResult = 0)
    {
        foreach($this->players as $player)
        {
            $player->message = '';
            $player->netGain = 0;
        }

        if($rollResult == 0)
        {
            $this->dice->roll();
            $rollResult = $this->dice->rollResult;
        }
        print "Rolling... ".  $rollResult . "\n";
        $this->lastRoll = $rollResult;
        
        $this->countRoll($rollResult);


        switch ($rollResult)
        {
            case 2:
                $this->processtwo();
                break;
            case 3:
                // crap
                $this->processThree();
                break;
            case 4:
                $this->processFour();
                break;
            case 5:
                $this->processFive();
                break;
            case 6:
                $this->processSix();
                break;
            case 7:
                $this->processSeven();
                break;
            case 8:
                $this->processEight();
                break;
            case 9:
                $this->processNine();
                break;
            case 10:
                $this->processTen();
                break;
            case 11:
                $this->processEleven();
                break;
            case 12:
                $this->processTwelve();
                break;
        }

        // We check for the point last so that if the point is hit it can auto turn off
        // the bets
        if ($this->point != 0)
        {
            $this->checkPoint($rollResult);
        }
        else 
        {
            $this->setPoint();
        }

        $this->processCome();
        //
        // Pay out the table winnings
        foreach($this->players as $player)
        {
            $player->balance += $player->winnings;

            $netGain = $player->winnings - $player->tableLoss;
            print "Player " . $player->playerId . " Net gain: " . $netGain . "\n";

            $player->winnings = 0;
            $player->tableLoss = 0;
            $player->netGain = $netGain;
        }
    }

    function processTwo()
    {
        if($this->point==0)
        {
            $this->tableLoss('passLine');
        }
        $this->tableLoss('come');
        $this->fieldWin();
    }

    function processThree()
    {
        if($this->point==0)
        {
            $this->tableLoss('passLine');
        }
        $this->tableLoss('come');
        $this->fieldWin();
    }

    function processFour()
    {
        // Point, Field, Hard?
        $this->placeWin('placeFour');
        $this->fieldWin();
    }

    function processFive()
    {
        // Point, No Field
        $this->placeWin('placeFive');
        $this->tableLoss('field');
    }

    function processSix()
    {
        // Point, No Field, Hard?
        $this->placeWin('placeSix');
        $this->tableLoss('field');
    }

    function processSeven()
    {
        if($this->point > 0)
        {
            $this->crapOut();
        }
        else
        {
            $this->passWin();
        }
        $this->comeWin();
    }

    function processEight()
    {
        // Point, No Field, Hard?
        $this->placeWin('placeEight');
        $this->tableLoss('field');
    }

    function processNine()
    {
        // Point, Field
        $this->placeWin('placeNine');
        $this->fieldWin();
    }

    function processTen()
    {
        // Point, Field, Hard?
        $this->placeWin('placeTen');
        $this->fieldWin();
    }

    function processEleven()
    {
        // Yo
        if($this->point==0)
        {
            $this->passWin();
        }
        $this->comeWin();
        $this->fieldWin();
    }

    function processTwelve()
    {
        // crap
        if($this->point==0)
        {
            $this->tableLoss('passLine');
        }
        $this->tableLoss('come');
        $this->fieldWin($rollResult);
    }

    function getOdds($point)
    {
        switch ($point)
        {
        case 4:
        case 10: 
            $odds =2;
            break;
        case 5:
        case 9:
            $odds = 1.5;
            break;
        case 6:
        case 8:
            $odds = 1.2;
            break;
        }

        return $odds;

    }

    function crapOut()
    {
        $this->crapOuts++;
        $this->point = 0;
        $this->shooterRollCountHistory[] = $this->shooterRollCount;
        $this->shooterRollCount = 0; 

        //
        // Clear the bets
        //

        $this->tableLoss('passLine');
        $this->tableLoss('passLineOdds');
        $this->tableLoss('field');
        $this->tableLoss('placeFour');
        $this->tableLoss('placeFive');
        $this->tableLoss('placeSix');
        $this->tableLoss('placeEight');
        $this->tableLoss('placeNine');
        $this->tableLoss('placeTen');

    }

    function passWin()
    {
        foreach($this->bets['passLine'] as $passLineBet)
        {
            if(!empty($passLineBet))
            {
                if($passLineBet->on)
                {
                    // Add back the pass line
                    $this->players[$passLineBet->playerId]->balance += $passLineBet->betSize;

                    // Place the winning in a holding to pay at end of roll function
                    $this->players[$passLineBet->playerId]->winnings += $passLineBet->betSize;

                    $this->players[$passLineBet->playerId]->message .= "Pass Line Win: " . $passLineBet->betSize . ',';

                    // Clear the bet
                    $this->bets['passLine'][$passLineBet->playerId] = NULL;
                }
            }
        }

    }

    function processCome()
    {
        // 
        // Pay the odds for the come bets
        //
        if($this->checkCrap($this->lastRoll) != 1)
        {
            $odds = $this->getOdds($this->lastRoll);

            $comeBetWinNumber = 'comeOdds' . $this->lastRoll;
            foreach($this->bets[$comeBetWinNumber] as $comeOddsBet)
            {
                if(!empty($comeOddsBet))
                {
                    if($comeOddsBet->on)
                    {
                        // Add back the pass line odds
                        $this->players[$comeOddsBet->playerId]->balance += $comeOddsBet->betSize;

                        // Place the winnings in a holding to pay at the end of the roll function
                        $this->players[$comeOddsBet->playerId]->winnings += $comeOddsBet->betSize * $odds;

                        $winSize = $comeOddsBet->betSize * $odds;
                        $this->players[$comeOddsBet->playerId]->message .= "Come Odds Win: " . $winSize . ',';

                        // clear the bet
                        $this->bets[$comeBetWinNumber][$comeOddsBet->playerId] = NULL;
                    }
                }
            }


            // 
            // Pay the line for the come bets
            //
            $comeBetWinNumber = 'come' . $this->lastRoll;
            foreach($this->bets[$comeBetWinNumber] as $comeLineBet)
            {
                if(!empty($comeLineBet))
                {
                    if($comeLineBet->on)
                    {
                        // Add back the pass line
                        $this->players[$comeLineBet->playerId]->balance += $comeLineBet->betSize;

                        // Place the winning in a holding to pay at end of roll function
                        $this->players[$comeLineBet->playerId]->winnings += $comeLineBet->betSize;

                        $this->players[$comeLineBet->playerId]->message .= "Come Line Win: " . $comeLineBet->betSize . ',';

                        // Clear the bet
                        $this->bets[$comeBetWinNumber][$comeLineBet->playerId] = NULL;
                    }
                }
            }

            // move the come bets
            foreach($this->bets['come'] as $comeBet)
            {
                if(!empty($comeBet))
                {
                    $newBetLocation = 'come' . $this->lastRoll;
                    $comeBet->location = $newBetLocation;
                    $this->bets[$newBetLocation][$comeBet->playerId] = $comeBet;
                    $this->bets['come'][$comeBet->playerId] = NULL;
                }
            }

        }

    
    
    }

    function comeWin()
    {
        foreach($this->bets['come'] as $comeBet)
        {
            if(!empty($comeBet))
            {
                if($comeBet->on)
                {
                    // Add back the pass line
                    $this->players[$comeBet->playerId]->balance += $comeBet->betSize;

                    // Place the winning in a holding to pay at end of roll function
                    $this->players[$comeBet->playerId]->winnings += $comeBet->betSize;

                    $this->players[$comeBet->playerId]->message .= "Come Win: " . $comeBet->betSize . ',';

                    // Clear the bet
                    $this->bets['come'][$comeBet->playerId] = NULL;
                }
            }
        }

    }

    function tableLoss($location)
    {
        foreach($this->bets[$location] as $bet)
        {
            if(!empty($bet))
            {
                // If the bets are in play
                if($bet->on)
                {
                    $this->players[$bet->playerId]->tableLoss += $bet->betSize;
                    $this->bets[$location][$bet->playerId] = NULL;
                    $this->players[$bet->playerId]->message .= "Table loss " . $bet->location .": " . $bet->betSize . ',';
                }
            }
        }
        
    }

    function passWinOdds($point)
    {
        $odds = $this->getOdds($point);
        foreach($this->bets['passLineOdds'] as $passLineOddsBet)
        {
            if(!empty($passLineOddsBet))
            {
                if($passLineOddsBet->on)
                {
                    // Add back the pass line odds
                    $this->players[$passLineOddsBet->playerId]->balance += $passLineOddsBet->betSize;

                    // Place the winnings in a holding to pay at the end of the roll function
                    $this->players[$passLineOddsBet->playerId]->winnings += $passLineOddsBet->betSize * $odds;

                    $winSize = $passLineOddsBet->betSize * $odds;
                    $this->players[$passLineOddsBet->playerId]->message .= "Pass Line Odds Win: " . $winSize . ',';
                }
            }
        }

        $this->bets['passLineOdds'] = array();

    }

    function fieldWin()
    {
        $rollResult = $this->lastRoll;
        $odds = 1;
        if($rollResult == 2)
        {
            // Pay Double
            $odds = 2;
        } 
        elseif ($rollResult == 12)
        {
            // Pay triple
            $odds = 3;
        }
        else
        {
            // Pay normal
            $odds = 1;
        }

        if(!empty($this->bets['field']))
        {
            foreach($this->bets['field'] as $fieldBet)
            {
                if(!empty($fieldBet))
                {
                    if($fieldBet->on)
                    {
                        // Add back the field bet for the win
                        $this->players[$fieldBet->playerId]->balance += $fieldBet->betSize;

                        // Place the winning in a holding to pay at end of roll function
                        $this->players[$fieldBet->playerId]->winnings += $fieldBet->betSize*$odds;

                        $winSize = $fieldBet->betSize*$odds;

                        $this->players[$fieldBet->playerId]->message .= "Field Win: " . $winSize . ',';
                    }
                }
            }
        }

        $this->bets['field'] = array();
    }


    function placeWin($location)
    {
        switch($location)
        {
        case 'placeFour':
        case 'placeTen':
            $odds = 9/5;
            break;
        case 'placeFive':
        case 'placeNine':
            $odds = 7/5;
            break;
        case 'placeSix':
        case 'placeEight':
            $odds = 7/6;
            break;
        }
        
        foreach($this->bets[$location] as $bet)
        {
            if(!empty($bet))
            {
                if($bet->on)
                {
                    // Place the winning in a holding to pay at end of roll function
                    $winSize = $bet->betSize * $odds;
                    $this->players[$bet->playerId]->winnings += $winSize;
                    $bet->numWins++;
                    $this->players[$bet->playerId]->message .= "Place: " . $winSize . ',';
                }
            }
        }


    }

    function tableBalance($playerId)
    {
        $tableBalance = 0;
        foreach($this->bets as $bets)
        {
            if(!empty($bets))
            {
                foreach($bets as $bet)
                {
                    if(!empty($bet))
                    {
                        if($bet->playerId == $playerId)
                        {
                            $tableBalance += $bet->betSize;
                        }
                    }
                }
            }
        }
        return $tableBalance;
    }

    function printTableBets()
    {
        if(!empty($this->players))
        {
            foreach($this->players as $player)
            {
                if(!empty($player))
                {
                    print "Player " . $player->playerId . "\n";
                    foreach($this->bets as $bet)
                    {
                        if(isset($bet[$player->playerId]))
                        {
                            print $bet[$player->playerId]->location . ": " . $bet[$player->playerId]->betSize . "\n";
                        }
                    }
                }
            }
        }
    }


    function getBet($location,$playerId)
    {
        if(isset($this->bets[$location][$playerId]))
        {
            return $this->bets[$location][$playerId]->betSize;
        }
        else
        {
            return 0;
        }

    }

    function addPlayer($player)
    {
        $player->playerId = $this->playerCount;
        $this->players[$this->playerCount] = $player;
        $this->playerCount++;
        return $player->playerId; 
    }

    function getRollCount($rollNumber = 0)
    {
        $countArray = array_count_values($this->rollHistory);
        if($rollNumber)
        {
            return $countArray[$rollNumber];
        }
        else
        {
            return $countArray;
        }
    }

    function averageShooterRollCount()
    {
        return array_sum($this->shooterRollCountHistory)/count($this->shooterRollCountHistory);
    }

    function shooterStandardDev()
    {
        $average = $this->averageShooterRollCount();
        $stdSum = 0;
        foreach($this->shooterRollCountHistory as $rollCount)
        {
            $stdSum += abs($rollCount - $average)^2;
        }
        return $stdSum/count($this->shooterRollCountHistory);
    }
}
