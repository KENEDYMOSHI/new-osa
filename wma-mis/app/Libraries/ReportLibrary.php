<?php

namespace App\Libraries;

class ReportLibrary
{




    //sums u all numbers within an array
    public function reducer($data)
    {
        if (!empty($data)) {
            return  number_format(array_reduce($data, fn ($x, $y) => $x + $y));
        } else {
            return 0;
        }
    }

    // public function getItemCount($gfsCode)
    // {
    //     $data = $this->billM;
    //     $instruments = (new ArrayLibrary($data))->map(fn ($instrument) => $instrument->ItemQuantity)->reduce(fn ($x, $y) => $x + $y)->get();
    // }

    function reportTemplateAllActivities($collection)
    {
        // $activities = [
        //     (object)[
        //         'title' => 'Vehicle Tank Verification',
        //         'tag' => setting('Gfs.vtv')
        //     ],

        // ];
        $codes = (array)gfsCodes();

        $activities =   array_map(fn ($value, $key) => (object)['title' => $value, 'code' => $key], $codes, array_keys($codes));

        $reportData = array_map(function ($data) use ($collection) {

            $rawData = array_filter($collection, fn ($activity) => $activity->GfsCode == $data->code);
            // $itemData = array_filter($rawData['billItems'], fn ($activity) => $activity->code == $data->code);


            $itm = [];

            // $items = array_values(array_map(fn ($data) => $data->billItems, $rawData));
            //PyrName,PayCntrNum,PaidAmount,BillAmt

            $code = $data->code;

            // $filteredItems = (new ArrayLibrary($rawData))
            // ->filter(fn ($item) => $item->Activity == $code)
            // ->map(fn ($item) => $item->ItemQuantity ?? 1)
            // ->reduce(fn($x,$y)=>$x + $y)
            // ->get();

            //  $paidAmount = reducer(array_map(fn ($paid) => $paid->amount, array_filter($rawData, fn ($status) => $status->PaymentStatus == 'Paid')));
            $paid = (new ArrayLibrary($rawData))
                ->filter(fn ($data) => $data->PaymentStatus == 'Paid')
                ->map(fn ($data) => $data->amount)
                ->reduce(fn ($x, $y) => $x + $y)->get();



            $partial = (new ArrayLibrary($rawData))
                ->filter(fn ($data) => $data->PaymentStatus == 'Partial')
                ->map(fn ($data) =>  $data->amount)
                ->reduce(fn ($x, $y) => $x + $y)->get();

            $pending = (new ArrayLibrary($rawData))
                ->filter(fn ($data) => $data->PaymentStatus == 'Pending')
                ->map(fn ($data) =>  $data->amount)
                ->reduce(fn ($x, $y) => $x + $y)->get();




            // $partialPending = reducer(array_map(fn ($bill) => $bill->BilledAmount - $bill->BilledAmount, array_filter($rawData, fn ($status) => $status->PaymentStatus == 'Partial')));

            $dataArray = [
                'activity' => $data->title,
                'paid' => number_format($paid) ?? 0,
                'pending' => number_format($pending) ?? 0,
                'partial' => number_format($partial) ?? 0,
                'total' => number_format($paid +  $pending + $partial),
                // 'xxx' =>  $items,
                //  'ITM' =>  $filteredItems,
                // 'instruments' => ($filteredItems),


            ];

            return $dataArray;
        }, $activities);

        //341,4577,664.3999996


        $tr = '';
        $sn = 1;
        foreach ($reportData as $report) {
            $index = $sn++;
            $activity = $report['activity'];
            $paid = $report['paid'];
            $pending = $report['pending'];
            $partial = $report['partial'];
            $total = $report['total'];
            //  $instruments = $report['instruments'];

            $tr .= <<<"HTML"
            <tr>
                <td>$index</td>
                <td>$activity</td>
                <td>$paid</td>
                <td>$pending</td>
                <td>$partial</td>
                <td>$total</td>
                <!-- <td></td> -->
            </tr>
   HTML;
        }
        $html = <<<"HTML"

     <thead class="thead-dark" id="4464">
      <tr>
        <th>#</th>
        <th>Activity name</th>
       <th>Paid Amount</th>
       <th>Pending Amount</th>
       <th>Partial Amount</th>
       <th>Total Amount</th>
       <!-- <th>Number Of Instruments(Items)</th> -->
      </tr>
     </thead>
                <tbody>
                  $tr
                </tbody>

HTML;
        $paid = (new ArrayLibrary($collection))
            ->filter(fn ($data) => $data->PaymentStatus === 'Paid')
            ->map(fn ($data) => $data->amount)
            ->reduce(fn ($x, $y) => $x + $y)
            ->get();





        //  $paid = $paidAmount + $partialPaid;

        $partial = (new ArrayLibrary($collection))
            ->filter(fn ($data) => $data->PaymentStatus == 'Partial')
            ->map(fn ($data) => $data->amount)
            ->reduce(fn ($x, $y) => $x + $y)
            ->get();

        $pending = (new ArrayLibrary($collection))
            ->filter(fn ($data) => $data->PaymentStatus == 'Pending')
            ->map(fn ($data) =>  $data->amount)
            ->reduce(fn ($x, $y) => $x + $y)
            ->get();


        $total = number_format($paid + $partial   + $pending);

        $paidAmount = number_format($paid);
        $pendingAmount = number_format($pending);
        $partialAmount = number_format($partial);


        $summary = <<<"HTML"
    <div class="row">
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                    <h5 class="txt-center"><b>Collection Summary</b></h5>
                     <table class="table table-sm table table-bordered">
                     <tr class="paidAmount">
                     <td><b>Paid Amount</b></td>
                     <td>Tsh $paidAmount</td>
                     </tr>
                     <tr class="pendingAmount">
                     <td><b>Pending Amount</b></td>
                     <td>Tsh $pendingAmount</td>
                     </tr>
                     <tr class="pendingAmount">
                     <td><b>Partial Amount</b></td>
                     <td>Tsh $partialAmount</td>
                     </tr>
                     <tr class="totalAmount">
                     <td><b>Total Amount</b></td>
                     <td>Tsh $total</td>
                     </tr>
                     </table>
                    </div>
                    </div>
  HTML;

        return (object)[
            'report' => $html,
            'summary' => $summary,
            'itm' => $reportData,
        ];
    }



