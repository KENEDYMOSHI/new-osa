<?php
//sums u all numbers within an array
function reducer($data)
{
  if (!empty($data)) {
    return  number_format(array_reduce($data, fn ($x, $y) => $x + $y));
  } else {
    return 0;
  }
}
function reportTemplateAllActivities($collection)
{
  $activities = [
    (object)[
      'title' => 'Vehicle Tank Verification',
      'tag' => 'vtv'
    ],
    (object)[
      'title' => 'Sandy And Ballast Lorries',
      'tag' => 'sbl',

    ],
    (object)[
      'title' => 'Water Meters',
      'tag' => 'waterMeter',

    ],
    (object)[
      'title' => 'Pre Package',
      'tag' => 'prepackage',

    ],
    (object)[
      'title' => 'Others',
      'tag' => 'all',

    ],
  ];

  $reportData = array_map(function ($data) use ($collection) {
    $rawData = array_filter($collection, fn ($activity) => $activity->Activity == $data->tag);

    $dataArray = [
      'activity' => $data->title,
      'paid' => reducer(array_map(fn ($paid) => $paid->amount, array_filter($rawData, fn ($status) => $status->PaymentStatus == 'Paid'))),
      'pending' => reducer(array_map(fn ($pending) => (float)$pending->amount, array_filter($rawData, fn ($status) => $status->PaymentStatus == 'Pending'))),
      'partial' => reducer(array_map(fn ($partial) => (float)$partial->amount, array_filter($rawData, fn ($status) => $status->PaymentStatus == 'Partial'))),
      'total' => reducer(array_map(fn ($total) => (float)$total->amount, $rawData)),
      'instruments' => count($rawData)

    ];

    return $dataArray;
  }, $activities);

// return  $reportData ;
// exit;


  $tr = '';
  foreach ($reportData as $report) {
    $activity = $report['activity'];
    $paid = $report['paid'];
    $pending = $report['pending'];
    $partial = $report['partial'];
    $total = $report['total'];
    $instruments = $report['instruments'];

    $tr .= <<<"HTML"
    <tr>
        <td>$activity</td>
        <td>$paid</td>
        <td>$pending</td>
        <td>$partial</td>
        <td>$total</td>
        <td>$instruments</td>
    </tr>
   HTML;
  }
  $html = <<<"HTML"

     <thead class="thead-dark" id="4464">
       <th>Activity name</th>
       <th>Paid Amount</th>
       <th>Pending Amount</th>
       <th>Partial Amount</th>
       <th>Total Amount</th>
       <th>Number Of Instruments(Items)</th>
     </thead>
                <tbody>
                  $tr
                </tbody>

HTML;

  $paid = number_format(array_reduce(array_map(fn ($data) => $data->amount, array_filter($collection, fn ($data) => $data->PaymentStatus == 'Paid')), fn ($x, $y) => $x + $y));

  $pending = number_format(array_reduce(array_map(fn ($data) => $data->amount, array_filter($collection, fn ($data) => $data->PaymentStatus == 'Pending')), fn ($x, $y) => $x + $y));

  $partial = number_format(array_reduce(array_map(fn ($data) => $data->amount, array_filter($collection, fn ($data) => $data->PaymentStatus == 'Partial')), fn ($x, $y) => $x + $y));

  $total = number_format(array_reduce(array_map(fn ($data) => $data->amount, $collection), fn ($x, $y) => $x + $y));



  $summary = <<<"HTML"
    <div class="row">
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                    <h5 class="txt-center"><b>Collection Summary</b></h5>
                     <table class="table table-sm">
                     <tr class="paidAmount">
                     <td><b>Paid Amount</b></td>
                     <td>Tsh $paid</td>
                     </tr>
                     <tr class="pendingAmount">
                     <td><b>Pending Amount</b></td>
                     <td>Tsh $pending</td>
                     </tr>
                     <tr class="pendingAmount">
                     <td><b>Partial Amount</b></td>
                     <td>Tsh $partial</td>
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
  ];
}



function reportTemplate($collection)
{

  $tr = '';
  foreach ($collection as $report) {
    $ul = '';
    $i = 1;
    $items = $report->billItems;

    foreach ($items as $item) {
      $sn  = $i++;
      $ul .= <<<"HTML"
            <li>- $item->ItemName</li>
          HTML;
    }
    $tr .= <<<"HTML"
    <tr>
        <td>$report->PyrName</td>
        <td>$report->PayCntrNum</td>
        <td>$report->amount</td>
        <td>$report->PaymentStatus</td>
        <td>
            <ul style="padding:0;margin:0;list-style:none">
              $ul
                
            </ul>
        </td>
        <td>$report->CreatedAt</td>
    </tr>
   HTML;
  }
  $html = <<<"HTML"

                <thead class="thead-dark">
                    <th>Payer Name</th>
                    <th>Control Number</th>
                    <th>Amount</th>
                    <th>Payment Status</th>
                    <th>BillItems</th>
                    <th>Date</th>
                </thead>
                <tbody>
               $tr
                   

                </tbody>

          
  HTML;

  $paid = number_format(array_reduce(array_map(fn ($data) => $data->amount, array_filter($collection, fn ($data) => $data->PaymentStatus == 'Paid')), fn ($x, $y) => $x + $y));

  $pending = number_format(array_reduce(array_map(fn ($data) => $data->amount, array_filter($collection, fn ($data) => $data->PaymentStatus == 'Pending')), fn ($x, $y) => $x + $y));

  $partial = number_format(array_reduce(array_map(fn ($data) => $data->amount, array_filter($collection, fn ($data) => $data->PaymentStatus == 'Partial')), fn ($x, $y) => $x + $y));

  $total = number_format(array_reduce(array_map(fn ($data) => $data->amount, $collection), fn ($x, $y) => $x + $y));



  $summary = <<<"HTML"
    <div class="row">
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                    <h5 class="txt-center"><b>Collection Summary</b></h5>
                     <table class="table table-sm">
                     <tr class="paidAmount">
                     <td><b>Paid Amount</b></td>
                     <td>Tsh $paid</td>
                     </tr>
                     <tr class="pendingAmount">
                     <td><b>Pending Amount</b></td>
                     <td>Tsh $pending</td>
                     </tr>
                     <tr class="pendingAmount">
                     <td><b>Partial Amount</b></td>
                     <td>Tsh $partial</td>
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
  ];
}
