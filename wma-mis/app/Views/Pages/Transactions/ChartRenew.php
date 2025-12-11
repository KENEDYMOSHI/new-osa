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

                BILL CREATION (VERIFICATION CHART RENEW)
            </div>
            <div class="card-body">


                <div class="row">


                    <div class="col-md-3">
                        <input type="text" name="chartNumber" id="chartNumber" value="<?= $chart ?>" class="form-control" required hidden readonly>
                        <div class="form-group">
                            <label class="must" for="">Payer Name</label>
                            <input type="text" name="PyrName" id="PyrName" value="<?= $customer->name ?>" class="form-control" required readonly>
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
                            <input type="email" name="PyrEmail" id="PyrEmail" value="<?= $customer->email ?>" class="form-control">
                            <span class="PyrEmail text-danger" data-error></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <?php $mobile = str_replace('255', '0', $customer->phone_number) ?>
                        <div class="form-group">
                            <label class="must" for="">Phone Number </label>
                            <input type="text" name="PyrCellNum" id="PyrCellNum" class="form-control " required oninput=" this.value = this.value.replace(/\D/g, '')" maxlength="10" value="<?= $mobile ?>" readonly>
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
                            <select class="form-control" name="BillPayOpt" id="BillPayOpt" required>
                                <!-- <option value="1">Full</option> -->
                                <option value="">--Select Payment Option--</option>
                                <option selected value="3">Exact</option>
                                <!-- <option value="2">Partial</option> -->
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


        <div class="card1" id="">
            <!-- <div class="card-header1">REVENUE SOURCE </div> -->
            <div class="card-body1">

                <div class="card">
                    <div class="card-header">
                        REVENUE SOURCE
                        <!-- <button type="button" id="addBtn" class="btn btn-primary  btn-sm" onclick="addRevenueSource()" style="float:right"><i class="far fa-plus"></i></button> -->
                    </div>
                    <div class="row p-3">

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="must" for="">Select Revenue Source </label>
                                <select class="form-control billItemName select2bs4" name="GfsCode[]" id="" style="width:100%">
                                    <option value="140202">Miscellaneous</option>
                                    <!-- <?php foreach (gfsCodesBill() as $key => $value) : ?>
                                        <option value="<?= $key ?>"><?= $value ?></option>

                                    <?php endforeach; ?> -->

                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="must" for="">Item Name</label>
                                <input type="text" name="ItemName[]" id="ItemName" class="form-control " placeholder="" required value="Chart No. <?= $chart ?>">
                                <small class=" ItemName text-danger"></small>
                            </div>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="">Amount</label>
                            <input type="text" name="BillItemAmt[]" id="BillItemAmt" pattern="[0-9]*" oninput="calcTotal(this)" class="form-control singleItemAmount" placeholder="" required>
                            <small class=" SingleItemAmount text-danger"></small>
                        </div>
                       

                        <input type="text" name="BillItemRef[]" id="" value="<?= randomString() ?>" class="form-control" placeholder="" hidden value="<?= $chart ?>">





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

    function calcTotal(amountInput) {
        amountInput.value = amountInput.value.replace(/\D/g, '').replace(/,/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ',')
        const parent = amountInput.parentNode.parentNode
        const quantityInput = 1
        const itemAmount = document.querySelector('.itemAmount')
        let total = new Intl.NumberFormat().format(quantityInput * amountInput.value.replace(/,/g, ''))
        // itemAmount.value = new Intl.NumberFormat().format(quantityInput * amountInput.value.replace(/,/g, ''))
        document.querySelector('#BillEqvAmt').value = total


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
            // 'Capacity': {
            //     required: true,

            // },
            // 'StickerNumber[]': {
            //     required: true,

            // },
            // 'BillItemAmt': {
            //     required: true,

            // },



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
            let billItemData = []
            const items = document.querySelectorAll('.billItemName')
            for (let item of items) {
                billItemData.push(item.options[item.selectedIndex].text)
            }


            submitInProgress(e.submitter)
            const formData = new FormData(billSubmissionRequest)
            formData.append('csrf_hash', document.querySelector('.token').value)
            billItemData.forEach(function(item) {
                formData.append("itemName[]", item);
            });



            fetch('<?= base_url() ?>' + 'billSubmissionRequest', {
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