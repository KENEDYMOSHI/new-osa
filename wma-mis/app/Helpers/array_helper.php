<?php
function showOption($arrayOne, $arrayTwo)
{
    $results = array_diff($arrayTwo, $arrayOne);

    return $results;
}

//this function remover duplicates from array of object
function removeDuplicatesByKey($array, $key)
{
    $result = array_reduce($array, function ($carry, $item) use ($key) {
        if (is_array($item)) {
            $uniqueKey = $item[$key];
        } elseif (is_object($item)) {
            $uniqueKey = $item->{$key};
        } else {
            throw new InvalidArgumentException('Elements of the array must be objects or arrays.');
        }

        if (!isset($carry[$uniqueKey])) {
            $carry[$uniqueKey] = $item;
        }

        return $carry;
    }, []);

    return array_values($result);
}

$data = [
    'name' => [ 'Jane', 'John', 'Alice'],
    'age' => [25, 30, 25],
    'city' => ['New York', 'Los Angeles', 'Chicago'],
];

function multiDimensionArray(array $array): array
{
    $newArray = [];

    // Find the maximum count of sub arrays
    $maxCount = 0;
    foreach ($array as $value) {
        if ($value !== null && is_array($value)) {
            $maxCount = max($maxCount, count($value));
        }
    }

    foreach ($array as $key => $value) {
        // Check if the value is [null] or not an array
        if ($value === null || !is_array($value)) {
            continue;
        }

        // Pad the subarray with [null] if its count is less than the maximum count
        $value = array_pad($value, $maxCount, null);

        for ($i = 0; $i < count($value); $i++) {
            $newArray[$i][$key] = $value[$i];
        }
    }

    // Filter out keys with values of [null]
    $newArray = array_filter($newArray, function ($item) {
        return $item !== [null];
    });

    return array_values($newArray);
}


// function multiDimensionArray(array $array): array
// {
//     $newArray = [];
//     foreach ($array as $key => $value) {
//         // Check if the value is [null], and skip the key if true
//         if ($value === [null]) {
//             continue;
//         }
//         for ($i = 0; $i < count($value); $i++) {
//             $newArray[$i][$key] = $value[$i];
//         }
//     }
//     // Filter out keys with values of [null]
//     $newArray = array_filter($newArray, function($item) {
//         return $item !== [null];
//     });
//     return array_values($newArray);
// }


// function multiDimensionArray(array $array): array
// {
//     $newArray = [];
//     foreach ($array as $key => $value) {
//         for ($i = 0; $i < count($value); $i++) {
//             $newArray[$i][$key] = $value[$i];
//         }
//     }
//     return array_values($newArray);
// }

function fillArray($count, $variable): array
{
    return array_fill(0, $count, $variable);
}

function breakArray($array)
{
    foreach ($array as $item) {
        return $item;
    }
}

//removes all empty values from the array
function filterParams(array $array): array
{
    return array_filter($array, function ($value) {
        return $value !== "";
    });
}

//this function removes an item from array by using the key specified
function arrayExcept(array $array, array $keys): array
{
    $keys = array_flip($keys); // Flip keys for faster lookup

    foreach ($array as &$subArray) {
        if (is_array($subArray)) {
            $subArray = array_diff_key($subArray, $keys);
        }
    }

    return $array;
}
function exceptKeys(array $array, array $keysToRemove): array
{
    foreach ($keysToRemove as $key) {
        if (array_key_exists($key, $array)) {
            unset($array[$key]);
        }
    }

    return $array;
}
function totalAmount($theArray)
{

    $total = 0;

    foreach ($theArray as $item) {
        $amount = $item->amount;
        $total += $amount;
    }
    if ($total) {
        return number_format($total);
        // return $total;
    } else {
        return '0';
    }
}

function paidAmount($theArray)
{

    if(!empty($theArray)){
        $paid = 0;

        foreach ($theArray as $item) {
            $amount = $item->amount;
            if ($item->PaymentStatus == 'Paid') {
                $paid += $amount;
            }
        }
        if ($paid) {
            return number_format($paid);
        } else {
            return '0';
        }
    }else{
        return 0;
    }
}
function pendingAmount($theArray)
{

    if(!empty($theArray)){
        $pending = 0;

        foreach ($theArray as $item) {
    
            $amount = $item->amount;
            if ($item->PaymentStatus == 'Pending') {
                $pending += $amount;
            }
        }
        if ($pending) {
            return number_format($pending);
        } else {
            return '0';
        }
    }else{
        return 0;
    }
}

function instrumentQuantity($theArray)
{
    return count($theArray);
}
//=================helper to check all paid instruments====================
function paidInstruments($theArray)
{
    if(!empty($theArray)){
        $instruments = [];
    foreach ($theArray as $item) {
        if ($item->PaymentStatus == 'Paid') {
            array_push($instruments, $item);
        }
    }

    return number_format(count($instruments));
    }else{
        return 0;
    }
}
//=================helper to check all Unpaid instruments====================
function pendingInstruments($theArray)
{
    $instruments = [];
    foreach ($theArray as $item) {
        if ($item->PaymentStatus == 'Pending') {
            array_push($instruments, $item);
        }
    }

    return number_format(count($instruments));
}

function paidSum($value)
{
    return str_replace(',', '', paidAmount($value));
}
function pendingSum($value)
{
    return str_replace(',', '', pendingAmount($value));
}
function totalCollection($value)
{
    return str_replace(',', '', totalAmount($value));
}

