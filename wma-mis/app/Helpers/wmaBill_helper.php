<?php

use App\Models\ProfileModel;

function normaSingleBill($billData): string
{

    $bill = $billData->bill;

    $billAmount = number_format($bill->BillAmt);
    $xpDate = dateFormatter($bill->BillExprDt);
    $printedOn = dateFormatter(date('Y-m-d'));
    $spCode = setting('Bill.spCode');


    $interval = (new DateTime())->diff(new DateTime($bill->BillExprDt))->days;


    $xprDate = ($bill->BillExprDt && $bill->extendedExpiryDate != null) 
    ? dateFormatter(date('Y-m-d', strtotime("+$interval days")))
    : dateFormatter($bill->BillExprDt);



    $billItems = $billData->billItems;
    $items = '';
    $sn = 0;
    foreach ($billItems as $billItem) {
        $amount = number_format($billItem->BillItemAmt);
        $fob = $billItem->fob;
        $tansardNumber = $billItem->tansardNumber;
        $tansardDate = date('d-m-Y', strtotime($billItem->date));
        $ppg = $billItem->GfsCode == setting('Gfs.prePackages') && $fob != '' &&  $tansardNumber != '' ? "F.O.B : $fob   Tansad Number : $tansardNumber Tansard Date: $tansardDate" : '';
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
                                <p class="text-small text-center" style="font-size: 11px; margin-top: -1px;margin-left:-76px;">
                                    Scan And Pay
                                </p>
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
                                </tbody>
                            </table>




                            <br>
                            <table class="table table-sm table-borderless">
                                   <tr style="display:flex">
                                    <td style="width:50%;"><b>Total Billed Amount:</b></b></td>
                                    <!-- <td></td> -->
                                    <td style="width:50%"><b id="billTotal"> $billAmount (TZS)</b></td>
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
function receiptView($receiptData): string
{
    $user = auth()->user();

    $receipt = $receiptData->receipt;

    $paidAmount = number_format($receipt->PaidAmt);
    $billed = number_format($receipt->BillAmt);
    $amtInWords = toWords($receipt->PaidAmt);
    $outstanding = number_format($receipt->BillAmt - $receipt->clearedAmount);
    $paymentDate = dateFormatter($receipt->TrxDtTm);
    $issuedOn = dateFormatter($receipt->BillGenDt);
    $phoneNumber = str_replace('255', '0', $receipt->PyrCellNum);




    $receiptItems = $receiptData->billItems;
    $items = '';
    $sn = 0;
    foreach ($receiptItems as $receiptItem) {
        $amount = number_format($receiptItem->BillItemAmt);
        $fob = $receiptItem->fob;
        $tansardNumber = $receiptItem->tansardNumber;
        $tansardDate = date('d-m-Y', strtotime($receiptItem->date));
        $ppg = $receiptItem->GfsCode == setting('Gfs.prePackages') && $fob != '' &&  $tansardNumber != '' ? "F.O.B : $fob   Tansad Number : $tansardNumber Tansard Date: $tansardDate" : '';
        $sn++;
        $items .= <<<"HTML"
          <tr style="display:flex">
            <td style="width:5%">$sn</td>
            <td style="width:70%">$receiptItem->ItemName  $ppg</td>
            <td style="width:30%"><b>$amount</b></td>
        </tr>
     HTML;
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
                               <tr>
                              
                              
                               </tr>
                                </tbody>
                               
                            </table>




                            <br>
                            <table class="table table-sm table-borderless">
                                   <tr style="display:flex">
                                    <td style="width:50%;"><b>Total Billed Amount:</b></b></td>
                                    <!-- <td></td> -->
                                    <td style="width:50%"><b id="billTotal"> $billed (TZS)</b></td>
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



function transferNormalBill($billData)
{

    $bill = $billData->bill;

    $billAmount = ($bill->BillAmt);
    $xpDate = dateFormatter($bill->BillExprDt);
    $printedOn = dateFormatter(date('Y-m-d'));





    $billItems = $billData->billItems;
  
    $items = '';
    $sn = 0;
    foreach ($billItems as $billItem) {
        $amount = number_format($billItem->BillItemAmt);
        $fob = $billItem->fob;
        $tansardNumber = $billItem->tansardNumber;
        $tansardDate = date('d-m-Y', strtotime($billItem->date));
        $ppg = $billItem->GfsCode == setting('Gfs.prePackages') && $fob != '' &&  $tansardNumber != '' ? "F.O.B : $fob   Tansad Number : $tansardNumber Tansard Date: $tansardDate" : '';
        $sn++;
        $items .= <<<"HTML"
        <tr style="display:flex">
            <td style="width:5%">$sn</td>
            <td style="width:70%">$billItem->ItemName $ppg </td>
            <td style="width:30%">Tsh $amount</td>
        </tr>
     HTML;
    }

    $amt = auth()->user()->inGroup('superadmin') ? $bill->BillAmt : number_format($bill->BillAmt);

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
                                <h6>Transfer Amount $amt </h6>
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
                            <div class="qr" style="position:absolute; top: -320px;left:620px">
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
                                    $items;
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

