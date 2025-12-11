<?php

// use NumberFormatter;

function  trimmed(string $str): string
{
	return str_replace(' ', '', $str);
}

function toWords($number): string
{
	$f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
	return ucwords($f->format($number)) . ' Tanzanian Shillings Only';
}

function numberToWords($num = '')
{
	$num    = (string) ((int) $num);

	if ((int) ($num) && ctype_digit($num)) {
		$words  = array();

		$num    = str_replace(array(',', ' '), '', trim($num));

		$list1  = array(
			'', 'one', 'two', 'three', 'four', 'five', 'six', 'seven',
			'eight', 'nine', 'ten', 'eleven', 'twelve', 'thirteen', 'fourteen',
			'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'
		);

		$list2  = array(
			'', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty',
			'seventy', 'eighty', 'ninety', 'hundred'
		);

		$list3  = array(
			'', 'thousand', 'million', 'billion', 'trillion',
			'quadrillion', 'quintillion', 'sextillion', 'septillion',
			'octillion', 'nonillion', 'decillion', 'undecillion',
			'duodecillion', 'tredecillion', 'quattuordecillion',
			'quindecillion', 'sexdecillion', 'septendecillion',
			'octodecillion', 'novemdecillion', 'vigintillion'
		);

		$num_length = strlen($num);
		$levels = (int) (($num_length + 2) / 3);
		$max_length = $levels * 3;
		$num    = substr('00' . $num, -$max_length);
		$num_levels = str_split($num, 3);

		foreach ($num_levels as $num_part) {
			$levels--;
			$hundreds   = (int) ($num_part / 100);
			$hundreds   = ($hundreds ? ' ' . $list1[$hundreds] . ' Hundred' . ($hundreds == 1 ? '' : 's') . ' ' : '');
			$tens       = (int) ($num_part % 100);
			$singles    = '';

			if ($tens < 20) {
				$tens = ($tens ? ' ' . $list1[$tens] . ' ' : '');
			} else {
				$tens = (int) ($tens / 10);
				$tens = ' ' . $list2[$tens] . ' ';
				$singles = (int) ($num_part % 10);
				$singles = ' ' . $list1[$singles] . ' ';
			}
			$words[] = $hundreds . $tens . $singles . (($levels && (int) ($num_part)) ? ' ' . $list3[$levels] . ' ' : '');
		}
		$commas = count($words);
		if ($commas > 1) {
			$commas = $commas - 1;
		}

		$words  = implode(', ', $words);

		$words  = trim(str_replace(' ,', ',', ucwords($words)), ', ');
		if ($commas) {
			$words  = str_replace(',', ' and', $words);
		}

		return $words;
	} else if (!((int) $num)) {
		return 'Zero';
	}
	return '';
}


function numString(int $size): string
{
    $result = '';
    for ($i = 0; $i < $size; $i++) {
        $result .= mt_rand(0, 9);
    }
    return $result;
}


//do not change the default value here
function randomString(int $size = 32): string
{
    return substr(hash('sha256', bin2hex(random_bytes($size))), 0, $size);
}

//vtv_1,0000,0000
function vehicleId($id)
{  
	$trim = strpos($id, '_', strpos($id, '_') + 1);
	return substr($id, 0, $trim);
}


function printer($array)
{
	echo '<pre>';
	print_r($array);
	echo '</pre>';
}


function financialYear()
{
	// date_default_timezone_set('Africa/Dar es Salaam');
	$currentMonth = date('m');

	$startYear = date('Y');


	if ($currentMonth >= 7) {
		$initialDate = new DateTime("$startYear-07-01");
		$finalDate = new DateTime("$startYear-06-30");
		$finalDate->add(new DateInterval('P1Y')); // Add 1 year
	} else {
		$initialDate = new DateTime("$startYear-07-01");
		$initialDate->sub(new DateInterval('P1Y')); // Subtract 1 year
		$finalDate = new DateTime("$startYear-06-30");
	}

	$initialDate = $initialDate->format('Y-m-d');
	$finalDate = $finalDate->format('Y-m-d');

	return (object)[
		'startDate' => $initialDate,
		'endDate' => $finalDate,
	];
}


function formatNumber($number) {
    return rtrim(rtrim(number_format($number, 6, '.', ','), '0'), '.');
}