function meterQuantityAll($array)
{
    $totalMeters = 0;
    foreach ($array as $item) {
        $totalMeters += $item->quantity;
    }
    return $totalMeters;
}

function meterQuantityPaid($array)
{
    $paidMeters = 0;
    foreach ($array as $item) {
        if ($item->PaymentStatus == 'Paid') {
            $paidMeters += $item->quantity;
        }
    }
    return $paidMeters;
}
function meterQuantityPending($array)
{
    $pendingMeters = 0;
    foreach ($array as $item) {
        if ($item->PaymentStatus == 'Pending') {
            $pendingMeters += $item->quantity;
        }
    }
    return $pendingMeters;
}
function stringToInteger($str)
{
    return str_replace(',', '', $str);
}

function renderContacts($contacts)
{

    return ' Phone No: ' . $contacts->phone_number . ', Tel: ' . $contacts->tele_number . ', Fax: ' . $contacts->fax . ' , P.O Box ' . $contacts->postal_address . ', e-mail: ' . $contacts->email;
}


function countries(): array
{
    return [
        'Afghan',
        'Albanian',
        'Algerian',
        'American',
        'Andorran',
        'Angolan',
        'Antiguans',
        'Argentinean',
        'Armenian',
        'Australian',
        'Austrian',
        'Azerbaijani',
        'Bahamian',
        'Bahraini',
        'Bangladeshi',
        'Barbadian',
        'Barbudans',
        'Batswana',
        'Belarusian',
        'Belgian',
        'Belizean',
        'Beninese',
        'Bhutanese',
        'Bolivian',
        'Bosnian',
        'Brazilian',
        'British',
        'Bruneian',
        'Bulgarian',
        'Burkinabe',
        'Burmese',
        'Burundian',
        'Cambodian',
        'Cameroonian',
        'Canadian',
        'Cape Verdean',
        'Central African',
        'Chadian',
        'Chilean',
        'Chinese',
        'Colombian',
        'Comoran',
        'Congolese',
        'Costa Rican',
        'Croatian',
        'Cuban',
        'Cypriot',
        'Czech',
        'Danish',
        'Djibouti',
        'Dominican',
        'Dutch',
        'East Timorese',
        'Ecuadorean',
        'Egyptian',
        'Emirian',
        'Equatorial Guinean',
        'Eritrean',
        'Estonian',
        'Ethiopian',
        'Fijian',
        'Filipino',
        'Finnish',
        'French',
        'Gabonese',
        'Gambian',
        'Georgian',
        'German',
        'Ghanaian',
        'Greek',
        'Grenadian',
        'Guatemalan',
        'Guinea-Bissauan',
        'Guinean',
        'Guyanese',
        'Haitian',
        'Herzegovinian',
        'Honduran',
        'Hungarian',
        'I-Kiribati',
        'Icelander',
        'Indian',
        'Indonesian',
        'Iranian',
        'Iraqi',
        'Irish',
        'Israeli',
        'Italian',
        'Ivorian',
        'Jamaican',
        'Japanese',
        'Jordanian',
        'Kazakhstani',
        'Kenyan',
        'Kittian and Nevisian',
        'Kuwaiti',
        'Kyrgyz',
        'Laotian',
        'Latvian',
        'Lebanese',
        'Liberian',
        'Libyan',
        'Liechtensteiner',
        'Lithuanian',
        'Luxembourger',
        'Macedonian',
        'Malagasy',
        'Malawian',
        'Malaysian',
        'Maldivan',
        'Malian',
        'Maltese',
        'Marshallese',
        'Mauritanian',
        'Mauritian',
        'Mexican',
        'Micronesian',
        'Moldovan',
        'Monacan',
        'Mongolian',
        'Moroccan',
        'Mosotho',
        'Motswana',
        'Mozambican',
        'Namibian',
        'Nauruan',
        'Nepalese',
        'New Zealander',
        'Nicaraguan',
        'Nigerian',
        'Nigerien',
        'North Korean',
        'Northern Irish',
        'Norwegian',
        'Omani',
        'Pakistani',
        'Palauan',
        'Panamanian',
        'Papua New Guinean',
        'Paraguayan',
        'Peruvian',
        'Polish',
        'Portuguese',
        'Qatari',
        'Romanian',
        'Russian',
        'Rwandan',
        'Saint Lucian',
        'Salvadoran',
        'Samoan',
        'San Marinese',
        'Sao Tomean',
        'Saudi',
        'Scottish',
        'Senegalese',
        'Serbian',
        'Seychellois',
        'Sierra Leonean',
        'Singaporean',
        'Slovakian',
        'Slovenian',
        'Solomon Islander',
        'Somali',
        'South African',
        'South Korean',
        'Spanish',
        'Sri Lankan',
        'Sudanese',
        'Surinamer',
        'Swazi',
        'Swedish',
        'Swiss',
        'Syrian',
        'Taiwanese',
        'Tajik',
        'Tanzanian',
        'Thai',
        'Togolese',
        'Tongan',
        'Trinidadian/Tobagonian',
        'Tunisian',
        'Turkish',
        'Tuvaluan',
        'Ugandan',
        'Ukrainian',
        'Uruguayan',
        'Uzbekistani',
        'Venezuelan',
        'Vietnamese',
        'Welsh',
        'Yemenite',
        'Zambian',
        'Zimbabwean'
    ];
}




// function getSum($arr){
//     sum
// }