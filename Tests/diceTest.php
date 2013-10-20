<?php
include "dice.php";

    $dice = new Dice;
    $numRolls = 15;

    for($rolls = 0; $rolls < $numRolls; $rolls++)
    {
        $dice->roll();
        print "Roll: " . $dice->rollResult . " Hard: " . $dice->hard ."\n";

        if ($dice->rollResult < 2 || $dice->rollResult > 12)
        {
            print "error dice out of bounds";
        }

    }

?>
