<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <!-- <h1 class="m-0 text-dark"></h1> -->
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?= $page['heading'] ?></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<div class="container-fluid">



    <?= view('Components/bill') ?>




    <?= view('Components/bill') ?>
    <script>
        $('#exampleModal').on('show.bs.modal', event => {
            var button = $(event.relatedTarget);
            var modal = $(this);
            // Use above variables to manipulate the DOM

        });
    </script>
    <form id="billSubmissionRequest">
        <div class="card">

            <div class="card-header">

                BILL CREATION
            </div>
            <div class="card-body">


                <div class="row">


                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="must" for="">Payer Name</label>
                            <input type="text" name="PyrName" id="PyrName" class="form-control" required>
                            <span class="PyrName text-danger" data-error></span>

                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="must" for="">Bill Description </label>
                            <input type="text" name="BillDesc" id="BillDesc" class="form-control" required>
                            <span class="BillDesc text-danger" data-error></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Email Address</label>
                            <input type="email" name="PyrEmail" id="PyrEmail" class="form-control">
                            <span class="PyrEmail text-danger" data-error></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="must" for="">Phone Number </label>
                            <input type="text" name="PyrCellNum" id="PyrCellNum" class="form-control " required oninput=" this.value = this.value.replace(/\D/g, '')" maxlength="10">
                            <span class="PyrCellNum text-danger" data-error></span>

                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="must" for="">Currency </label>
                            <select class="form-control" name="Ccy" id="Ccy">
                                <option value="TZS">TZS</option>
                                <!-- <option value="USD">USD</option> -->

                            </select>
                        </div>
                    </div>
                    <!-- <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Exchange Rate<span class="text-danger"></span></label>
                            <input type="text" id="" class="form-control">

                        </div>
                    </div> -->

                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="must" for="">Days</label>
                                    <input type="number" name="days" class="form-control" oninput="calculateDate(this.value)" required>

                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="must" for="">Expiry Date<span class="text-danger"></span></label>
                                    <input type="text" name="BillExprDt" id="expiryDate" readonly class="form-control" required>

                                </div>
                            </div>
                        </div>
                    </div>






                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="must" for="">Payment Option</label>
                            <select class="form-control" name="BillPayOpt" id="BillPayOpt" onchange="checkPaymentOpt(this.value)" required>
                                <!-- <option value="1">Full</option> -->
                                <option value="">--Select Payment Option--</option>
                                <option value="3">Exact</option>
                                <option value="2">Partial</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Set Reminder</label>
                            <div class="form-check">
                                <input class="form-check-input" name="RemFlag" type="checkbox" checked="" style="transform:scale(1.3) ; accent-color:#DB611E;cursor:pointer"> &nbsp;
                                <label class="form-check-label">Yes</label>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

        </div>


        <div class="bill">
            <!-- <div class="card-header1">REVENUE SOURCE </div> -->
            <div class="card-body1">

                <div class="card" id="source">
                    <div class="card-header">
                        REVENUE SOURCE
                        <button type="button" id="addBtn" class="btn btn-primary  btn-sm" onclick="addRevenueSource()" style="float:right"><i class="far fa-plus"></i></button>
                    </div>
                    <div class="boxy">
                        <div class="row p-3">
                            <div class="col-md-3 stretch">
                                <div class="form-group">
                                    <label class="must" for="">Task</label>
                                    <select class="form-control task select2bs4" name="Task[]" id="" style="width:100%" required onchange="handleTaskChange(this)">
                                        <option value="">--Select Task--</option>
                                        <option value="Verification">Verification</option>
                                        <option value="Reverification">Reverification</option>
                                        <option value="Inspection">Inspection</option>
                                        <option value="Consultation">Consultation</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 stretch">
                                <div class="form-group">
                                    <label class="must" for="">Select Revenue Source </label>
                                    <select class="form-control billItemName select2bs4" name="GfsCode[]" id="" style="width:100%" onchange="toggleFields(this,'001')">
                                        <option value="">--Select Revenue Source--</option>
                                        <option value="142101210007">Pre Package(Imported)</option>
                                        <?php $except = ['142101210003', '142101210013', '142101210035', '142101210007'] ?>
                                        <?php foreach (gfsCodes() as $key => $value) : ?>
                                            <?php if (!in_array($key, $except)) : ?>
                                                <option value="<?= $key ?>"><?= $value ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>

                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3 ommit">
                                <div class="form-group">
                                    <label class="must" for="">Item Name</label>
                                    <input type="text" name="ItemName[]" id="ItemName" class="form-control " placeholder="" required>
                                    <small class=" ItemName text-danger"></small>
                                </div>
                            </div>

                            <input type="text" name="BillItemRef[]" id="" value="<?= randomString() ?>" class="form-control" placeholder="" hidden>

                            <div class="col-md-3 ommit">
                                <div class="form-group">
                                    <label class="must" for="">Capacity/Quantity</label>
                                    <input type="number" name="Capacity[]" id="Capacity" class="form-control " placeholder="" required min="0">
                                    <small class="Capacity text-danger"></small>
                                </div>
                            </div>
                            <div class="col-md-3 ommit">
                                <div class="form-group ">
                                    <label class="must" for="">Unit</label>
                                    <select class="form-control" name="ItemUnit[]" id="Unit">
                                        <option value="">--Select Unit--</option>
                                        <optgroup label="Weight">
                                            <option value="mg">mg</option>
                                            <option value="g">g</option>
                                            <option value="kg">kg</option>
                                        </optgroup>
                                        <optgroup label="Volume">
                                            <option value="mL">mL</option>
                                            <option value="L">L</option>
                                            <option value="cubCm">cm <sup>3</sup></option>
                                            <option value="cubM">m <sup>3</sup></option>
                                        </optgroup>
                                        <optgroup label="Area">
                                            <option value="sqCm">cm <sup>2</sup></option>
                                            <option value="sqM">m <sup>2</sup></option>
                                        </optgroup>
                                        <optgroup label="Length">
                                            <option value="mm">mm </option>
                                            <option value="cm">cm </option>
                                            <option value="m">m </option>

                                        </optgroup>
                                        <optgroup label="Count">
                                            <option value="Pieces">Pieces </option>
                                            <option value="Items">Items </option>


                                        </optgroup>
                                        <small class="Unit text-danger"></small>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3 ommit">
                                <div class="form-group">
                                    <label class="must" for="">Status</label>
                                    <select class="form-control Status select2bs4" name="Status[]" id="" style="width:100%" required>
                                        <option value="">--Select Status--</option>
                                        <option value="Pass">Pass</option>
                                        <option value="Rejected">Rejected</option>
                                        <option value="Adjusted">Adjusted</option>
                                        <option value="Condemned">Condemned</option>
                                        <option value="None">None</option>
                                    </select>
                                    <small class=" Status text-danger"></small>
                                </div>
                            </div>
                            <div class="col-md-6 billFigures">
                                <div class="row">
                                    <div class="col-md-4 form-group">
                                        <label for="">Amount</label>
                                        <input type="text" name="SingleItemAmount[]" id="singleItemAmount" pattern="[0-9]*" oninput="calcTotal(this)" class="form-control singleItemAmount" placeholder="" required>
                                        <small class=" SingleItemAmount text-danger"></small>
                                    </div>
                                    <div class="col-md-4 form-group ">
                                        <label for="">Quantity</label>
                                        <input type="number" min="1" max="100" name="ItemQuantity[]" id="ItemQuantity" pattern="[0-9]*" oninput="getTotal(this)" class="form-control qty" placeholder="" required>
                                        <small class="text-danger"></small>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label for="">Bill Item Amount</label>
                                        <input type="text" name="BillItemAmt[]" id="BillItemAmt" pattern="[0-9]*" class="form-control itemAmount" placeholder="" required readonly>
                                        <small class=" BillItemAmt text-danger"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" id="imported001" >
                                <div class="row">
                                    <div class="col-md-4 form-group">
                                        <label for="">F.O.B</label>
                                        <input type="text" name="fob[]" id="fob" pattern="[0-9]*" class="form-control" placeholder="" required oninput="formatDigits(this)">
                                        <small class=" fob text-danger"></small>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label for="">TANSAD Number</label>
                                        <input type="text" name="tansardNumber[]" id="tansardNumber" class="form-control" placeholder="Tansard Number" required>
                                        <small class=" fob text-danger"></small>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label for="">TANSAD Date</label>
                                        <input type="date" name="date[]" id="date" class="form-control" placeholder="Date" required>
                                        <small class=" fob text-danger"></small>
                                    </div>
                                </div>
                            </div>






                        </div>
                    </div>
                </div>
                <div id="billItemsSource"></div>






            </div>
            <div class="container-fluid">
                <div class="card p-2 row">

                    <div class="form-group col-md-6">
                        <label for="">Total Billed Amount</label>
                        <input type="text" name="BillEqvAmt" id="BillEqvAmt" class="form-control" placeholder="Total Billed Amount" readonly>
                        <!-- <small id="helpId" class="text-muted">Help text</small> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">PAYMENT METHODS</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label for="">Method</label><br>
                        <div class="form-check form-check-inline">
                            <label class="form-check-label" for="mobile">
                                <input class="form-check-input" style="accent-color:#DB611E;transform:scale(1.25)" type="radio" name="method" id="mobile" value="MobileTransfer" onchange="changeTransfer(this.value)"> Mobile Money Or Bank
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <label class="form-check-label" for="bank">
                                <input class="form-check-input" style="accent-color:#DB611E;transform:scale(1.25)" type="radio" name="method" id="bank" value="BankTransfer" oncanplay="
                                " onchange="changeTransfer(this.value)"> Electronic Fund Transfer
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="">Transfer To Bank</label>
                            <select class="form-control" disabled name="SwiftCode" id="swiftCode" required>
                                <option value="">--Select Bank--</option>
                                <option value="NMIBTZTZ">National Microfinance Bank</option>
                                <option value="CORUTZTZ">CRDB Bank</option>
                                <!-- <option value="TANZTZTX">Bank Of Tanzania (BOT)</option> -->
                            </select>
                        </div>
                    </div>
                </div>


            </div>
            <div class="card-footer">
                <button type="submit" id="submit" class="btn btn-primary btn-sm">
                    <div class="spinner-border spinner-border-sm" id="spinner" role="status" style="display: none;"></div>
                    Submit
                </button>
            </div>
        </div>


