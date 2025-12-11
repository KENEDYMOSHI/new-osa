<?php 

function importCsv($file)
{
   
    if ($file->isValid() && $file->getExtension() == 'csv') {
        $csv = array_map('str_getcsv', file($file->getPathname()));
        $keys = array_shift($csv);
        $data = array();
        foreach ($csv as $i => $row) {
            $data[$i] = array_combine($keys, $row);
        }

        $csvData = array_map(fn ($item) => [
            'date' => $item['STATEMENT DATE'],
            'amount' => (float)str_replace(',', '', $item['CREDIT AMOUNT']),
            'transactionReference' => $item['TRANSACTION REFFERENCE NUMBER'],
            'controlNumber' => controlNumber($item['NAME / DESCRIPTION']),
        ], $data);
        return $csvData;
    } else {
        return ['msg' => 'Invalid file format'];
    }
}


function controlNumber($billString)
{
    // $billString = 'TMS GEPG BIL:994191180244 REC:923032157281899 NESHAL INVESTM REF:FH244201675228364';
    $pattern = '/BIL:(\d{12})/'; // The regular expression pattern
    preg_match($pattern, $billString, $matches); // Search for the pattern in the string

    if (count($matches) > 1) {
        $result = $matches[1]; // The 12 digits after "BIL:" are captured in group 1
        return $result; // Output: 994191180244
    } else {
        return '';
    }
}

?>