    public function reportTemplate($collection)
    {


        $data =
            $tr = '';
        foreach ($collection as $report) {
            $ul = '';
            $i = 1;

            $date = dateFormatter($report->date);
            $amount = number_format($report->amount);
            // $amount = number_format((new ArrayLibrary($items))->map(fn ($item) => $item['amount'])->reduce(fn ($x, $y) => $x + $y)->get());
            // $amount = json_encode( $items);

            $phoneNumber = str_replace('255', '0', $report->PyrCellNum);


            $tr .= <<<"HTML"
    <tr>
        <td>$report->customer </td>
        <td>$phoneNumber</td>
        <td>$report->controlNumber</td> 
        <td>$amount</td>
        <td>$report->PaymentStatus</td>
        <td>$report->ItemName</td>
        
        <td>$date</td>
    </tr>
   HTML;
        }
        $html = <<<"HTML"
                <thead class="thead-dark">
               <tr>
                     <th>Payer Name</th>
                     <th>Mobile Number</th>
                    <th>Control Number</th>
                    <th>Amount</th>
                    <th>Payment Status</th>
                    <th>BillItems</th>
                    <th>Date</th>
               </tr>
                </thead>
                <tbody>
                   $tr
                </tbody>

          
  HTML;

        $paid = (new ArrayLibrary($collection))
            ->filter(fn ($data) => $data->PaymentStatus === 'Paid')
            ->map(fn ($data) => $data->amount)
            ->reduce(fn ($x, $y) => $x + $y)
            ->get();



        // $partialPaid = (new ArrayLibrary($collection))
        //     ->filter(fn ($data) => $data->PaymentStatus == 'Partial')
        //     ->map(fn ($data) => $data->PaidAmount)
        //     ->reduce(fn ($x, $y) => $x + $y)
        //     ->get();

        //  $paid = $paidAmount + $partialPaid;

        $partial = (new ArrayLibrary($collection))
            ->filter(fn ($data) => $data->PaymentStatus == 'Partial')
            ->map(fn ($data) => $data->amount)
            ->reduce(fn ($x, $y) => $x + $y)
            ->get();

        $pending = (new ArrayLibrary($collection))
            ->filter(fn ($data) => $data->PaymentStatus == 'Pending')
            ->map(fn ($data) =>  $data->amount)
            ->reduce(fn ($x, $y) => $x + $y)
            ->get();

        $total = number_format($paid + $partial  + $pending);

        $paidAmount = number_format($paid);
        $pendingAmount = number_format($pending);
        $partialAmount = number_format($partial);




        $summary = <<<"HTML"
    <div class="row">
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                    <h5 class="txt-center"><b>Collection Summary</b></h5>
                     <table class="table table-bordered table-sm">
                     <tr class="paidAmount">
                     <td><b>Paid Amount</b></td>
                     <td>Tsh $paidAmount</td>
                     </tr>
                     <tr class="pendingAmount">
                     <td><b>Pending Amount</b></td>
                     <td>Tsh $pendingAmount</td>
                     </tr>
                     <tr class="pendingAmount">
                     <td><b>Partial Amount</b></td>
                     <td>Tsh $partialAmount</td>
                     </tr>
                     <tr class="totalAmount">
                     <td><b>Total Amount</b></td>
                     <td>Tsh $total</td>
                     </tr>
                     </table>
                    </div>
                    </div>
  HTML;

        return (object)[
            'report' => (string)$html,
            'summary' => (string)$summary,
            'itm' => '',

        ];
    }