</div>
</from>

<script>
    function pickCenter(center) {
        document.querySelector('#CollectionCenter').value = center
    }

    function formatDigits(input) {
        // Remove non-numeric and non-decimal point characters
        let value = input.value.replace(/[^0-9.]/g, '');

        // Add commas for thousands separation
        value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

        // Update the input value
        input.value = value;
    }


    function calcTotal(amountInput) {
        amountInput.value = amountInput.value.replace(/\D/g, '').replace(/,/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ',')
        const parent = amountInput.parentNode.parentNode
        const quantityInput = +parent.children[1].querySelector('.qty').value
        const itemAmount = parent.children[2].querySelector('.itemAmount')
        itemAmount.value = new Intl.NumberFormat().format(quantityInput * amountInput.value.replace(/,/g, ''))
        calculateTotalAmount()


    }

    function toggleFields(selectElement, id) {
        //impp

        const importUi = `
         <div class="row">
            <div class="col-md-4 form-group">
            <label for="">F.O.B</label>
            <input type="text" name="fob[]" id="fob" pattern="[0-9]*" class="form-control" placeholder="" required oninput="formatDigits(this)" >
            <small class=" fob text-danger"></small>
             </div>
                <div class="col-md-4 form-group">
                <label for="">TANSAD Number</label>
                <input type="text" name="tansardNumber[]" id="tansardNumber" class="form-control" placeholder="Tansard Number" required>
                <small class=" fob text-danger"></small>
                                    
                 </div>
                <div class="col-md-4 form-group">
                <label for="">Date</label>
                <input type="date" name="date[]" id="date" class="form-control" placeholder="Date" required>
                <small class=" fob text-danger"></small>
            </div>
        </div>
         `
        const selectedValue = selectElement.value;
        const importedDiv = selectElement.closest(".bill").querySelector(`#imported${id}`);

        console.log(importedDiv)

        if (selectedValue === "142101210007") {
            importedDiv.innerHTML = importUi;
        } else {
            importedDiv.innerHTML = "";
        }
    }

    function handleTaskChange(selectElement) {

        let randomNumber = Math.floor(Math.random() * 10000000000)

        function isSelected(current, changed) {
            if (current == changed.value) {
                return 'selected'
            } else {
                return ''
            }
        }

        function xxx(param) {
            return param + '12345'

        }


        //    console.log(selectElement.value)
        //    console.log('=========================================')

        // console.log(isSelected('Consultation',selectElement))




        let fullRevenueSource = `
                     <div class='XXXXX'>
                    <div class="row p-3">
                    <div class="col-md-3 stretch">
                            <div class="form-group">
                                <label class="must" for="">Task</label>
                                <select class="form-control task select2bs4" name="Task[]" id="task${randomNumber}" style="width:100%" required  onchange="handleTaskChange(this)">
                                <option value="">--Select Task--</option>
                                <option ${isSelected('Verification',selectElement)} value="Verification">Verification</option>
                                <option ${isSelected('Reverification',selectElement)} value="Reverification">Reverification</option>
                                <option ${isSelected('Inspection',selectElement)} value="Inspection">Inspection</option>
                                <option ${isSelected('Consultation',selectElement)} value="Consultation">Consultation</option>
                                <option ${isSelected('Other',selectElement)} value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 stretch">
                            <div class="form-group">
                                <label class="must" for="">Select Revenue Source </label>
                                
                                <select class="form-control billItemName select2bs4" name="GfsCode[]" id="billItemName${randomNumber}" style="width:100%" onchange="toggleFields(this,'${randomNumber}')">
                                <option value="">--Select Revenue Source--</option>
                                <option value="142101210007">Pre Package(Imported)</option>
                                     <?php foreach (gfsCodes() as $key => $value) : ?>
                                      <?php if (!in_array($key, $except)) : ?>
                                        <option value="<?= $key ?>"><?= $value ?></option>
                                      <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3 ommit">
                            <div class="form-group">
                                <label class="must" for="">Item Name</label>
                                <input type="text" name="ItemName[]" id="ItemName${randomNumber}" class="form-control " placeholder="" required>
                                <small class=" ItemName text-danger"></small>
                            </div>
                        </div>

                        <input type="text" name="BillItemRef[]" id="" value="<?= randomString() ?>" class="form-control" placeholder="" hidden>

                        <div class="col-md-3 ommit">
                            <div class="form-group">
                                <label class="must" for="">Capacity/Quantity</label>
                                <input type="number" name="Capacity[]" id="Capacity${randomNumber}" class="form-control " placeholder="" required min="0">
                                <small class="Capacity text-danger"></small>
                            </div>
                        </div>
                        <div class="col-md-3 ommit">
                            <div class="form-group">
                                <label class="must" for="">Unit</label>
                                <select class="form-control" name="ItemUnit[]" id="Unit${randomNumber}">
                                <option value="">--Select Unit--</option>
                                    <optgroup label="Weight">
                                        <option value="mg">mg</option>
                                        <option value="g">g</option>
                                        <option value="kg">kg</option>
                                    </optgroup>
                                    <optgroup label="Volume">
                                        <option value="mL">mL</option>
                                        <option value="L">L</option>
                                        <option value="cubCm">cm <sup>3</sup></option>
                                        <option value="cubM">m <sup>3</sup></option>
                                    </optgroup>
                                    <optgroup label="Area">
                                        <option value="sqCm">cm <sup>2</sup></option>
                                        <option value="sqM">m <sup>2</sup></option>
                                    </optgroup>
                                    <optgroup label="Length">
                                        <option value="mm">mm </option>
                                        <option value="cm">cm </option>
                                        <option value="m">m </option>

                                    </optgroup>
                                    <optgroup label="Count">
                                        <option value="Pieces">Pieces </option>
                                        <option value="Items">Items </option>


                                    </optgroup>
                                    <small class="Unit text-danger"></small>
                                </select>
                            </div>
                        </div>
                       
                        
                        <div class="col-md-3 ommit">
                            <div class="form-group">
                            <label class="must" for="">Status</label>
                                <select class="form-control Status select2bs4" name="Status[]" id="Status${randomNumber}" style="width:100%" required>
                                    <option value="">--Select Status--</option>
                                    <option value="Pass">Pass</option>
                                    <option value="Rejected">Rejected</option>
                                    <option value="Adjusted">Adjusted</option>
                                    <option value="Condemned">Condemned</option>
                                    <option value="None">None</option>
                                </select>
                                <small class=" Status text-danger"></small>
                            </div>
                        </div>
                        <div class="col-md-6 billFigures">
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label for="">Amount</label>
                                    <input type="text" name="SingleItemAmount[]" id="singleItemAmount${randomNumber}" pattern="[0-9]*" oninput="calcTotal(this)" class="form-control singleItemAmount" placeholder="" required>
                                    <small class=" SingleItemAmount text-danger"></small>
                                </div>
                                <div class="col-md-4 form-group ">
                                    <label for="">Quantity</label>
                                    <input type="number" min="1" max="100" name="ItemQuantity[]" id="ItemQuantity${randomNumber}" pattern="[0-9]*" oninput="getTotal(this)" class="form-control qty" placeholder="" required>
                                    <small class="text-danger"></small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label for="">Bill Item Amount</label>
                                    <input type="text" name="BillItemAmt[]" id="BillItemAmt${randomNumber}" pattern="[0-9]*"  class="form-control itemAmount" placeholder="" required readonly>
                                    <small class=" BillItemAmt text-danger"></small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" id="imported${randomNumber}" >
                           
                        </div>
                       
                        
                    </div>
                    </div>
    
    `;



        let trimmedRevenueSource = `
    
     

    <div class="row spark p-3 ">
    <div class="col-md-6 stretch">
            <div class="form-group">
                <label class="must" for="">Task</label>
                <select class="form-control task select2bs4" name="Task[]" id="task${randomNumber}" style="width:100%" required  onchange="handleTaskChange(this)">
                <option value="">--Select Task--</option>
                <option ${isSelected('Verification',selectElement)} value="Verification">Verification</option>
                <option ${isSelected('Reverification',selectElement)} value="Reverification">Reverification</option>
                <option ${isSelected('Inspection',selectElement)} value="Inspection">Inspection</option>
                <option ${isSelected('Consultation',selectElement)} value="Consultation">Consultation</option>
                <option ${isSelected('Other',selectElement)} value="Other">Other</option>
                </select>
            </div>
        </div>
        <div class="col-md-6 stretch">
            <div class="form-group">
                <label class="must" for="">Select Revenue Source </label>
                
                <select class="form-control billItemName select2bs4" name="GfsCode[]" id="billItemName${randomNumber}" style="width:100%" onchange="toggleFields(this,'${randomNumber}')">
                <option value="">--Select Revenue Source--</option>
                <option value="142101210007">Pre Package(Imported)</option>
                     <?php foreach (gfsCodes() as $key => $value) : ?>
                      <?php if (!in_array($key, $except)) : ?>
                        <option value="<?= $key ?>"><?= $value ?></option>
                      <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

      

        <input type="text" name="BillItemRef[]" id="" value="<?= randomString() ?>" class="form-control" placeholder="" hidden>

    
   
        <div class="col-md-12 billFigures">
            <div class="row">
                <div class="col-md-4 form-group">
                    <label for="">Amount</label>
                    <input type="text" name="SingleItemAmount[]" id="singleItemAmount${randomNumber}" pattern="[0-9]*" oninput="calcTotal(this)" class="form-control singleItemAmount" placeholder="" required>
                    <small class=" SingleItemAmount text-danger"></small>
                </div>
                <div class="col-md-4 form-group ">
                    <label for="">Quantity</label>
                    <input type="number" min="1" max="100" name="ItemQuantity[]" id="ItemQuantity${randomNumber}" pattern="[0-9]*" oninput="getTotal(this)" class="form-control qty" placeholder="" required>
                    <small class="text-danger"></small>
                </div>
                <div class="col-md-4 form-group">
                    <label for="">Bill Item Amount</label>
                    <input type="text" name="BillItemAmt[]" id="BillItemAmt${randomNumber}" pattern="[0-9]*"  class="form-control itemAmount" placeholder="" required readonly>
                    <small class=" BillItemAmt text-danger"></small>
                </div>
            </div>
        </div>
        <div class="col-md-12" id="imported${randomNumber}" >
            
        </div>
        </div>
       
        


    `





        // Get the grandparent of the select element
        let grandparent = selectElement.parentElement.parentElement.parentElement;


        // Find all elements with the class 'ommit' within the grandparent
        // let omitDivs = grandparent.querySelectorAll('.ommit');

        // Set display based on the selected value
        console.log(grandparent)

        grandparent.innerHTML = (selectElement.value === 'Consultation' || selectElement.value === 'Other') ? trimmedRevenueSource : fullRevenueSource;

        const boxes = document.querySelectorAll('.spark');
        for(let box of boxes) {
            box.classList.remove('p-3');
            box.style.padding = '0px'
            // box.classList.add('p-1');
        }



        $('.select2bs4').select2({
            theme: 'bootstrap4',
        });
        // Get the parent div with class 'billFigures' within the grandparent

        //    let task = grandparent.querySelector('.task') 
        //    task.value = 'Inspection'
        //    console.log(task)
        // Adjust column width based on the selected value

    }






    function getTotal(qty) {
        const quantity = +qty.value
        const parent = qty.parentNode.parentNode
        const amountInput = +parent.children[0].querySelector('.singleItemAmount').value.replace(/,/g, '')
        const itemAmount = parent.children[2].querySelector('.itemAmount')
        itemAmount.value = new Intl.NumberFormat().format(amountInput * quantity)
        calculateTotalAmount()


    }

    function calculateTotalAmount() {
        let total = 0
        const itemAmounts = document.querySelectorAll('.itemAmount')
        for (let amount of itemAmounts) {
            total += Number(amount.value.replace(/,/g, ''))
        }
        document.querySelector('#BillEqvAmt').value = new Intl.NumberFormat().format(total)


    }







    function checkPaymentOpt(opt) {
        const addBtn = document.querySelector('#addBtn')
        if (opt == '2') {
            addBtn.style.display = 'none'
            document.querySelector('#billItems').innerHTML = ''
        } else {
            addBtn.style.display = 'block'

        }
    }

    function changeTransfer(method) {
        const swiftCode = document.querySelector('#swiftCode')
        if (method == 'BankTransfer') {
            swiftCode.removeAttribute('disabled')
        } else {
            swiftCode.setAttribute('disabled', 'disabled')

        }
    }

    function calculateDate(days) {
        const date = new Date();

        date.setDate(date.getDate() + Number(days));
        const expiryDate = `${date.getDate()}-${date.toLocaleString('default', { month: 'long' })}-${date.getFullYear()}`

        document.querySelector('#expiryDate').value = expiryDate
    }





    function addRevenueSource() {

        let randomNumber = Math.floor(10000 + Math.random() * 90000).toString();


        $('#billSubmissionRequest').validate()
        $('#billItemsSource').append(`
        <div class="card">
                    <div class="card-header">
                    REVENUE SOURCE
                        <button type="button" id="addBtn" class="btn btn-dark  btn-sm" onclick="removeItem(this)" style="float:right"><i class="far fa-minus"></i></button>
                    </div>
                    <div class="row p-3">
                    <div class="col-md-3 stretch">
                            <div class="form-group">
                                <label class="must" for="">Task</label>
                                <select class="form-control task select2bs4" name="Task[]" id="task${randomNumber}" style="width:100%" required  onchange="handleTaskChange(this)">
                                    <option value="">--Select Task--</option>
                                    <option value="Verification">Verification</option>
                                    <option value="Reverification">Reverification</option>
                                    <option value="Inspection">Inspection</option>
                                    <option value="Consultation">Consultation</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 stretch">
                            <div class="form-group">
                                <label class="must" for="">Select Revenue Source </label>
                                
                                <select class="form-control billItemName select2bs4" name="GfsCode[]" id="billItemName${randomNumber}" style="width:100%" onchange="toggleFields(this,'${randomNumber}')">
                                <option value="">--Select Revenue Source--</option>
                                <option value="142101210007">Pre Package(Imported)</option>
                                     <?php foreach (gfsCodes() as $key => $value) : ?>
                                      <?php if (!in_array($key, $except)) : ?>
                                        <option value="<?= $key ?>"><?= $value ?></option>
                                      <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3 ommit">
                            <div class="form-group">
                                <label class="must" for="">Item Name</label>
                                <input type="text" name="ItemName[]" id="ItemName${randomNumber}" class="form-control " placeholder="" required>
                                <small class=" ItemName text-danger"></small>
                            </div>
                        </div>

                        <input type="text" name="BillItemRef[]" id="" value="<?= randomString() ?>" class="form-control" placeholder="" hidden>

                        <div class="col-md-3 ommit">
                            <div class="form-group">
                                <label class="must" for="">Capacity/Quantity</label>
                                <input type="number" name="Capacity[]" id="Capacity${randomNumber}" class="form-control " placeholder="" required min="0">
                                <small class="Capacity text-danger"></small>
                            </div>
                        </div>
                        <div class="col-md-3 ommit">
                            <div class="form-group">
                                <label class="must" for="">Unit</label>
                                <select class="form-control" name="ItemUnit[]" id="Unit${randomNumber}">
                                <option value="">--Select Unit--</option>
                                    <optgroup label="Weight">
                                        <option value="mg">mg</option>
                                        <option value="g">g</option>
                                        <option value="kg">kg</option>
                                    </optgroup>
                                    <optgroup label="Volume">
                                        <option value="mL">mL</option>
                                        <option value="L">L</option>
                                        <option value="cubCm">cm <sup>3</sup></option>
                                        <option value="cubM">m <sup>3</sup></option>
                                    </optgroup>
                                    <optgroup label="Area">
                                        <option value="sqCm">cm <sup>2</sup></option>
                                        <option value="sqM">m <sup>2</sup></option>
                                    </optgroup>
                                    <optgroup label="Length">
                                        <option value="mm">mm </option>
                                        <option value="cm">cm </option>
                                        <option value="m">m </option>

                                    </optgroup>
                                    <optgroup label="Count">
                                        <option value="Pieces">Pieces </option>
                                        <option value="Items">Items </option>


                                    </optgroup>
                                    <small class="Unit text-danger"></small>
                                </select>
                            </div>
                        </div>
                       
                        
                        <div class="col-md-3 ommit">
                            <div class="form-group">
                            <label class="must" for="">Status</label>
                                <select class="form-control Status select2bs4" name="Status[]" id="Status${randomNumber}" style="width:100%" required>
                                    <option value="">--Select Status--</option>
                                    <option value="Pass">Pass</option>
                                    <option value="Rejected">Rejected</option>
                                    <option value="Adjusted">Adjusted</option>
                                    <option value="Condemned">Condemned</option>
                                    <option value="None">None</option>
                                </select>
                                <small class=" Status text-danger"></small>
                            </div>
                        </div>
                        <div class="col-md-6 billFigures">
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label for="">Amount</label>
                                    <input type="text" name="SingleItemAmount[]" id="singleItemAmount${randomNumber}" pattern="[0-9]*" oninput="calcTotal(this)" class="form-control singleItemAmount" placeholder="" required>
                                    <small class=" SingleItemAmount text-danger"></small>
                                </div>
                                <div class="col-md-4 form-group ">
                                    <label for="">Quantity</label>
                                    <input type="number" min="1" max="100" name="ItemQuantity[]" id="ItemQuantity${randomNumber}" pattern="[0-9]*" oninput="getTotal(this)" class="form-control qty" placeholder="" required>
                                    <small class="text-danger"></small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label for="">Bill Item Amount</label>
                                    <input type="text" name="BillItemAmt[]" id="BillItemAmt${randomNumber}" pattern="[0-9]*"  class="form-control itemAmount" placeholder="" required readonly>
                                    <small class=" BillItemAmt text-danger"></small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" id="imported${randomNumber}" >
                            
                        </div>
                       
                          
                        
                       



                    </div>
                </div>
        
        `)

        $('.select2bs4').select2({
            theme: 'bootstrap4',
        });


    }

    function removeItem(btn) {
        btn.parentNode.parentNode.remove()
        calculateTotalAmount()
    }

    const spinner = document.querySelector('#spinner')
    const submit = document.querySelector('#submit')






    const billSubmissionRequest = document.querySelector('#billSubmissionRequest')


    $('#billSubmissionRequest').validate({
        rules: {
            'PyrCellNum': {
                required: true,
                minlength: 10
            },
            'days': {
                required: true,
                min: 1,
                max: 30,
                digits: true
            },




        },


        messages: {
            'PyrCellNum': {
                required: 'Please enter  mobile number',
                minlength: 'Mobile number must be  10 characters long'
            },
            'days': {
                // required: 'Please enter number of  days',
                min: 'Enter At least 1 Day',
                max: 'Enter less than 30 Days',

            },


        }
    });





    billSubmissionRequest.addEventListener('submit', (e) => {

        if ($('#billSubmissionRequest').valid()) {
            e.preventDefault()
            // let billItemData = []
            // const items = document.querySelectorAll('.billItemName')
            // for (let item of items) {
            //     billItemData.push(item.options[item.selectedIndex].text)
            // }


            submitInProgress(e.submitter)
            const formData = new FormData(billSubmissionRequest)
            formData.append('csrf_hash', document.querySelector('.token').value)
            // billItemData.forEach(function(item) {
            //     formData.append("itemName[]", item);
            // });



            fetch('billSubmissionRequest', {
                    method: 'POST',
                    headers: {
                        ///'Content-Type': 'application/json;charset=utf-8',
                        "X-Requested-With": "XMLHttpRequest",
                        'X-CSRF-TOKEN': document.querySelector('.token').value
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {

                    console.log(data)
                    const {
                        status,
                        token,
                        msg,
                        TrxStsCode,
                        heading,
                        reconStatusCode,
                        METHOD
                    } = data
                    document.querySelector('.token').value = token
                    if (data.status == 1) {
                        submitDone(e.submitter)
                        billSubmissionRequest.reset()
                        document.querySelector('#heading').textContent = heading
                        printBill(data)

                    } else {
                        submitDone(e.submitter)
                        swal({
                            text: TrxStsCode,
                            title: msg,
                            icon: "warning",
                            // timer: 92500
                        });
                    }






                });
        }
        return false;

    })

    function printBill(billData) {
        const {
            status,
            bill,
            qrCodeObject,
            token,

        } = billData
        // console.log('printing ......')

        const qrCode = new QRCodeStyling({

            width: 200,
            height: 200,
            type: "svg",
            data: JSON.stringify(qrCodeObject),
            image: "<?= base_url('assets/images/emblem.png') ?>",
            dotsOptions: {
                color: "#333333",
                type: "square"
            },
            backgroundOptions: {
                color: "#ffffff",
            },
            imageOptions: {
                crossOrigin: "anonymous",
                margin: 0,
                imageSize: 0.2
            }
        });

        console.log(bill)

        document.querySelector('#billDetails').innerHTML = bill
        document.querySelector('#canvas').innerHTML = ''
        qrCode.append(document.getElementById("canvas"));
        $('#printModal').modal({
            open: true,
            backdrop: 'static'
        })



    }
</script>
<?= $this->endSection(); ?>