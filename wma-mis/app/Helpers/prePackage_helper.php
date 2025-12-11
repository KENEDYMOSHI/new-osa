<?php

//percentage calculator
function percentage($percent, $theValue)
{
    return ($percent / 100) * $theValue;
}


//calculate standard deviation
function standardDeviation(array $array): float
{
    $size = count($array);
    $mean = array_sum($array) / $size;
    $squares = array_map(function ($x) use ($mean) {
        return pow($x - $mean, 2);
    }, $array);

    return sqrt(array_sum($squares) / ($size - 1));
}

//function to calculate tolerable deficiency of the nominal quantity

function tolerableDeficiency($nQ)
{
    if ($nQ == 0 && $nQ <= 49) {
        return percentage(9, $nQ);
    } else if ($nQ >= 50 && $nQ <= 99) {
        return 4.5;
    } else if ($nQ >= 100 && $nQ <= 199) {
        return percentage(4.5, $nQ);
    } else if ($nQ >= 200 && $nQ <= 299) {
        return 9;
    } else if ($nQ >= 300 && $nQ <= 499) {
        return percentage(3, $nQ);
    } else if ($nQ >= 500 && $nQ <= 999) {
        return 15;
    } else if ($nQ >= 1000 && $nQ <= 9999) {
        return percentage(1.5, $nQ);
    } else if ($nQ >= 10000 && $nQ <= 14999) {
        return 150;
    } else if ($nQ > 15000) {
        return percentage(1, $nQ);
    }
}

function nominalQtyPercent($nQ)
{
    if ($nQ == 0 && $nQ <= 49) {
        return 9;
    } else if ($nQ >= 100 && $nQ <= 199) {
        return 4.5;
    } else if ($nQ >= 300 && $nQ <= 499) {
        return 3;
    } else if ($nQ >= 1000 && $nQ <= 9999) {
        return 1.5;
    } else if ($nQ > 15000) {
        return 1;
    } else {
        return 0;
    }
}

function nominalQtyGram($nQ)
{
    if ($nQ >= 50 && $nQ <= 99) {
        return 4.5;
    } else if ($nQ >= 200 && $nQ <= 299) {
        return 9;
    } else if ($nQ >= 500 && $nQ <= 999) {
        return 15;
    } else if ($nQ >= 10000 && $nQ <= 15000) {
        return 150;
    } else {
        return 0;
    }
}


function checkPositiveOrNegative($individualError)
{
    if ($individualError >= 0) {
        return 'Positive ' . $individualError;
    } else {
        return 'Negative ' . $individualError;
    }
}
