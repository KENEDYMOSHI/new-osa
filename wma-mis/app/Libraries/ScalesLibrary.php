<?php

namespace App\Libraries;


class ScalesLibrary
{
    public function weights()
    {
        return   $data['weights'] = [

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


    public function denominations()
    {
        return  $data['denominations'] = [

            [
                'capacity' => '0 Kg to 20 Kg',
                'price'    => 7000
            ],
            [
                'capacity' => '21 Kg to 50 Kg',
                'price'    => 10000
            ],
            [
                'capacity' => '51 Kg to 100 Kg',
                'price'    => 20000
            ],
            [
                'capacity' => '101 Kg to 200 Kg',
                'price'    => 30000
            ],
            [
                'capacity' => '201 Kg to 300 Kg',
                'price'    => 40000
            ],
            [
                'capacity' => '301 Kg to 500 Kg',
                'price'    => 50000
            ],
            [
                'capacity' => '501 Kg to 2000 Kg',
                'price'    => 60000
            ],
            [
                'capacity' => '2,001 Kg to 5,000 Kg',
                'price'    => 150000
            ],
            [
                'capacity' => '5,001 Kg to 10,000 Kg',
                'price'    => 175000
            ],
            [
                'capacity' => '10001 Kg to 30,000 Kg',
                'price'    => 200000
            ],
            [
                'capacity' => '30,001 Kg to 50,000 Kg',
                'price'    => 300000
            ],
            [
                'capacity' => '30,001 Kg to 50,000 Kg',
                'price'    => 300000
            ],
            [
                'capacity' => '50,001 Kg to 10,000 Kg',
                'price'    => 500000
            ],
            [
                'capacity' => '50,001 Kg to 10,000 Kg',
                'price'    => 500000
            ],
            [
                'capacity' => '100,001 Kg to 200,000 Kg',
                'price'    => 700000
            ],
            [
                'capacity' => 'Exceeding 200,000 Kg',
                'price'    => 800000
            ],

        ];
    }



    public function korobois()
    {

        return  $data['korobois'] = [

            [
                'capacity' => 'Not Exceeding 250 Milliliters',
                'price'    => 500
            ],
            [
                'capacity' => '251 Milliliters to 2 Litters',
                'price'    => 1000
            ],
            [
                'capacity' => '3 Liters to 5 Litters',
                'price'    => 2000
            ],
            [
                'capacity' => '6 Liters to 10 Litters',
                'price'    => 4000
            ],
            [
                'capacity' => '11 Liters to 20 Litters',
                'price'    => 6000
            ],
            [
                'capacity' => '21 Liters to 50 Litters',
                'price'    => 10000
            ],
            [
                'capacity' => '51 Liters to 100 Litters',
                'price'    => 20000
            ],
            [
                'capacity' => '101 Liters to 500 Litters',
                'price'    => 30000
            ],
            [
                'capacity' => '501 Liters to 1,000 Litters',
                'price'    => 60000
            ],
            [
                'capacity' => '1,001 Liters to 2,000 Litters',
                'price'    => 100000
            ],
            [
                'capacity' => '2001 Liters to 5000 Litters',
                'price'    => 1000
            ],
            [
                'capacity' => '5001 Liters to 10000 Litters',
                'price'    => 240000
            ],
            [
                'capacity' => 'Exceeding 10,000Litters',
                'price'    => 300000
            ],

        ];
    }

    public function scalesList()
    {
        return $data['scales'] = [
            'Counter Scale',
            'Platform Scale',
            'Spring Balance',
            'Digital Scale',
            'WeighBridge',
            'Beam Scale',
            'Steel Yard',
            'Axel Weigher',
            'Balance',
            'Weights',


        ];
    }
}