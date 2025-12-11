<?php
function getWeights()
{
    return  $data['weights'] = [

        [
            'capacity' => 'Not Exceeding 100 Grams',
            'price'    => 500
        ],
        [
            'capacity' => '101 Grams to 500 Grams',
            'price'    => 1000
        ],
        [
            'capacity' => '501 Grams to 2 Kg',
            'price'    => 1500
        ],
        [
            'capacity' => '3 Kg to 5 Kg',
            'price'    => 2000
        ],
        [
            'capacity' => '6 Kg to 50 Kg',
            'price'    => 15000
        ],
        [
            'capacity' => '51 Kg to 200 Kg',
            'price'    => 20000
        ],
        [
            'capacity' => '201 Kg to 500 Kg',
            'price'    => 30000
        ],

        [
            'capacity' => 'Exceeding 500 Kg',
            'price'    => 100000
        ],

    ];
}