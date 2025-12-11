<?php
function renderRegions()
{
    $regions = [

        [
            'value' => 1,
            'region' => 'Arusha',
        ],
        // [
        //     'value' => 2,
        //     'region' => 'Dar-es-salaam',
        // ],
        [
            'value' => 3,
            'region' => 'Dodoma',
        ],
        [
            'value' => 4,
            'region' => 'Geita',
        ],
        [
            'value' => 5,
            'region' => 'Iringa',
        ],
        [
            'value' => 6,
            'region' => 'Kagera',
        ],
        // [
        //     'value' => 7,
        //     'region' => 'Kaskazini Pemba [North Pemba]',
        // ],
        // [
        //     'value' => 8,
        //     'region' => 'Kaskazini Unguja [North Zanzibar]',
        // ],
        [
            'value' => 9,
            'region' => 'Katavi',
        ],
        [
            'value' => 10,
            'region' => 'Kigoma',
        ],

        [
            'value' => 11,
            'region' => 'Kilimanjaro',
        ],
        // [
        //     'value' => 12,
        //     'region' => 'Kusini Pemba [South Pemba]',
        // ],
        // [
        //     'value' => 13,
        //     'region' => 'Kusini Unguja [South Zanzibar]',
        // ],
        [
            'value' => 14,
            'region' => 'Lindi',
        ],
        [
            'value' => 15,
            'region' => 'Manyara',
        ],
        [
            'value' => 16,
            'region' => 'Mara',
        ],
        [
            'value' => 17,
            'region' => 'Mbeya',
        ],
        // [
        //     'value' => 18,
        //     'region' => 'Mjini Magharibi [Urban West]',
        // ],
        [
            'value' => 19,
            'region' => 'Morogoro',
        ],
        [
            'value' => 20,
            'region' => 'Mtwara',
        ],
        [
            'value' => 21,
            'region' => 'Mwanza',
        ],
        [
            'value' => 22,
            'region' => 'Njombe',
        ],
        [
            'value' => 23,
            'region' => 'Pwani',
        ],
        [
            'value' => 24,
            'region' => 'Rukwa',
        ],
        [
            'value' => 25,
            'region' => 'Ruvuma',
        ],
        [
            'value' => 26,
            'region' => 'Shinyanga',
        ],
        [
            'value' => 27,
            'region' => 'Simiyu',
        ],
        [
            'value' => 28,
            'region' => 'Singida',
        ],
        [
            'value' => 29,
            'region' => 'Songwe',
        ],
        [
            'value' => 30,
            'region' => 'Tabora',
        ],
        [
            'value' => 31,
            'region' => 'Tanga',
        ],
        [
            'value' => 32,
            'region' => 'Ilala',
        ],
        [
            'value' => 33,
            'region' => 'Kinondoni',
        ],
        [
            'value' => 34,
            'region' => 'Temeke',
        ],
        [
            'value' => 35,
            'region' => 'Wma-ports-unit',
        ],

    ];

    return $regions;
}



function regionsAndDistricts($colSize ){
    return view('Components/RegionsSelection',['colSize'=> $colSize]);
}