    public function sortPartials($payments)
    {
        $partials = (new ArrayLibrary($payments))->filter(fn ($item) => $item->BillPayOpt == 2)->get();
        $consolidated = array_reduce($partials, function ($carry, $item) {
            $key = $item->controlNumber . '|' . $item->BillPayOpt;
            if ($item->BillPayOpt == 2) {
                if (!isset($carry[$key])) {
                    $carry[$key] = clone $item;
                    $carry[$key]->amount = $item->PaidAmt;
                } else {
                    $carry[$key]->PaidAmt += $item->PaidAmt;
                    $carry[$key]->amount += $item->PaidAmt;
                }
            } else {
                $carry[] = $item;
            }
            return $carry;
        }, []);
        $consolidated = array_values($consolidated);
        return $consolidated;
    }






    public function partialsPaid($collection)
    {
        $partials = (new ArrayLibrary($collection))->filter(fn($item) => $item->BillPayOpt == 2)->get();
        $groupedItems = [];
        $uniqueBillItemRefs = [];
        $paidAmountsPerControl = [];
        $uniquePayRefIds = [];
    
        foreach ($partials as $item) {
            if (!isset($paidAmountsPerControl[$item->controlNumber])) {
                $paidAmountsPerControl[$item->controlNumber] = [];
                $uniquePayRefIds[$item->controlNumber] = [];
            }
    
            // Only add the PaidAmt if the PayRefId is unique for this controlNumber
            if (!in_array($item->PayRefId, $uniquePayRefIds[$item->controlNumber])) {
                $paidAmountsPerControl[$item->controlNumber][] = $item->PaidAmt;
                $uniquePayRefIds[$item->controlNumber][] = $item->PayRefId;
            }
    
            if (!isset($uniqueBillItemRefs[$item->controlNumber])) {
                $uniqueBillItemRefs[$item->controlNumber] = [];
            }
    
            if (!in_array($item->BillItemRef, $uniqueBillItemRefs[$item->controlNumber])) {
                $uniqueBillItemRefs[$item->controlNumber][] = $item->BillItemRef;
                $groupedItems[$item->controlNumber][] = $item;
            }
        }
    
        // Update amount for each group
        foreach ($groupedItems as $controlNumber => $items) {
            $itemCount = count($items);
            $totalPaidAmt = array_sum($paidAmountsPerControl[$controlNumber]);
         //   $paidAmounts = $paidAmountsPerControl[$controlNumber];
    
            // Divide the total PaidAmt by the item count
            $amountPerItem = $totalPaidAmt / $itemCount;
    
            // Update the amount and status for each item and add c_Number key
            foreach ($items as $item) {
                if ($item->clearedAmount == $item->BillAmt) {
                    $item->amount = $item->amount;
                    $item->status = 'Completed';
                } else {
                    $item->amount = $amountPerItem;
                    $item->status = 'NOT Completed';
                }
              //  $item->paidAmountsInCn = $paidAmounts;
            }
        }
    
        return array_merge(...array_values($groupedItems));
    }
    



    public function partialsUnpaid($collection)
    {
        $pendingPartials = (new ArrayLibrary($collection))->filter(fn ($item) => $item->BillPayOpt == 2 && $item->PaymentStatus == 'Partial')->get();
        $groupedItems = [];
        foreach ($pendingPartials as $item) {
            $groupedItems[$item->controlNumber][] = $item;
        }

        // Update amount for each group
        foreach ($groupedItems as $controlNumber => $items) {
            $itemCount = count($items);
            $remainingAmount = $items[0]->BillAmt - $items[0]->PaidAmount;
            $amountPerItem = $remainingAmount / $itemCount;

            foreach ($items as $item) {
                $item->amount = $amountPerItem;
            }
        }



        return array_merge(...array_values($groupedItems));
    }




















    public function processPaidItems($params)
    {
        $db = \Config\Database::connect();


        // $params = [
        //     'MONTH(bill_payment.TrxDtTm)' => '2',
        //     'YEAR(bill_payment.TrxDtTm)' => '2024',
        //    // 'bill_payment.CenterNumber' => '0003',
        //     // 'GfsCode' => '142101210037'
        //     //'controlNumber' => '994191449132',
        // ];

        $collection = $db->table('bill_payment')->select('PyrName as customer,controlNumber,CenterNumber as region,GfsCode,ItemName,PaidAmt,clearedAmount,BillAmt,BillItemAmt  as amount,TrxDtTm,BillPayOpt,"Paid" as PaymentStatus')
            ->where($params)
            // ->limit(20)
            ->join('bill_items', 'bill_payment.PayCtrNum = bill_items.controlNumber', 'inner')
            ->get()
            ->getResult();


        $full = (new ArrayLibrary($collection))->filter(fn ($item) => $item->BillPayOpt == 3)->get();
        $partials = $this->sortPartials($collection);

        $allPayments = array_merge($partials, $full);
        return $allPayments;
    }
}
