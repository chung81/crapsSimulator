<?php
class Dice
{
    public $rollResult;
    public $hard;

    function __construct()
    {
    }

    public function roll()
    {
        // reset the roll result
        $this->rollResult = 0;
        $dice1 = rand(1,6);
        $dice2 = rand(1,6);

        $this->rollResult = $dice1 + $dice2;
        if ($dice1 == $dice2)
        {
            $this->hard = true;
        }
        else
        {
            $this->hard = false;
        }
    }
}
