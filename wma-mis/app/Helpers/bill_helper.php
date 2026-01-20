<?php

use LSS\Array2XML;
use App\Models\ProfileModel;

function amountPercentage($amount, $percent)
{
    return ($amount * $percent) / 100;
}

function normalBill($billData): string
{

    $bill = $billData->bill;

    $billAmount = number_format($bill->BillAmt);
    $xpDate = dateFormatter($bill->BillExprDt);
    $printedOn = dateFormatter(date('Y-m-d'));
    $spCode = setting('Bill.spCode');


    $interval = (new DateTime())->diff(new DateTime($bill->BillExprDt))->days;

    $billItems = $billData->billItems;




    $totalBillAmount = $bill->BillAmt;

    $xprDate = ($bill->BillExprDt && $bill->extendedExpiryDate != null)
        ? dateFormatter(date('Y-m-d', strtotime("+$interval days")))
        : dateFormatter($bill->BillExprDt);

    $BillTyp = $bill->BillTyp;
    $count = count($billItems);
    if ($BillTyp == 2 && $bill->isTrBill == 'Yes') {
        $fifteenPercent = number_format(amountPercentage($totalBillAmount, 15));
        $si = $count + 1;
        $trItem = <<<HTML
            <tr style="display:flex">
            <td style="width:5%">$si</td>
            <td style="width:70%">Treasury Registrar  15 % </td>
            <td style="width:30%">Tsh <b>$fifteenPercent</b></td>
            </tr>    
            HTML;
    } else {
        $trItem = '';
    }





    $items = '';
    $sn = 0;
    foreach ($billItems as $billItem) {
        $amount = $BillTyp == 2 ? number_format(amountPercentage($billItem->BillItemAmt, 85)) : number_format($billItem->BillItemAmt);
        $fob = $billItem->fob;
        $tansardNumber = $billItem->tansardNumber;
        $tansardDate = date('d-m-Y', strtotime($billItem->date));
        $ppg = $billItem->GfsCode == setting('Gfs.prePackages') && $fob != '' && $tansardNumber != '' ? "F.O.B : $fob   Tansad Number : $tansardNumber Tansard Date: $tansardDate" : '';
        $sn++;
        $items .= <<<"HTML"
          <tr style="display:flex">
            <td style="width:5%">$sn</td>
            <td style="width:70%">$billItem->ItemName $ppg</td>
            <td style="width:30%">Tsh <b>$amount</b></td>
        </tr>
      

     HTML;
    }

    return <<<"HTML"
     <div class="row">
                        <div class="col-8">

                            <table class="table table-sm table-borderless" id="billCustomer">
                                   <tr style="display:flex">
                                    <td>Control Number:</td>
                                    <td><b>$bill->PayCntrNum</b></td>
                                </tr>
                                   <tr style="display:flex">
                                    <td>Service Provider Code:</td>
                                    <td><b>$spCode</b></td>
                                </tr>

                                   <tr style="display:flex">
                                    <td>Payer:</td>
                                    <td><b>$bill->PyrName</b></td>
                                </tr>
                                   <tr style="display:flex">
                                    <td>Payer Phone:</td>
                                    <td>+$bill->PyrCellNum</td>
                                </tr>
                                   <tr style="display:flex">
                                    <td>Bill Description:</td>
                                    <td>$bill->BillDesc</td>
                                </tr>
                            </table>
                        </div>
                      
                        <div class="col-4">
                            <div class="qr">
                                <div id="canvas">
                                   
                                </div>
                                <!-- <p class="text-small text-center" style="font-size: 11px; margin-top: -1px;margin-left:-76px;">
                                    Scan And Pay
                                </p> -->
                            </div>

                        </div>

                    </div>

                    <div class="row">
                        <div class="col-8">
                            <table class="table table-sm">
                                <thead>
                                       <tr style="display:flex">
                                        <th>#</th>
                                        <th style="width: 70%;">Billed Item</th>
                                        <!-- <th>Details</th> -->
                                        <th style="width: 30%;">Amount</th>

                                    </tr>
                                </thead>
                                <tbody id="billItems">
                                $items
                                $trItem
                                </tbody>
                            </table>




                            <br>
                            <table class="table table-sm table-borderless">
                                   <tr style="display:flex">
                                    <td style="width:50%;"><b>Total Billed Amount:</b></b></td>
                                    <!-- <td></td> -->
                                    <td style="width:50%"><b id="billTotal">  $billAmount (TZS)</b></td>
                                </tr>

                                   <tr style="display:flex">
                                    <td style="width:50%;">Amount In Words:</b></td>
                                    <!-- <td></td> -->
                                    <td style="width:50%"><span id="billTotalInWords">$bill->BillAmtWords.</span></td>
                                </tr>
                                   <tr style="display:flex">
                                    <td style="width:50%;"><b>Expires On:</b></b></td>
                                    <!-- <td></td> -->
                                    <td style="width:50%"><b id="expire">$xprDate</td>
                                </tr>
                                   <tr style="display:flex">
                                    <td style="width:50%;">Prepared By:</b></td>
                                    <!-- <td></td> -->
                                    <td style="width:50%"><span id="preparedBy">$bill->BillGenBy</span></td>
                                </tr>
                                   <tr style="display:flex">
                                    <td style="width:50%;">Collection Center:</b></td>
                                    <!-- <td></td> -->
                                    <td style="width:50%"><span id="">$bill->centerName</span></td>
                                </tr>
                                   <tr style="display:flex">
                                    <td style="width:50%;">Printed By:</b></td>
                                    <!-- <td></td> -->
                                    <td style="width:50%"><b><span id="printedBy">$billData->printedBy</span></b></td>
                                </tr>
                                   <tr style="display:flex">
                                    <td style="width:50%;">Printed On:</b></td>
                                    <!-- <td></td> -->
                                    <td style="width:50%"><span id="printedOn">$printedOn</span></td>
                                </tr>
                                   <tr style="display:flex">
                                    <td style="width:50%;">Signature</b></td>
                                    <!-- <td></td> -->
                                    <td style="width:50%"><b>.........................</b></td>
                                </tr>

                            </table>
                        </div>
                    </div>
                    <hr>

                    <div class="row">

                        <div class="col-6">
                            <h6><b>How to pay</b></h6>

                            <ul>
                                <li>1 Via bank: visit any branch or bank agent of CRDB,NMB, reference number: <b class="ref">$bill->PayCntrNum</b> </li>
                               
                                <li>2 Via Mobile Network Operators (MNO) :</li>
                                <!-- <li> (Kupitia mitandao ya simu -->
                                <ul>
                                    <li>Enter respective USSD menu of MNO</li>
                                    <!-- <li>Ingia kwenye Mtandao Husika</li> -->
                                    <li>Select 4 (Make Payments)</li>
                                    <!-- <li>Chagua 4 (Lipa bili)</li> -->
                                    <li>Select 5 (Government payments enter <b class="ref">$bill->PayCntrNum</b> as reference number) </li>
                                    <!-- <li>Chagua 5 (malipo ya serikali ingiza <b class="ref"></b> kama namba ya kumbukumbu) </li> -->


                                </ul>
                                </li>

                            </ul>
                            </li>

                        </div>
                        <div class="col-6">
                            <h6><b>Jinsi ya kulipa</b></h6>

                            <ul>
                                <!-- <li>1 Via bank visit any branch CRDB,NMB, reference number: <b class="ref"></b> </li> -->
                                <li>( Kupitia Benki: Fika tawi lolote au wakala wa benki ya CRDB,NMB, Namba ya kumbukumbu : <b class="ref">$bill->PayCntrNum</b>)</li>
                                <!-- <li>2 via Mobile network operators (MNO)</li> -->
                                <li> (Kupitia mitandao ya simu
                                    <ul>
                                        <!-- <li>Enter respective USSD menu of MNO</li> -->
                                        <li>Ingia kwenye menu ya mtandao husika</li>
                                        <!-- <li>Select 4 (Make Payments)</li> -->
                                        <li>Chagua 4 (Lipa bili)</li>
                                        <!-- <li>Select 5 (Government payments enter <b class="ref"></b> as reference number) </li> -->
                                        <li>Chagua 5 (malipo ya serikali ingiza <b class="ref">$bill->PayCntrNum</b> kama namba ya kumbukumbu) </li>


                                    </ul>

                            </ul>
                            </li>

                        </div>
                    </div>
   HTML;
}




function receipt($receiptData): string
{
    $user = auth()->user();

    $receipt = $receiptData->receipt;

    $paidAmount = number_format($receipt->PaidAmt);
    $billedAmount = ($receipt->BillAmt);
    $amtInWords = toWords($receipt->PaidAmt);
    $outstanding = number_format($receipt->BillAmt - $receipt->clearedAmount);
    $paymentDate = dateFormatter($receipt->TrxDtTm);
    $issuedOn = dateFormatter($receipt->BillGenDt);
    $phoneNumber = str_replace('255', '0', $receipt->PyrCellNum);
    $billType = $receipt->BillTyp;





    $receiptItems = $receiptData->billItems;
    // $grossAmt = array_sum(array_map(fn($item) => $item->SingleItemAmount, $receiptItems));

    $totalBillAmount = number_format($receipt->BillAmt);
    $items = '';
    $sn = 0;
    foreach ($receiptItems as $receiptItem) {
        // $amount = number_format(amountPercentage($receiptItem->BillItemAmt,85));
        $amount = $billType == 2 ? number_format(amountPercentage($receiptItem->BillItemAmt, 85)) : number_format($receiptItem->BillItemAmt);
        $fob = $receiptItem->fob;
        $tansardNumber = $receiptItem->tansardNumber;
        $tansardDate = date('d-m-Y', strtotime($receiptItem->date));
        $ppg = $receiptItem->GfsCode == setting('Gfs.prePackages') && $fob != '' && $tansardNumber != '' ? "F.O.B : $fob   Tansad Number : $tansardNumber Tansard Date: $tansardDate" : '';
        $sn++;
        $items .= <<<"HTML"
          <tr style="display:flex">
            <td style="width:5%">$sn</td>
            <td style="width:70%">$receiptItem->ItemName  $ppg</td>
            <td style="width:30%">Tsh <b>$amount</b></td>
        </tr>
     HTML;
    }
    $BillTyp = $receipt->BillTyp;
    $count = count($receiptItems);
    if ($BillTyp == 2) {
        $fifteenPercent = number_format(amountPercentage($billedAmount, 15));
        $si = $count + 1;
        $trItem = <<<HTML
        <tr style="display:flex">
        <td style="width:5%">$si</td>
        <td style="width:70%">Treasury Registrar  15 % </td>
        <td style="width:30%">Tsh <b>$fifteenPercent</b></td>
        </tr>    
        HTML;
    } else {
        $trItem = '';
    }


    return <<<"HTML"
     <div class="row">
                        <div class="col-8">

                            <table class="table table-sm table-borderless" id="">
                                   <tr style="display:flex">
                                    <td>Receipt Number:</td>
                                    <td><b>$receipt->PayRefId</b></td>
                               

                                   <tr style="display:flex">
                                    <td>Received From:</td>
                                    <td><b>$receipt->PyrName</b></td>
                                   </tr>

                                   <tr style="display:flex">
                                    <td>Phone Number:</td>
                                    <td><b>$phoneNumber</b></td>
                                   </tr>
                                   <tr style="display:flex">
                                    <td>Amount :</td>
                                    <td>$paidAmount</td>
                                </tr>
                                </tr>
                                   <tr style="display:flex">
                                    <td>Amount In Words :</td>
                                    <td>$amtInWords</td>
                                </tr>
                                   <tr style="display:flex">
                                    <td>Outstanding Balance</td>
                                    <td>$outstanding</td>
                                </tr>
                                </tr>
                                   <tr style="display:flex">
                                    <td>Bill Description:</td>
                                    <td>$receipt->BillDesc</td>
                                </tr>
                            </table>
                        </div>
                      
                      

                    </div>
                    <p>In Respect Of</p>
                    <div class="row">
                        <div class="col-8">
                            <table class="table table-sm">
                                <thead>
                                       <tr style="display:flex">
                                        <th>#</th>
                                        <th style="width: 70%;">Billed Item</th>
                                        <!-- <th>Details</th> -->
                                        <th style="width: 30%;">Amount</th>

                                    </tr>
                                </thead>
                                <tbody id="billItems">
                                $items
                                $trItem
                               <tr>
                              
                              
                               </tr>
                                </tbody>
                               
                            </table>




                            <br>
                            <table class="table table-sm table-borderless">
                                   <tr style="display:flex">
                                    <td style="width:50%;"><b>Total Billed Amount:</b></b></td>
                                    <!-- <td></td> -->
                                    <td style="width:50%"><b id="billTotal"> $totalBillAmount (TZS)</b></td>
                                </tr>

                                 
                                   <tr style="display:flex">
                                    <td style="width:50%;">Bill Reference</b></td>
                                    <!-- <td></td> -->
                                    <td style="width:50%"><span id="preparedBy">$receipt->TrxId</span></td>
                                </tr>
                                   <tr style="display:flex">
                                    <td style="width:50%;">Payment Control Number</b></td>
                                    <!-- <td></td> -->
                                    <td style="width:50%"><b><span id="printedBy">$receipt->PayCntrNum</span></b></td>
                                </tr>
                                   <tr style="display:flex">
                                    <td style="width:50%;">Payment Date</b></td>
                                    <!-- <td></td> -->
                                    <td style="width:50%"><span id="printedOn">$paymentDate</span></td>
                                </tr>
                                </tr>
                                   <tr style="display:flex">
                                    <td style="width:50%;">Issued By</b></td>
                                    <!-- <td></td> -->
                                    <td style="width:50%"><span id="printedOn">$receipt->BillGenBy</span></td>
                                </tr>
                                </tr>
                                   <tr style="display:flex">
                                    <td style="width:50%;">Printed By</b></td>
                                    <!-- <td></td> -->
                                    <td style="width:50%"><span id="printedOn">$user->username</span></td>
                                </tr>
                                </tr>
                                   <tr style="display:flex">
                                    <td style="width:50%;">Date Issued</b></td>
                                    <!-- <td></td> -->
                                    <td style="width:50%"><span id="printedOn">$issuedOn</span></td>
                                </tr>
                                   <tr style="display:flex">
                                    <td style="width:50%;">Signature</b></td>
                                    <!-- <td></td> -->
                                    <td style="width:50%"><b>.........................</b></td>
                                </tr>

                            </table>
                        </div>
                    </div>
                    <hr>

                    
   HTML;
}



function transferBill($billData)
{

    $bill = $billData->bill;

    $billAmount = number_format($bill->BillAmt);
    $xpDate = dateFormatter($bill->BillExprDt);
    $printedOn = dateFormatter(date('Y-m-d'));


    $BillTyp = $bill->BillTyp;


    $billItems = $billData->billItems;

    $grossAmt = array_sum(array_map(fn($item) => $item->SingleItemAmount, $billItems));

    $totalBillAmount = number_format($grossAmt);

    $items = '';
    $sn = 0;
    foreach ($billItems as $billItem) {
        //$amount = number_format($billItem->BillItemAmt);
        $amount = $BillTyp == 2 ? number_format(amountPercentage($billItem->BillItemAmt, 85)) : number_format($billItem->BillItemAmt);
        $fob = $billItem->fob;
        $tansardNumber = $billItem->tansardNumber;
        $tansardDate = date('d-m-Y', strtotime($billItem->date));
        $ppg = $billItem->GfsCode == setting('Gfs.prePackages') && $fob != '' && $tansardNumber != '' ? "F.O.B : $fob   Tansad Number : $tansardNumber Tansard Date: $tansardDate" : '';
        $sn++;
        $items .= <<<"HTML"
        <tr style="display:flex">
            <td style="width:5%">$sn</td>
            <td style="width:70%">$billItem->ItemName $ppg </td>
            <td style="width:30%">Tsh <b>$amount</b></td>
        </tr>
     HTML;
    }

    $count = count($billItems);
    if ($BillTyp == 2) {
        $fifteenPercent = number_format(amountPercentage($bill->BillAmt, 15));
        $si = $count + 1;
        $trItem = <<<HTML
        <tr style="display:flex">
        <td style="width:5%">$si</td>
        <td style="width:70%">Treasury Registrar  15 % </td>
        <td style="width:30%">Tsh <b>$fifteenPercent</b></td>
        </tr>    
        HTML;
    } else {
        $trItem = '';
    }

    return <<<"HTML"
     
                    <h6><b>(a). Remitter / Tax Payer Details</b></h6>
                    <table border="0" style="width: 100%;">
                      <tr style="display:flex">
                            <td style="width: 30%;">
                                <h6>Name Of Account Holder(s)<h6>
                            </td>
                            
                            <td style="width: 60%;">
                                <h6>:............................................................................................................................................................<h6>
                            </td>
                        </tr>
                     
                      <tr style="display:flex">
                            <td style="width: 30%;">
                                <h6>Name Of Commercial Bank<h6>
                            </td>
                            
                            <td style="width: 60%;">
                                <h6>:............................................................................................................................................................<h6>
                            </td>
                        </tr>
                      <tr style="display:flex">
                            <td style="width: 30%;">
                                <h6>Bank Account Number<h6>
                            </td>
                            
                            <td style="width: 60%;">
                                <h6>:............................................................................................................................................................<h6>
                            </td>
                        </tr>
                      <tr style="display:flex">
                            <td style="width: 30%;">
                                <h6>Signatories<h6>
                            </td>
                            
                            <td style="width: 60%;">
                                <table border="0" style="width: 100%;">
                                    <tr>
                                        <td style="width: 50%;">
                                            :.................................................................. <br>
                                            Signature of transfer one
                                        </td>
                                        <td>I<br>
                                          .
                                    </td>
                                        <td style="width: 50%;">
                                            ...................................................................<br>
                                            Signature of transfer two
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <br>
                   
                    
                    <table border="0" style="width:100%">
                        <tr style="display:flex">
                            <td style="width: 30%;">

                                <h6><b>(b). Beneficiary Details:</b></h6>

                            </td>
                        
                            <td style="width: 60%;">
                                <h6>
                                    :<b>COMMISSIONER FOR WEIGHTS AND MEASURES AGENCY</b>
                                    <h6>
                            </td>
                        </tr>

                        <tr style="display:flex">
                            <td style="width: 30%;">
                        
                            <td style="width: 60%;">
                                <h6><b>:$billData->bank</b></h6>
                            </td>
                        </tr>
                        <tr style="display:flex">
                            <td style="width: 30%;">
                                <h6>Account Number</h6>
                            </td>
                        
                            <td style="width: 60%;">
                                $billData->accountNumber</td>
                        </tr>
                        <tr style="display:flex">
                            <td style="width: 30%;">
                                <h6>SWIFT Code</h6>
                            </td>
                        
                            <td style="width: 60%;">
                                $bill->SwiftCode</td>
                        </tr>
                        <tr style="display:flex">
                            <td style="width: 30%;">
                                <h6>Control Number</h6>
                            </td>
                        
                            <td style="width: 60%;">
                                <b>$bill->PayCntrNum</b></td>
                        </tr>
                        <tr style="display:flex">
                            <td style="width: 30%;">
                                <h6>Payer Name</h6>
                            </td>
                        
                            <td style="width: 60%;">
                                $bill->PyrName</td>
                        </tr>
                        <tr style="display:flex">
                            <td style="width: 30%;">
                                <h6>Beneficiary Account(Filed 59 Of MT103)</h6>
                            </td>
                        
                            <td style="width: 60%;">
                                $billData->accountNumber</td>
                        </tr>
                        <tr style="display:flex">
                            <td style="width: 30%;">
                                <h6>Payment Reference(Filed 70 Of MT103)</h6>
                            </td>
                        
                            <td style="width: 60%;">
                                <h6><b>:ROC/$bill->PayCntrNum</b></h6>
                            </td>
                        </tr>
                        <tr style="display:flex">
                              <td style="width: 30%;">
                                <h6>Transfer Amount</h6>
                            </td>
                        
                            <td style="width: 60%;"><b>$billAmount (TZS)</b></td>
                        </tr>
                        <tr style="display:flex">
                              <td style="width: 30%;">
                                <h6>Amounts In Words</h6>
                            </td>
                        
                            <td style="width: 60%;">
                                $bill->BillAmtWords</td>
                        </tr>
                        <tr style="display:flex">
                              <td style="width: 30%;">
                                <h6>Being Paid For</h6>
                            </td>
                        
                            <td style="width: 60%;">
                                $bill->BillDesc</td>
                        </tr>
                    </table>

                    <div class="co-md-3">
                        <div class="col-4">
                            <div class="qr" style="position:absolute; top: -300px;left:620px">
                                <div id="canvas">
                                   
                                </div>
                                <!-- <p class="text-small text-center" style="font-size: 11px; margin-top: -1px;margin-left:-20px;">
                                    Scan And Pay 
                                </p> -->
                            </div>

                        </div>
                    </div>






                    <div class="row">
                        <div class="col-8">
                            <table class="table table-sm">
                                <thead>
                                       <tr style="display:flex">
                                        <th>#</th>
                                        <th style="width: 70%;">Billed Item</th>
                                        <th style="width: 30%;">Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="billItems">
                                    $items
                                    $trItem
                                </tbody>
                            </table>



                        </div>

                    </div>

                    <table border="0" style="width:100%">
                       <tr style="display:flex">
                              <td style="width: 30%;">
                                <h6>Expires On</h6>
                            </td>
                        
                            <td style="width: 60%;">
                                $xpDate</td>
                        </tr>
                       <tr style="display:flex">
                              <td style="width: 30%;">
                                <h6>Prepared By</h6>
                            </td>
                        
                            <td style="width: 60%;">
                                $bill->BillGenBy</td>
                        </tr>
                       <tr style="display:flex">
                              <td style="width: 30%;">
                                <h6>Collection Center</h6>
                            </td>
                        
                            <td style="width: 60%;">
                                $bill->centerName</td>
                        </tr>
                       <tr style="display:flex">
                              <td style="width: 30%;">
                                <h6>Printed By</h6>
                            </td>
                        
                            <td style="width: 60%;">
                               $billData->printedBy</td>
                        </tr>
                       <tr style="display:flex">
                              <td style="width: 30%;">
                                <h6>Printed On</h6>
                            </td>
                        
                            <td style="width: 60%;">
                               $printedOn</td>
                        </tr>
                       <tr style="display:flex">
                              <td style="width: 30%;">
                                <h6>Signature</h6>
                            </td>
                        
                            <td style="width: 60%;">
                               .........................</td>
                        </tr>
                    </table>

                    


                


                    <hr>

                    <div class="row">

                        <div class="col-md-12">
                            <h6><b>Note to commercial bank:</b></h6>
                            <p style="margin:0;padding:0;line-height:1.6">1. Please capture the above information correctly.Do not change or add any text symbol or digit on information provided</p>
                            <p style="margin:0;padding:0;line-height:1.6">2. Field 59 of MT103 is an <b>"Account number"</b> with value <b>/$billData->accountNumber</b> must be captured correctly</p>
                            <p style="margin:0;padding:0;line-height:1.6">3. Field 70 of MT103 is a <b>"Control number"</b> with value <b>/ROC/$bill->PayCntrNum</b> must be captured correctly</p>

                        </div>
                    </div>
                   
    HTML;
}




function tnxCode($code): string
{
    switch ($code) {
        case '7101':
            return 'Request is Successfully';
            break;
        case '7201':
            return 'Failure';
            break;
        case '7202':
            return 'Required header is not given';
            break;
        case '7203':
            return 'Unauthorized';
            break;
        case '7204':
            return 'Bill does not exist';
            break;
        case '7205':
            return 'Invalid service provider';
            break;
        case '7206':
            return 'Service provider is not active';
            break;
        case '7207':
            return 'Duplicate payment';
            break;
        case '7208':
            return 'Invalid business account';
            break;

        case '7209':
            return 'Business account is not active';
            break;
        case '7210':
            return 'Collection account balance limit reached';
            break;
        case '7211':
            return 'Payment service provider code did not match Bill service provider code';
            break;
        case '7212':
            return 'Payment currency did not match Bill currency';
            break;
        case '7213':
            return 'Bill has expired';
            break;
        case '7214':
            return 'Insufficient amount paid';
            break;
        case '7215':
            return 'Invalid payment service provider';
            break;
        case '7216':
            return 'Payment service provider is not active';
            break;
        case '7217':
            return 'No payer email or phone number';
            break;
        case '7218':
            return 'Wrong payer identity';
            break;
        case '7219':
            return 'Wrong currency';
            break;
        case '7220':
            return 'Sub Service provider is inactive';
            break;
        case '7221':
            return 'Wrong bill equivalent amount';
            break;
        case '7222':
            return 'Wrong bill miscellaneous amount';
            break;
        case '7223':
            return 'Invalid or inactive gfs or service type';
            break;
        case '7224':
            return 'Wrong bill amount';
            break;
        case '7225':
            return 'Invalid bill reference number or code';
            break;
        case '7226':
            return 'Duplicate bill information';
            break;
        case '7227':
            return 'Blank bill identification number';
            break;
        case '7228':
            return 'Invalid sub service provider code';
            break;
        case '7229':
            return 'Wrong bill item gfs or payment type code';
            break;
        case '7230':
            return 'Wrong bill generation date';
            break;
        case '7231':
            return 'Wrong bill expiry date';
            break;
        case '7232':
            return 'Consumer already started by another process';
            break;
        case '7233':
            return 'Consumer already stopped by another process';
            break;
        case '7234':
            return 'Wrong bill payment option';
            break;
        case '7235':
            return 'Bill creation completed successfully';
            break;
        case '7236':
            return 'Bill creation completed with errors';
            break;
        case '7237':
            return 'Bill detail creation completed successfully';
            break;
        case '7238':
            return 'Bill detail creation completed with errors';
            break;
        case '7239':
            return 'No external bill system settings found';
            break;
        case '7240':
            return 'Failed to save transaction';
            break;
        case '7241':
            return 'Invalid session';
            break;
        case '7242':
            return 'Invalid request data';
            break;
        case '7243':
            return 'Invalid credit account';
            break;
        case '7244':
            return 'Invalid transfer amount';
            break;
        case '7245':
            return 'Invalid credit account name';
            break;
        case '7246':
            return 'Invalid debit account';
            break;
        case '7247':
            return 'Invalid transfer transaction description';
            break;
        case '7248':
            return 'Invalid debtor bic';
            break;
        case '7249':
            return 'Wrong transfer date';
            break;
        case '7250':
            return 'Invalid value in transfer reserved field one';
            break;
        case '7251':
            return 'Invalid transfer transaction number';
            break;
        case '7252':
            return 'Transfer transaction created successfully';
            break;
        case '7253':
            return 'Transfer transaction created with errors';
            break;
        case '7254':
            return 'Invalid use payment reference, use "Y" or "N"';
            break;
        case '7255':
            return 'Invalid item billed amount';
            break;
        case '7256':
            return 'Invalid item equivalent amount';
            break;
        case '7257':
            return 'Invalid item miscellaneous amount';
            break;
        case '7258':
            return 'Total item billed amount mismatches the bill amount';
            break;
        case '7259':
            return 'Total item equivalent amount mismatches the bill equivalent amount';
            break;
        case '7260':
            return 'Total item miscellaneous amount mismatches the bill miscellaneous amount';
            break;
        case '7261':
            return 'Defect bill saved successfully';
            break;
        case '7262':
            return 'Defect bill saved with errors';
            break;
        case '7263':
            return 'Defect bill items saved successfully';
            break;
        case '7264':
            return 'Defect bill items saved with errors';
            break;
        case '7265':
            return 'Bill items saved successfully';
            break;
        case '7266':
            return 'Bill items saved with errors';
            break;
        case '7267':
            return 'Invalid email address ';
            break;
        case '7268':
            return 'Invalid phone number';
            break;
        case '7269':
            return 'Invalid or inactive Service Provider System Id';
            break;
        case '7270':
            return 'Transfer transaction update completed successfully';
            break;
        case '7271':
            return 'Transfer transaction update completed with errors';
            break;
        case '7272':
            return 'Defect transfer transaction saved successfully';
            break;
        case '7273':
            return 'Defect transfer transaction saved with errors';
            break;
        case '7274':
            return 'Duplicate transfer transaction';
            break;
        case '7275':
            return 'Invalid Service Provider Payer Id';
            break;
        case '7276':
            return 'Invalid Service Provider Payer Name';
            break;
        case '7277':
            return 'Invalid bill description';
            break;
        case '7278':
            return 'Invalid bill approval user';
            break;
        case '7279':
            return 'Bill already settled';
            break;
        case '7280':
            return 'Bill expired and bill move process failed';
            break;
        case '7281':
            return 'Invalid payment transaction date';
            break;
        case '7282':
            return 'Invalid payer email or phone number';
            break;
        case '7283':
            return 'Bill has been cancelled';
            break;
        case '7284':
            return 'Payment currency did not match collection account currency';
            break;
        case '7285':
            return 'Invalid bill generation user';
            break;
        case '7286':
            return 'Bill cancellation process failed';
            break;
        case '7287':
            return 'Bill reference number does not meet required bill control number specifications';
            break;
        case '7288':
            return 'Disbursement request did not match signature';
            break;
        case '7289':
            return 'Invalid batch generated date';
            break;
        case '7290':
            return 'Total batch amount cannot be zero(0)';
            break;
        case '7291':
            return 'Total batch amount is not equal to summation of items(transactions)';
            break;
        case '7292':
            return 'Duplicate disbursement batch';
            break;
        case '7293':
            return 'Invalid disbursement pay option';
            break;
        case '7294':
            return 'Invalid disbursement batch scheduled date';
            break;
        case '7295':
            return 'Invalid disbursement notification template';
            break;
        case '7296':
            return 'Disbursement notification template is not active';
            break;
        case '7297':
            return 'Inactive currency';
            break;
        case '7298':
            return 'Invalid currency for disbursement';
            break;
        case '7299':
            return 'Batch item(recipients) recipients should not exceed';
            break;
        case '7301':
            return 'Bill has been paid partially';
            break;
        case '7302':
            return 'Paid amount is not exact billed amount';
            break;
        case '7303':
            return 'Invalid Signature';
            break;
        case '7304':
            return 'Invalid Signature Configuration missing one of parameter (passphrase,key alias,filename)';
            break;
        case '7305':
            return 'Invalid batch start and end date';
            break;
        case '7306':
            return 'Batch has no item(transaction)';
            break;
        case '7307':
            return 'lbl.message.0107 = Inconsistency batch start, end and generated date ';
            break;
        case '7308':
            return 'Invalid value in transfer reserved field two';
            break;
        case '7309':
            return 'Invalid value in transfer reserved field three ';
            break;
        case '7310':
            return 'Invalid transfer credit or debit account ';
            break;
        case '7311':
            return 'Invalid GePG configurations missing one of parameter (gepgKeyFilePath,gepgPassphrase,gepgAlias)';
            break;
        case '7312':
            return 'Batch does not exist';
            break;
        case '7313':
            return 'Cancel is only for auto pay batch';
            break;
        case '
                                                                        7314':
            return 'Batch already on disbursement process, cancellation process failed';
            break;
        case '7315':
            return 'Batch cancellation process failed';
            break;
        case '7316':
            return 'Batch already canceled';
            break;
        case '7317':
            return 'Error on processing request';
            break;
        case '7318':
            return 'Invalid reconciliation request date';
            break;
        case '7319':
            return 'Reconciliation request date is out of allowable range';
            break;
        case '7320':
            return 'Invalid reconciliation request options';
            break;

            break;
        case ' 7321':
            return 'Request can not completed at this time, try later';
            break;
        case '7322':
            return 'Inactive communication protocol';
            break;
        case '7323':
            return 'Invalid code, mismatch of supplied code on information and header';
            break;
        case '7324':
            return 'No payment(s) found for specified bill control number';
            break;
        case '7325':
            return 'Request to partner application composed';
            break;
        case '7326':
            return 'Request sent to partner application(system)';
            break;
        case '7327':
            return 'Request sent to partner application(system) with no content response';
            break;
        case '7328':
            return 'Request not received successful with partner application(system)';
            break;
        case '7329':
            return 'Processing or communication error on partner application(system)';
            break;
        case '7330':
            return 'Inactive or Unavailable, bill push to pay for specified Payment service Provider';
            break;
        case '7331':
            return 'Paid Online Waiting Bank Confirmation';
            break;
        case '7332':
            return 'Too many request of the same content';
            break;
        case '7333':
            return 'Invalid bill control number';
            break;
        case '7334':
            return 'Bill has been updated';
            break;
        case '7335':
            return 'Requested bill control number does not exist';
            break;
        case '7336':
            return 'Requested bill amount has reached the maximum payable limit';
            break;
        case '7376':
            return 'Invalid Bill Reuse Reason';
            break;


        default:
            return '-';
            break;
    }
}

$code = [
    [
        'activity' => 'vtv',
        'code' => '142101210024',
        'title' => 'Vehicle Tank Verification (VTV)'
    ],
    [
        'activity' => 'sbl',
        'code' => '142101210025',
        'title' => 'Sandy & Ballast lorry (SBL)'
    ],
    [
        'activity' => 'waterMeter',
        'code' => '142101210026',
        'title' => 'Meter'
    ],

];
$x = ['142101210003', '142101210013', '142101210035', '142101210007'];
function gfsCod()
{
    $codes = [
        '142101210003' => 'Vehicle Tank Verification (VTV)',
        '142101210004' => 'Wb - Weighbridge',
        '142101210005' => 'FST – Fixed Storage Tank',
        '142101210006' => 'Bulk Storage Tank (BST)',
        '142101210007' => 'Pre-packages',
        '142101210008' => 'WGT – Wagon Tank',
        '142101210009' => 'Fuel pump',
        '142101210010' => 'CNG filling Station',
        '142101210011' => 'F/M – Flow Meter',
        '142101210012' => 'Ch/p - check pump',
        '142101210013' => 'Meter',
        '142101210014' => 'Metrological Supervision (On Board & Shore Tanks)',
        '142101210015' => 'Pressure gauges',
        '142101210016' => 'Proving Tank',
        '142101210017' => 'Taximeter',
        '142101210018' => 'MR - Metre Rule',
        '142101210019' => 'TM - Tape Measure',
        // '142101210020' => 'M. LE - Measures of Length',
        '142101210021' => 'BRIM - Brim Measure system',
        '142101210022' => 'S/y – Steelyard',
        '142101210023' => 'SDw -Suspended Digital Ware',
        '142101210024' => 'C/S - Counter scale',
        '142101210025' => 'P/s - Platform scale',
        '142101210026' => 'S/B - Spring Balance',
        '142101210027' => 'Bal - Balance',
        '142101210028' => 'Kor - Koroboi',
        '142101210029' => 'Vib – Vibaba',
        '142101210030' => 'Pis – Pishi',
        '142101210031' => 'Ax/w - Weigher',
        '142101210032' => 'Au/W - Automatic Weigher',
        '142101210033' => 'B/S - Beam Scale',
        '142101210034' => 'S/y – Steelyard',
        '142101210035' => 'Sandy & Ballast lorry (SBL)',
        '142101210036' => 'E/m- Electricity meter',
        '142101210037' => 'OMI - Other Measuring Instrument',
        '142101210038' => 'OML - Other Measures of Length',
        '142101210039' => 'DM - Domestic gas meter',
        '142101210040' => 'WT - Weights',
        '142201611278' => 'Miscellaneous Receipts',
        '142202080006' => 'Fines, Penalties and Forfeitures',
    ];

    ksort($codes); // Sort the array by keys in ascending order

    return (object) $codes;
}

function gfsCodesBill()
{
    return (object) [

        '142101210028' => 'Bulk Storage Tank (BST)',
        '142101210029' => 'WGT – Wagon Tank',
        '142101210030' => 'Fuel pump',
        '142101210031' => 'Pre Packages',
        '142101210032' => 'F/M – Flow Meter',
        '142101210033' => 'Ch/p - check pump',
        '142101210034' => 'FST – Fixed Storage Tank',
        '142101210004' => 'S/B - Spring Balance',
        '140202' => 'Fine And Penalty',

    ];
}
function gfsCodes()
{
    $testingGfsCode = [
        // '142101210024' => 'Vehicle Tank Verification (VTV)',
        // '142101210025' => 'Sandy & Ballast lorry (SBL)',
        // '142101210026' => 'Water Meter',
        '142101210031' => 'Pre-packages',
        '142101210028' => 'Bulk Storage Tank (BST)',
        '142101210029' => 'WGT – Wagon Tank',
        '142101210030' => 'Fuel pump',
        // '142101210031' => 'C/S Counter Scale',
        '142101210032' => 'F/M – Flow Meter',
        '142101210033' => 'Ch/p - check pump',
        '142101210034' => 'FST – Fixed Storage Tank',
        '142101210004' => 'S/B - Spring Balance',
        '140202' => 'Fine And Penalty',

    ];

    $liveGfsCode = [
        '142101210003' => 'Vehicle Tank Verification (VTV)',
        '142101210004' => 'Wb - Weighbridge',
        '142101210005' => 'FST – Fixed Storage Tank',
        '142101210006' => 'Bulk Storage Tank (BST)',
        '142101210007' => 'Pre-packages',
        '142101210008' => 'WGT – Wagon Tank',
        '142101210009' => 'Fuel pump',
        '142101210010' => 'CNG filling Station',
        '142101210011' => 'F/M – Flow Meter',
        '142101210012' => 'Ch/p - check pump',
        '142101210013' => 'Meter',
        '142101210014' => 'Metrological Supervision',
        '142101210015' => 'Pressure gauges',
        '142101210016' => 'Proving Tank',
        '142101210017' => 'Taximeter',
        '142101210018' => 'MR - Metre Rule',
        '142101210019' => 'TM - Tape Measure',
        // '142101210020' => 'M. LE - Measures of Length',
        '142101210021' => 'BRIM - Brim Measure system',
        '142101210022' => 'S/y – Steelyard',
        '142101210023' => 'SDw -Suspended Digital Ware',
        '142101210024' => 'C/S - Counter scale',
        '142101210025' => 'P/s - Platform scale',
        '142101210026' => 'S/B - Spring Balance',
        '142101210027' => 'Bal - Balance',
        '142101210028' => 'Kor - Koroboi',
        '142101210029' => 'Vib – Vibaba',
        '142101210030' => 'Pis – Pishi',
        '142101210031' => 'Ax/w - Weigher',
        '142101210032' => 'Au/W - Automatic Weigher',
        '142101210033' => 'B/S - Beam Scale',
        '142101210034' => 'S/y – Steelyard',
        '142101210035' => 'Sandy & Ballast lorry (SBL)',
        '142101210036' => 'E/m- Electricity meter',
        '142101210037' => 'OMI - Other Measuring Instrument',
        '142101210038' => 'OML - Other Measures of Length',
        '142101210039' => 'DM - Domestic gas meter',
        '142101210040' => 'WT - Weights',
        '142201611278' => 'Miscellaneous Receipts',
        '142202080006' => 'Fines, Penalties and Forfeitures',
    ];

    $env = setting('System.env');

    // return $testingGfsCode;

    if ($env == 'testing') {
        return (object) $testingGfsCode;
    } else {
        return (object) $liveGfsCode;
    }
}

function activityName($code = ''): string
{
    switch ($code) {
        case '142101210003':
            return ' Vehicle Tank Verification (VTV) ';
            break;
        case '142101210004':
            return ' Weighbridge ';
            break;
        case '142101210005':
            return '  Fixed Storage Tank ';
            break;
        case '142101210006':
            return ' Bulk Storage Tank (BST) ';
            break;
        case '142101210007':
            return ' Pre Packaging ';
            break;
        case '142101210008':
            return '  Wagon Tank ';
            break;
        case '142101210009':
            return ' Fuel pump ';
            break;
        case '142101210010':
            return '  Filling Station ';
            break;
        case '142101210011':
            return ' Flow Meter ';
            break;
        case '142101210012':
            return ' Check pump ';
            break;
        case '142101210013':
            return ' Meter ';
            break;
        case '142101210014':
            return ' Metrological Supervision ';
            break;
        case '142101210015':
            return ' Pressure gauges ';
            break;
        case '142101210016':
            return ' Proving Tank ';
            break;
        case '142101210017':
            return ' Taximeter ';
            break;
        case '142101210018':
            return ' Metre Rule ';
            break;
        case '142101210019':
            return '  Tape Measure ';
            break;
        case '142101210020':
            return '  Measures of Length ';
            break;
        case '142101210021':
            return ' Brim Measure system ';
            break;
        case '142101210022':
            return ' Steelyard ';
            break;
        case '142101210023':
            return ' Suspended Digital Ware ';
            break;
        case '142101210024':
            return ' Counter scale ';
            break;
        case '142101210025':
            return ' Platform scale ';
            break;
        case '142101210026':
            return ' Spring Balance ';
            break;
        case '142101210027':
            return ' Balance ';
            break;
        case '142101210028':
            return ' Koroboi ';
            break;
        case '142101210029':
            return ' Vibaba ';
            break;
        case '142101210030':
            return ' Pishi ';
            break;
        case '142101210031':
            return ' Weigher ';
            break;
        case '142101210032':
            return ' Automatic Weigher ';
            break;
        case '142101210033':
            return ' Beam Scale ';
            break;
        case '142101210034':
            return ' Steelyard ';
            break;
        case '142101210035':
            return ' Sandy & Ballast lorry (SBL) ';
            break;
        case '142101210036':
            return ' Electricity meter ';
            break;
        case '142101210037':
            return ' Other Measuring Instrument ';
            break;
        case '142101210038':
            return ' Other Measures of Length ';
            break;
        case '142101210039':
            return ' Domestic gas meter ';
            break;
        case '142101210040':
            return 'Weights ';
            break;
        case '142201611278':
            return ' Miscellaneous Receipts ';
            break;
        case '142202080006':
            return ' Fines, Penalties and Forfeitures ';
            break;
        case '':
            return '  ';
            break;
        default:
            return 'All Activities ';
            break;
    }
}




// 
//


function centerName(): string
{
    $profile = new ProfileModel();
    $userIid = auth()->user()->unique_id;
    $user = $profile->getUser($userIid);
    return $user->centerName;
}

function collectionCenters()
{
    $profile = new ProfileModel();
    return $profile->getCollectionCenters();
}

function collectionYear($_year = null)
{
    $year = $_year ?? date('Y');
    return (object) [
        'startDate' => ($year) . '-07-01',
        'endDate' => ((int) $year + 1) . '-06-30'
    ];
}

function wmaCenter($centerId = '')
{

    $profileModal = new ProfileModel();

    $center = empty($centerId) ? auth()->user()->collection_center : $centerId;

    $res = $profileModal->findCollectionCenter($center);



    return $res;
}

/**
 * Filters an array of data, removing items with a code of '7101'.
 *
 * @param array $data The input data to be filtered.
 * @return array The filtered data, with the first remaining item returned as an array.
 */
function filterResponse($data)
{
    $filtered = array_filter($data, fn($item) => $item->billStatusCode !== '7101');
    return $filtered ? [reset($filtered)] : [reset($data)];
}


/**
 * Generates a text template for a bill.
 *
 * @param object $textParams An associative array containing the following keys:
 *   - `center`: The name of the center.
 *   - `payer`: The name of the payer.
 *   - `amount`: The amount of the bill.
 *   - `items`: The items related to the bill.
 *   - `expiryDate`: The expiry date of the bill.
 *   - `controlNumber`: The control number of the bill.
 * @return string The generated text template for the bill.
 */
function billTextTemplate($textParams)
{
    $billAmount = number_format($textParams->amount);
    $date = date('d/m/Y H:i:s');
    return "$textParams->center inakutaarifu $textParams->payer kulipa deni lako Tsh $billAmount linalohusu huduma ya uhakiki wa $textParams->items  kabla ya tarehe  $textParams->expiryDate kupitia control number $textParams->controlNumber . Tafadhali lipa sasa , BILL HII NI NOTISI (piga 0800110097 bure). $date";
}


/**
 * Generates a text template for a payment receipt.
 *
 * @param object $textParams An associative array containing the following keys:
 *   - `center`: The name of the center.
 *   - `amount`: The amount paid.
 *   - `debt`: The remaining debt.
 *   - `controlNumber`: The control number of the payment.
 *   - `receiptNumber`: The receipt number.
 * @return string The generated text template for the payment receipt.
 */
function paymentTextTemplate($textParams)
{
    $paid = number_format($textParams->amount);
    $debt = number_format($textParams->debt);
    $date = date('d/m/Y H:i:s');

    $pending = $textParams->debt == 0 ? "" : ".Bado unadaiwa Tshs.$debt,";

    $text = "Ndugu Mteja, $textParams->center inakutaarifu Malipo Tshs. $paid yamepokelewa  kwenye  control number  $textParams->controlNumber,Risiti $textParams->receiptNumber $date $pending Ahsante.";

    return $text;
}


function verificationReminderText($textParams)
{

    $date = dateFormatter($textParams->nextVerification);
    switch ($textParams->activity) {
        case setting('Gfs.vtv'):
        case setting('Gfs.sbl'):
            $text = "$textParams->center  inakutaarifu $textParams->name Mmiliki wa $textParams->item Muda wa uhakiki umefika,Wasilisha gari lako  tarehe $date kwa ajili ya uhakiki, Ahsante.";
            break;



        case setting('Gfs.prePackages'):
            $text = "$textParams->center  inakutaarifu $textParams->name   Ukaguzi wa Vipimo na Bidhaa zilizofungashwa utafanyika $date , Ahsante.";
            break;
        case setting('Gfs.weighBridge'):
            $text = "$textParams->center  inakutaarifu $textParams->name   Mmiliki wa Weighbridge watafika kufanya uhakiki tarehe $date , Ahsante.";
            break;
        case setting('Gfs.wagonTank'):
            $text = "$textParams->center  inakutaarifu $textParams->name   Mmiliki wa tank watafika kufanya uhakiki tarehe $date , Ahsante.";
            break;
        case setting('Gfs.checkPump'):
        case setting('Gfs.fuelPump'):
        case setting('Gfs.cngFillingStation'):
            $text = "$textParams->center  inakutaarifu $textParams->name  uhakiki wa pump utafanyika tarehe $date , Ahsante.";
            break;
        case setting('Gfs.fst'):
            $text = "$textParams->center  inakutaarifu $textParams->name   Mmiliki wa Visima vya Mafuta watafika  kwa uhakiki tarehe $date , Ahsante.";
            break;
        case setting('Gfs.bst'):
            $text = "$textParams->center  inakutaarifu $textParams->name   Mmiliki wa  Matenki ya kuhifadhia Mafuta watafika  kwa uhakiki tarehe $date , Ahsante.";
            break;
        case setting('Gfs.weights'):
        case setting('Gfs.springBalance'):
        case setting('Gfs.balance'):
        case setting('Gfs.weigher'):
        case setting('Gfs.automaticWeigher'):
        case setting('Gfs.beamScale'):
        case setting('Gfs.electricityMeter'):
        case setting('Gfs.pishi'):
        case setting('Gfs.vibaba'):
        case setting('Gfs.koroboi'):
        case setting('Gfs.platformScale'):
        case setting('Gfs.counterScale'):
        case setting('Gfs.suspendedDigitalWare'):
        case setting('Gfs.measuresOfLength'):
        case setting('Gfs.tapeMeasure'):
        case setting('Gfs.metreRule'):
        case setting('Gfs.taxiMeter'):
        case setting('Gfs.provingTank'):
        case setting('Gfs.pressureGauges'):
        case setting('Gfs.koroboi'):

            $text = "$textParams->center  inakutaarifu $textParams->name  Mmiliki wa ($textParams->item ) Muda wa uhakiki umefika,Wasilisha Kipimo chako katika Ofisi ya Wakala wa vipimo iliyopo karibu nawewe kabla au ifikapo tarehe $date. Ahsante";
            break;

        default:
            $text = "$textParams->center  inakutaarifu $textParams->name  Muda wa uhakiki umefika,Wasilisha Kipimo chako katika Ofisi ya Wakala wa vipimo iliyopo karibu nawewe kabla au ifikapo tarehe $date. Ahsante";
            break;
    }



    return $text;
}



function billDataArray($billArr, $institution)
{
    $bill = (object) $billArr;
    $extendedExpiryDate = date("Y-m-d\TH:i:s", strtotime("+360 days"));
    $billAmount = $bill->BillAmt;
    $fifteenPercent = sprintf('%.2f', 0.15 * $billAmount);
    $eightyFivePercent = sprintf('%.2f', 0.85 * $billAmount);

    // $amount = $institution == 'wma' ? $eightyFivePercent : $fifteenPercent;

    $billId = $institution == 'wma' ? $bill->BillId : randomString();




    $user = auth()->user();
    return [
        'BillTyp' => $bill->BillTyp,
        'RequestId' => $bill->RequestId,
        'CollCentCode' => $bill->CollCentCode,
        'isTrBill' => 'Yes',
        'CustId' => $bill->CustId,
        'CustTin' => $bill->CustTin,
        'GrpBillId' => $bill->GrpBillId,
        'CustIdTyp' => $bill->CustIdTyp,
        'BillId' => $billId,
        'Activity' => $bill->Activity,
        'BillRef' => numString(10),
        'BillAmt' => $billAmount,
        'TotalBillAmount' => (float) $billAmount,
        'BillAmtWords' => toWords($billAmount),
        'MiscAmt' => 0.00,
        'BillExprDt' => $bill->BillExprDt,
        'extendedExpiryDate' => $extendedExpiryDate,
        'PyrId' => randomString(),
        'PyrName' => $bill->PyrName,
        'BillDesc' => $bill->BillDesc,
        'BillGenDt' => $bill->BillGenDt,
        'BillGenBy' => isset($user->username) ? $user->username : 'License Applicant',
        'CollectionCenter' => isset($user->collection_center) ? $user->collection_center : '001',
        'BillApprBy' => 'wma-hq',
        'PyrCellNum' => $bill->PyrCellNum,
        'PyrEmail' => $bill->PyrEmail,
        'Ccy' => 'TZS',
        'BillEqvAmt' => $billAmount,
        'RemFlag' => 'true',
        'BillPayOpt' => $bill->BillPayOpt,
        'method' => $bill->method == '' ? 'MobileTransfer' : $bill->method,
        'UserId' => isset($user->unique_id) ? $user->unique_id : '12345',
        'Task' => $bill->Task,
        'deviceId' => isset($user->deviceId) ? $user->deviceId : '',
        'SwiftCode' => $bill->SwiftCode != '' ? $bill->SwiftCode : '',
    ];
}

//configuration for TR/combined bill payload
function combinedBillContent($wma, $tr, $type = '')
{
    $wmaBill = (object) $wma;
    $trBill = (object) $tr;
    $requestId = $wmaBill->RequestId;
    $spGroupCode = setting('Bill.spGroupCodeCombined'); //'SPG1103';
    $systemCode = setting('Bill.systemCode'); //'TWMATR001';
    $wmaSpCode = setting('Bill.wmaSpCode'); // 'SP19960';
    $trSpCode = setting('Bill.trSpCode'); // 'SP19966';
    $groupBillId = $wmaBill->GrpBillId;



    $trId = 'HQQ' . randomString();
    // $trBill->BillId = $trId;

    $trBill->BillItems['SubSpCode'] = setting('Bill.trSubSpCode'); //'1001';
    $trBill->BillItems['GfsCode'] = '141133240001';
    $trBill->BillItems['CollSp'] = $trSpCode;
    $trBill->BillItems['RefBillId'] = $trBill->BillId;
    $trBill->BillItems['BillItemAmt'] = $trBill->BillAmt;
    $trBill->BillItems['BillItemEqvAmt'] = $trBill->BillAmt;
    $trBillItemRef = 'TR' . numString(10);



    $reason = '';
    $costomerControlNumber = '';
    $wmaControlNumber = '';
    $trControlNumber = '';

    $billAmount = $wmaBill->BillAmt;


    $fifteenPercent = sprintf('%.2f', 0.15 * $billAmount);
    $eightyFivePercent = sprintf('%.2f', 0.85 * $billAmount);

    // $amount = $institution == 'wma' ? $eightyFivePercent : $fifteenPercent;


    if ($type == 'renew') {
        $ctrNumbers = explode(',', $wmaBill->billControlNumber);
        $WmactrNumbers = $ctrNumbers[0];
        $TrctrNumbers = $ctrNumbers[1];
        $costomerControlNumber .= "<CustCntrNum>$wmaBill->PayCntrNum</CustCntrNum>";
        $wmaControlNumber .= "<BillCntrNum>$WmactrNumbers</BillCntrNum>";
        $trControlNumber .= "<BillCntrNum>$TrctrNumbers</BillCntrNum>";
        $reason .= "<ReuseReasn>Billed could not be paid</ReuseReasn>";
    }

    $collectionCenterCode = $wmaBill->CollCentCode; // 'CC1002000199419'

    $extendedExpiryDate = (new DateTime())->modify('+360 days')->format('Y-m-d\TH:i:s');

    $content = "<billSubReq>
        <BillHdr>
          <ReqId>$requestId</ReqId>
          <SpGrpCode>$spGroupCode</SpGrpCode>
          <SysCode>$systemCode</SysCode>
          <BillTyp>2</BillTyp>
          <PayTyp>1</PayTyp>
          <GrpBillId>$groupBillId</GrpBillId>
          " . $costomerControlNumber . "
        </BillHdr>
        <BillDtls>
          <BillDtl>
            <BillId>$wmaBill->BillId</BillId>
            <SpCode>$wmaSpCode</SpCode>
            <CollCentCode>$collectionCenterCode</CollCentCode>
            <BillDesc>$wmaBill->BillDesc</BillDesc>
            <CustTin></CustTin>
            <CustId>$wmaBill->CustId</CustId>
            <CustIdTyp>5</CustIdTyp>
            <CustAccnt>30997</CustAccnt>
            <CustName>$wmaBill->PyrName</CustName>
            <CustCellNum>$wmaBill->PyrCellNum</CustCellNum>
            <CustEmail>$wmaBill->PyrEmail</CustEmail>
            <BillGenDt>$wmaBill->BillGenDt</BillGenDt>
            <BillExprDt>$extendedExpiryDate</BillExprDt>
            <BillGenBy>$wmaBill->BillGenBy</BillGenBy>
            <BillApprBy>$wmaBill->BillApprBy</BillApprBy>
            <BillAmt>$eightyFivePercent</BillAmt>
            <BillEqvAmt>$eightyFivePercent</BillEqvAmt>
            <MinPayAmt>0.01</MinPayAmt>
            <Ccy>TZS</Ccy>
            <ExchRate>1.00</ExchRate>
            <BillPayOpt>$wmaBill->BillPayOpt</BillPayOpt>
            <PayPlan>1</PayPlan>
            <PayLimTyp>1</PayLimTyp>
            <PayLimAmt>0.00</PayLimAmt>
            <CollPsp></CollPsp>
            " . $wmaControlNumber . "
            " . $reason . "
            " . arrayToXml($wmaBill->BillItems) . "
          </BillDtl>
          <BillDtl>
            <BillId>$trBill->BillId</BillId>
            <SpCode>$trSpCode</SpCode>
            <CollCentCode>CC1000000199517</CollCentCode>
            <BillDesc>$trBill->BillDesc</BillDesc>
            <CustTin></CustTin>
            <CustId>$trBill->CustId</CustId>
            <CustIdTyp>5</CustIdTyp>
            <CustAccnt>30997</CustAccnt>
            <CustName>$trBill->PyrName</CustName>
            <CustCellNum>$trBill->PyrCellNum</CustCellNum>
            <CustEmail>$trBill->PyrEmail</CustEmail>
            <BillGenDt>$trBill->BillGenDt</BillGenDt>
            <BillExprDt>$extendedExpiryDate</BillExprDt>
            <BillGenBy>$trBill->BillGenBy</BillGenBy>
            <BillApprBy>$trBill->BillApprBy</BillApprBy>
            <BillAmt>$fifteenPercent</BillAmt>
            <BillEqvAmt>$fifteenPercent</BillEqvAmt>
            <MinPayAmt>0.01</MinPayAmt>
            <Ccy>TZS</Ccy>
            <ExchRate>1.00</ExchRate>
            <BillPayOpt>$trBill->BillPayOpt</BillPayOpt>
            <PayPlan>1</PayPlan>
            <PayLimTyp>1</PayLimTyp>
            <PayLimAmt>0.00</PayLimAmt>
            <CollPsp></CollPsp>
            " . $trControlNumber . "
            " . $reason . "
            <BillItems>
             <BillItem>
               <RefBillId>$trBill->BillId</RefBillId>
               <SubSpCode>1002</SubSpCode>
               <GfsCode>141133240001</GfsCode>
               <BillItemRef>$trBillItemRef</BillItemRef>
               <UseItemRefOnPay>N</UseItemRefOnPay>
               <BillItemAmt>$fifteenPercent</BillItemAmt>
               <BillItemEqvAmt>$fifteenPercent</BillItemEqvAmt>
               <CollSp>$trSpCode</CollSp>
             </BillItem>
         </BillItems>
          </BillDtl>
        </BillDtls>
      </billSubReq>";

    return $content;
}


//configuration for single/normal bill payload
function normalBillContent($wma, $type = '')
{

    $wmaBill = (object) $wma;
    $requestId = $wmaBill->RequestId;


    $spGroupCode = setting('Bill.spGroupCodeSingle'); //'';
    $systemCode = setting('Bill.systemCode'); //'';
    $wmaSpCode = setting('Bill.wmaSpCode'); // '';

    $extendedExpiryDate = (new DateTime())->modify('+360 days')->format('Y-m-d\TH:i:s');

    $groupBillId = $wmaBill->BillId;




    $reason = '';
    $wmaControlNumber = '';
    $costomerControlNumber = '';

    $collectionCenterCode = $wmaBill->CollCentCode;



    if ($type == 'renew') {
        $wmaControlNumber .= "<BillCntrNum>$wmaBill->PayCntrNum</BillCntrNum>";
        $reason .= "<ReuseReasn>Billed could not be paid</ReuseReasn>";
        $costomerControlNumber .= "<CustCntrNum>$wmaBill->PayCntrNum</CustCntrNum>";
    }



    $content = "<billSubReq>
        <BillHdr>
          <ReqId>$requestId</ReqId>
          <SpGrpCode>$spGroupCode</SpGrpCode>
          <SysCode>$systemCode</SysCode>
          <BillTyp>1</BillTyp>
          <PayTyp>2</PayTyp>
          <GrpBillId>$groupBillId</GrpBillId>
          " . $costomerControlNumber . "
        </BillHdr>
        <BillDtls>
          <BillDtl>
            <BillId>$groupBillId</BillId>
            <SpCode>$wmaSpCode</SpCode>
            <CollCentCode>$collectionCenterCode</CollCentCode>
            <BillDesc>$wmaBill->BillDesc</BillDesc>
            <CustTin></CustTin>
            <CustId>$wmaBill->CustId</CustId>
            <CustIdTyp>5</CustIdTyp>
            <CustAccnt>30997</CustAccnt>
            <CustName>$wmaBill->PyrName</CustName>
            <CustCellNum>$wmaBill->PyrCellNum</CustCellNum>
            <CustEmail>$wmaBill->PyrEmail</CustEmail>
            <BillGenDt>$wmaBill->BillGenDt</BillGenDt>
            <BillExprDt>$extendedExpiryDate</BillExprDt>
            <BillGenBy>$wmaBill->BillGenBy</BillGenBy>
            <BillApprBy>$wmaBill->BillApprBy</BillApprBy>
            <BillAmt>$wmaBill->BillAmt</BillAmt>
            <BillEqvAmt>$wmaBill->BillEqvAmt</BillEqvAmt>
            <MinPayAmt>0.01</MinPayAmt>
            <Ccy>TZS</Ccy>
            <ExchRate>1.00</ExchRate>
            <BillPayOpt>$wmaBill->BillPayOpt</BillPayOpt>
            <PayPlan>1</PayPlan>
            <PayLimTyp>1</PayLimTyp>
            <PayLimAmt>0.00</PayLimAmt>
            <CollPsp></CollPsp>
            " . $wmaControlNumber . "
            " . $reason . "
            " . arrayToXml($wmaBill->BillItems) . "
          </BillDtl>
        </BillDtls>
      </billSubReq>";

    return $content;
}


/**
 * Converts an array to an XML string, excluding specified keys.
 *
 * @param array $array The array to be converted to XML.
 * @param array $except The keys to be excluded from the XML.
 * @return string The XML string representation of the array.
 */
function arrayToXml(array $array): string
{
    $except = ['BillId', 'ItemName', 'UserId', 'Task', 'Status', 'ItemQuantity', 'SingleItemAmount', 'NextVerification', 'fob', 'tansardNumber', 'date', 'RequestId', 'center'];

    // Exclude specific keys from the array
    $filteredArray = array_filter($array, function ($key) use ($except) {
        return !in_array($key, $except);
    }, ARRAY_FILTER_USE_KEY);

    // Convert the filtered array to XML
    $xml = Array2XML::createXML('BillItems', ['BillItem' => arrayExcept($array, $except)])->saveXML();

    // Remove the XML declaration
    $xmlPayload = ltrim(str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $xml));

    return $xmlPayload;
}
//