<?php

namespace App\Controllers;

use App\Models\Testing;
use App\Libraries\ArrayLibrary;
use App\Models\Test;
use NumberFormatter;

class Yap extends BaseController
{
    public $testingModel;
    public $arrayLibrary;
    public function __construct()
    {
        $this->testingModel = new Testing();
        $this->arrayLibrary = new ArrayLibrary();
        helper(['image', 'form', 'text', 'url']);
    }

    public function str()
    {
        $name = '    t456 dnx';

        echo $name . '<br><br>';
        echo strtoupper(preg_replace('/\s+/', '', $name));

        //  echo str_replace(' ','',$name);
    }
    public function timeDifference($t1, $t2)
    {
        $t1To24 = date("H:i", strtotime($t1));
        $t2To24 = date("H:i", strtotime($t2));
        $time1 = strtotime($t1To24);
        $time2 = strtotime($t2To24);
        $difference = (($time1 - $time2) / 60) / 60;

        return abs((int) $difference);
    }
    public function dateDifference($d2, $d1)
    {
        $MULTI = 365 * 60 * 60 * 24;
        $MULTI_2 = 30 * 60 * 60 * 24;
        $MULTI_3 = 60 * 60 * 24;
        $date1 = strtotime($d2);
        $date2 = strtotime($d1);
        $difference = abs($date2 - $date1);
        $years = floor($difference / ($MULTI));
        $months = floor(($years * $MULTI) / ($MULTI_2));

        $days = floor(($difference - $years * $MULTI - $months * $MULTI) / ($MULTI_3));

        $hours = floor(($days * $MULTI_3) / (60 * 60));
        return $hours;
    }

    public function toWords($number)
    {
        $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        return ucwords($f->format($number)) . ' Tanzanian Shillings Only';
    }

    public function text()
    {

        $x = 'compartment';
        // $f = new NumberFormatter("sw", NumberFormatter::SPELLOUT);
        // echo ucwords($f->format(3444957)) . ' Tanzanian Shillings';

        echo increment_string($x);




    }

    public function pdf()
    {

      return view('welcome');


    }

    public function arr()
    {



        $array1 = [
            'customers.region' => 'moshi',
            'transactions.unique_id' => 'wr466rwett1t4wet',
            'MONTH(created_on) BETWEEN ' => 3,
            'YEAR(created_on) = ' => '2020',
            'created_on >=' => null
        ];
        $myData =  array_filter($array1, function ($var) {
            return $var != '';
        });

        echo "<pre>";
        print_r($myData);
        echo "</pre>";
        // echo "Even:\n";
        // print_r(array_filter($array2, "even"));
    }

    public function splat()
    {

        function addNumbers(...$numbers)
        {
            // $sum = 0;
            // foreach ($numbers as $number) {
            //   $sum += $number;
            // }
            return $numbers;
        }
        //echo 'total = '. addNumbers(1,2,7,5);

        print_r(addNumbers(1, 2, 7, 5));
    }

    public function gen()
    {
        if ((4 + 2) == 3) {
            yield 'ooh yeah';
        } else {
            yield 'ooh Noooo';
        }

        yield $data = (function () {
            echo 'HELLO WORLD';
        })();

        $a = 10;
        $b = 25;
        $c = $a + $b;
        yield 'num' => $c;
    }

    public function yld()
    {
        foreach ($this->gen() as $item) {
            echo $item . '<br>';
        }
    }

    public function datax()
    {
        $role = 2;
        // $data = $this->testingModel->byRole($role);
        echo '<pre>';
        // print_r($data);
        echo '</pre>';
    }






    public function mail()
    {

        // helper(['image','emailTemplate']);
        return view('email');
    }



    public function alpine()
    {
        return view('alpine');
    }
    public function alpineContact()
    {
        $data = [
            'name' => $this->request->getVar('name'),
            'email' => $this->request->getVar('email'),
            'message' => $this->request->getVar('message'),
        ];
        // $this->testingModel->saveData($data);
        if ($this->testingModel->saveData($data)) {
        }
        echo json_encode([
            'res' => 'Done'
        ]);
    }

    public function arrData()
    {
        $x = [
            ['id' => '10', 'product' => 'Nokia'],
            ['id' => '15', 'product' => 'Apple'],
            ['id' => '20', 'product' => 'Vivo'],
            ['id' => '22', 'product' => 'Samsung'],
        ];


        function removeElementWithValue($array, $key, $values)
        {
            foreach ($values as $v) {

                // echo $v;
                foreach ($array as $subKey => $subArray) {


                    if ($subArray[$key] == $v) {
                        unset($array[$subKey]);
                    }
                }
            }

            return $array;
        }
        $set = ['10', '15'];


        echo '<pre>';
        print_r(removeElementWithValue($x, "id", $set));
        echo '</pre>';
        echo '<pre>';
        //   print_r($x);
        echo '</pre>';
        echo '<br>';
        echo '<br>';
        echo '<br>';

        // echo '<pre>';
        // print_r($array);
        // echo '</pre>';
    }


    public function index()
    {
        $cars = array("Volvo", "BMW", "Toyota", "Honda", "Mercedes", "Opel", "Nissan");

        $first = array_slice($cars, 0, count($cars) / 2);
        $second = array_slice($cars, count($cars) / 2);
        echo '<pre>';
        print_r($first);
        echo '</pre>';
        echo '</br>';
        echo '</br>';
        echo '</br>';
        echo '<pre>';
        print_r($second);
        echo '</pre>';
        echo '</br>';
        echo '</br>';
        echo '</br>';
    }

    public function groupBy()
    {

        $data = new Testing();
        $bill = $data->getAll();

        echo json_encode($bill);
        exit;

        // $newDataset = array_map(fn ($data) => [
        //     'hash' => $data->hash,
        //     'product_id' => $data->instrument_id,
        //     'phoneNumber' => $data->phone_number,
        //     'controlNumber' => $data->control_number,
        //     'payment' => $data->payment,
        //     'amount' => $data->amount,
        //     'date' => $data->created_on,
        //     'item' => $data->item,
        //     'total' => number_format($data->total),
        //     'totalInWords' => $this->toWords($data->total),
        // ], $bill);
        // return $this->response->setJSON([
        //     'count' => count($newDataset),
        //     'data' => $newDataset,
        // ]);
    }
    public function searchBy()
    {

        $data = new Testing();
        $bill = $data->searchBy();

        $newDataset = array_map(fn ($data) => [
            'id' => $data->id,
            'hash' => $data->hash,
            'name' => $data->name,
            'phoneNumber' => $data->phone_number,
            'controlNumber' => $data->control_number,
            'payment' => $data->payment,
            'amount' => $data->amount,
            'date' => $data->created_on,
            'item' => $data->item,
            'total' => $data->total,
            'totalInWords' => $this->toWords($data->total),
        ], $bill);
        return $this->response->setJSON([
            'count' => count($newDataset),
            'data' => $newDataset,
        ]);
    }
    public function bill()
    {

        $data = new Testing();
        $bill = $data->bill();

        $newDataset = array_map(fn ($data) => [
            'id' => $data->id,
            'hash' => $data->hash,
            'phoneNumber' => $data->phone_number,
            'controlNumber' => $data->control_number,
            'payment' => $data->payment,
            'amount' => $data->amount,
            'date' => $data->created_on,
            'item' => $data->item,
            'total' => $data->total,
            'totalInWords' => $this->toWords($data->total),
        ], $bill);
        return $this->response->setJSON([
            'count' => count($newDataset),
            'data' => $newDataset,
        ]);
    }
}





/* End of file Yap.php */
/* Location: ./app/controllers/Yap.php */