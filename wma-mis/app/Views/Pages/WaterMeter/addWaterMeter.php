<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
<?php
$pageSession = \CodeIgniter\Config\Services::session();
?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <div class="container">
                    <h1 class="m-0 text-dark"><?= $page['heading'] ?></h1>
                </div>
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
<!-- /.content-header -->

<!-- Main content -->
<section class="content body">
    <div class="container-fluid">
        <?= view('Components/bill') ?>
        <?= view('Components/ClientsBlock') ?>


        <div class="">
            <div class="card">
                <div class="card-header">

                    <div class="row">
                        <div class="col-md-6 flex">
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" onclick="openMeterModal()"><i class="far fa-plus"></i> Add Meter</button>
                            <button type="button" class="btn btn-success btn-sm" onclick="getWaterMeters()"><i class="far fa-list"></i>
                                List Meters</button>

                        </div>


                    </div>
                    <!-- /.card-tools -->
                </div>
                <div class="card-body">
                    <form id="waterMeterBillForm">


                        <div id="unpaidMeters"></div>


                        <div class="form-group">
                            <label for="">Total Amount</label>
                            <input type="text" name="totalAmount" id="totalAmount" class="form-control" readonly>

                        </div>



                </div>



            </div>
            <div class="card" id="billBlock" style="display: none;">

                <div class="card-header">BILL DETAILS</div>
                <div class="card-body">
                    <?= view('Components/billOptions') ?>
                </div>
                <div class="card-footer">
                    <button type="submit" id="submit" class="btn btn-primary btn-sm ">
                        <div class="spinner-border spinner-border-sm" id="spinner" role="status" style="display: none;"></div>
                        Submit
                    </button>
                </div>
                </form>
            </div>


        </div>
        <!-- /.card -->

    </div>
    </div>

    <!-- Button trigger modal -->


    <!-- Modal -->
    <div class="modal fade" id="extraMeters" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Extra Meters</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="extraMeterForm">
                        <input class="form-control" type="text" name="batchId" id="batchIdField" hidden>
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label for="">Meter Brand</label>
                                        <input type="text" class="form-control" name="" id="meterBrand"  readonly>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="">Flow Rate</label>
                                        <input type="text" class="form-control" name="" id="meterFlowRate"  readonly>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="">Class</label>
                                        <input type="text" class="form-control" name="" id="meterClass"  readonly>
                                    </div>
                                    <div class="form-group col-md-4">


                                        <label for="">Actual Volume</label>
                                        <select class="form-control" name="actualVolume" id="actualVolume-second">
                                            <option value="50">50 Liters</option>
                                            <option value="100">100 Liters</option>
                                            <option value="200">200 Liters</option>
                                            <option value="300">300 Liters</option>
                                            <option value="400">400 Liters</option>
                                            <option value="500">500 Liters</option>

                                        </select>

                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="">Meter Quantity</label>
                                        <input type="number" name="quantity" id="quantity-second" class="form-control" placeholder="Number Of Meters">
                                    </div>
                                    <div class="form-group col-md-4">

                                        <button type="button" class="btn btn-primary  " style="margin-top:1.8rem ;" onclick="generateFields('second')"><i class="far fa-sync"></i> Generate Fields</button>
                                    </div>
                                </div>

                            </div>
                            <div class="card-body">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Meter Serial No</th>
                                            <th>Initial Reading</th>
                                            <th>Final Reading</th>
                                            <th>Indicated Volume Vi (L)</th>
                                            <th>Actual Volume Va (L)</th>
                                            <th>% Error</th>
                                            <th>Decision</th>
                                            <!-- <th>Seal No/Rej No</th> -->
                                            <th><button type="button" class="btn btn-primary btn-sm" onclick="addField('second')"><i class="fas fa-plus"></i></button></th>
                                        </tr>
                                    </thead>
                                    <tbody id="fields-second">


                                    </tbody>
                                </table>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <div class="spinner-border spinner-border-sm" id="spinner" role="status" style="display: none;"></div>
                        Save
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>





    <!-- Modal -->
    <div class="modal fade" id="waterMeterModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Meters</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="waterMeterForm">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="must" for="">Category</label>
                                            <select class="form-control" name="category" id="category" required>
                                                <option value="">--Select Category </option>
                                                <option value="Domestic">Domestic</option>
                                                <option value="Industrial">Industrial</option>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="must" for="">Activity</label>
                                            <select class="form-control" name="task" id="task" required>
                                                <option value="">--Select Activity </option>
                                                <option value="Verification">Verification</option>
                                                <option value="Inspection">Inspection</option>
                                                <option value="Reverification">Reverification</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="must" for="">Brand Name</label>
                                            <input type="text" name="brandName" id="brandName" class="form-control" placeholder="Brand Name" required>

                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="must" for="">Meter Size</label>
                                            <select class="form-control" name="meterSize" id="meterSize" required>
                                                <option value="">-Select Meter Size</option>
                                                <option value="DN15">DN15</option>
                                                <option value="DN20">DN20</option>
                                                <option value="DN25">DN25</option>
                                                <option value="DN32">DN32</option>
                                                <option value="DN40">DN40</option>
                                                <option value="DN50">DN50</option>
                                                <option value="DN65">DN65</option>
                                                <option value="DN80">DN80</option>
                                                <option value="DN100">DN100</option>
                                                <option value="DN120">DN120</option>
                                                <option value="DN150">DN150</option>
                                                <option value="DN200">DN200</option>
                                                <option value="DN250">DN250</option>
                                                <option value="DN300">DN300</option>
                                                <option value="DN300">DN300</option>

                                            </select>

                                        </div>
                                    </div>
                                    <div class="col-md-3">

                                        <div class="form-group">
                                            <label for="">Flow Rate</label>
                                            <select class="form-control" name="flowRate" id="flowRate" required>
                                                <option value="">-Select Flow Rate</option>
                                                <option value="Q1">Q1</option>
                                                <option value="Q2">Q2</option>
                                                <option value="Q3">Q3</option>
                                                <option value="Q4">Q4</option>

                                            </select>
                                        </div>

                                    </div>
                                    <div class="col-md-3">

                                        <div class="form-group">
                                            <label for="">Flow Rate At (m<sup>3</sup>/s)</label>

                                            <input type="text" name="rate" id="class" class="form-control" placeholder="Flow rate at" required>


                                        </div>

                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="must" for="">Class</label>
                                            <input type="text" name="class" id="class" class="form-control" placeholder="Class" required>

                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="must" for="">Testing Center</label>
                                            <input type="text" name="testingLab" id="testingLab" class="form-control" placeholder="Testing Center" required>

                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="must" for="">Test Method</label>
                                            <select class="form-control" name="testMethod" id="testMethod">
                                                <option value="Volumetric">Volumetric</option>
                                                <option value="Gravimetric">Gravimetric</option>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="must" for="">Verified By</label>
                                            <input type="text" name="verifier" id="verifier" value="<?= $user->username ?>" class="form-control" placeholder="Verified By" required readonly>

                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="form-group col-md-4">


                                        <label for="">Actual Volume</label>
                                        <select class="form-control" name="actualVolume" id="actualVolume-first">
                                            <option value="50">50 Liters</option>
                                            <option value="100">100 Liters</option>
                                            <option value="200">200 Liters</option>
                                            <option value="300">300 Liters</option>
                                            <option value="400">400 Liters</option>
                                            <option value="500">500 Liters</option>
                                            <option value="600">600 Liters</option>
                                            <option value="700">700 Liters</option>
                                            <option value="800">800 Liters</option>
                                            <option value="900">900 Liters</option>
                                            <option value="1000">1000 Liters</option>

                                        </select>

                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="">Meter Quantity</label>
                                        <input type="number" name="quantity" id="quantity-first" class="form-control" placeholder="Number Of Meters">
                                    </div>
                                    <div class="form-group col-md-4">

                                        <button type="button" class="btn btn-primary  " style="margin-top:1.8rem ;" onclick="generateFields('first')"><i class="far fa-sync"></i> Generate Fields</button>
                                    </div>
                                </div>

                            </div>
                            <div class="card-body">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Meter Serial No</th>
                                            <th>Initial Reading</th>
                                            <th>Final Reading</th>
                                            <th>Indicated Volume Vi (L)</th>
                                            <th>Actual Volume Va (L)</th>
                                            <th>% Error</th>
                                            <th>Decision</th>
                                            <!-- <th>Seal No/Rej No</th> -->
                                            <th><button type="button" class="btn btn-primary btn-sm" onclick="addField('first')"><i class="fas fa-plus"></i></button></th>
                                        </tr>
                                    </thead>
                                    <tbody id="fields-first">


                                    </tbody>
                                </table>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <div class="spinner-border spinner-border-sm" id="spinner" role="status" style="display: none;"></div>
                        Save
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>

</section>
<script>
    function openMeterModal() {
        const Hash = document.querySelector('#customerId')

        if (Hash) {
            $('#waterMeterModal').modal({
                show: true,
                focus: true,
                backdrop: 'static'

            })
        } else {
            swal({
                title: 'Please Select Customer First',
                icon: "warning",
                // timer: 2500
            });
        }
    }



    $('#waterMeterForm').validate()
    const extraMeterForm = document.querySelector('#extraMeterForm')
    extraMeterForm.addEventListener('submit', (e) => {
        e.preventDefault()
        if ($('#extraMeterForm').valid()) {
            submitInProgress(e.submitter)
            const formData = new FormData(extraMeterForm)
            formData.append('customerId', document.querySelector('#customerId').value)
            fetch('addExtraWaterMeters', {
                    method: 'POST',
                    headers: {
                        // 'Content-Type': 'application/json;charset=utf-8',
                        "X-Requested-With": "XMLHttpRequest",
                        'X-CSRF-TOKEN': document.querySelector('.token').value
                    },

                    body: formData,

                }).then(response => response.json())
                .then(data => {
                    const {
                        status,
                        token,
                        msg,
                        meters
                    } = data
                    document.querySelector('.token').value = token
                    submitDone(e.submitter)
                    if (status == 1) {
                        getWaterMeters()
                        extraMeterForm.reset()
                        $('#extraMeters').modal('hide')
                        swal({
                            title: msg,
                            icon: "success",

                        });
                    } else {
                        submitDone(e.submitter)
                        swal({
                            title: msg,
                            icon: "warning",
                            timer: 6500
                        });
                    }
                   // console.log(data)
                })
        } else {
            return false
        }


    })



























    $('#waterMeterForm').validate()
    const waterMeterForm = document.querySelector('#waterMeterForm')
    waterMeterForm.addEventListener('submit', (e) => {
        e.preventDefault()
        if ($('#waterMeterForm').valid()) {
            submitInProgress(e.submitter)
            const formData = new FormData(waterMeterForm)
            formData.append('customerId', document.querySelector('#customerId').value)
            fetch('registerWaterMeter', {
                    method: 'POST',
                    headers: {
                        // 'Content-Type': 'application/json;charset=utf-8',
                        "X-Requested-With": "XMLHttpRequest",
                        'X-CSRF-TOKEN': document.querySelector('.token').value
                    },

                    body: formData,

                }).then(response => response.json())
                .then(data => {
                    const {
                        status,
                        token,
                        msg,
                        meters
                    } = data
                    document.querySelector('.token').value = token
                    submitDone(e.submitter)
                    if (status == 1) {
                        getWaterMeters()
                        waterMeterForm.reset()
                        $('#waterMeterModal').modal('hide')
                        swal({
                            title: msg,
                            icon: "success",

                        });
                    } else {
                        submitDone(e.submitter)
                        swal({
                            title: msg,
                            icon: "warning",
                            timer: 6500
                        });
                    }
                  //  console.log(data)
                })
        } else {
            return false
        }


    })



    function getWaterMeters() {
        const clientId = document.querySelector('#customerId')
        if (clientId) {
            fetch('getUnpaidWaterMeters', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json;charset=utf-8',
                        "X-Requested-With": "XMLHttpRequest",
                        'X-CSRF-TOKEN': document.querySelector('.token').value
                    },

                    body: JSON.stringify({
                        customerId: document.querySelector('#customerId').value,
                    }),

                }).then(response => response.json())
                .then(data => {
                    const {
                        status,
                        token,
                        meters
                    } = data
                    // if (status == 1) {
                    // }
                    document.querySelector('.token').value = token
                    document.querySelector('#unpaidMeters').innerHTML = meters
                    $('[data-toggle="tooltip"]').tooltip()
                    calculateTotalAmount()
                })
        } else {
            swal({
                title: 'Please Select Customer First',
                icon: "warning",
                // timer: 2500
            });
        }

    }


    function meterFields(actualVolume, id) {
        return `
             
             <td> <input type="text" name="serialNumber[]" id="sr${id}" class="form-control" required></td>
             <td> <input type="number" name="initialReading[]" id="ini${id}" oninput="calculateError(this)"  step="any" class="form-control" required></td>
             <td> <input type="number" name="finalReading[]" id="fin${id}" oninput="calculateError(this)"  step="any" class="form-control" required></td>
             <td> <input type="text" name="indicatedVolume[]" id="ind${id}" step="any" class="form-control" required readonly></td>
             <td> <input type="number" name="actualVolume[]" id="act${id}" value="${actualVolume}" class="form-control" readonly required></td>
             <td> <input type="text" name="error[]" id="err${id}" class="form-control" required readonly></td>
             <td> <input type="text" name="decision[]" id="dec${id}" class="form-control" required readonly></td>
             <td> <input type="text" name="tag[]" id="tg${id}" class="form-control" placeholder="" required ></td>
             <td> <button type="button" class="btn btn-dark btn-sm" onclick="javascript: this.parentNode.parentNode.remove()"><i class="fas fa-ban"></i></button></td>
        

        `
    }




    function generateFields(position) {
       // console.log(position)
        const actualVolume = document.querySelector(`#actualVolume-${position}`).value
        const quantity = document.querySelector(`#quantity-${position}`).value
        let fields = ''
        for (let index = 0; index < quantity; index++) {
            let id = Math.floor(10000 + Math.random() * 90000).toString();
            fields += `<tr>${meterFields(actualVolume,id)}</tr>`

        }

      //  console.log(fields)


        document.querySelector(`#fields-${position}`).innerHTML = fields



    }

    function addField(position) {
        //(position)
        const actualVolume = document.querySelector(`#actualVolume-${position}`).value
        let id = Math.floor(10000 + Math.random() * 90000).toString();
        $(`#fields-${position}`).append(` <tr>${meterFields(actualVolume,id)}</tr>`)
    }

    function addMeters(batchId, meterBrand, meterFlowRate, meterClass,actualVolume) {
        document.querySelector('#batchIdField').value = batchId
        document.querySelector('#meterBrand').value = meterBrand
        document.querySelector('#meterFlowRate').value = meterFlowRate
        document.querySelector('#meterClass').value = meterClass
        document.querySelector('#actualVolume-second').value = actualVolume
        $('#extraMeters').modal('show')
    }

    function roundNumber(num) {
        return +(Math.round(num + "e+3") + "e-3");
    }


    function calculateError(input) {
        let Qn
        const flowRate = document.querySelector('#flowRate').value
        flowRate == 'Q1' || flowRate == 'Q2' ? Qn = 5 : Qn = 2


        const parent = input.parentNode.parentNode
        const entered = input.value
        const initialReading = parent.children[1].children[0].value
        const finalReading = parent.children[2].children[0].value
        const indicatedVolume = parent.children[3].children[0]
        const actualVolume = parent.children[4].children[0].value
        const error = parent.children[5].children[0]
        const decision = parent.children[6].children[0]
        const tag = parent.children[7].children[0]
        if (initialReading != '' && finalReading != '') {
            indicatedVolume.value = roundNumber(Number(finalReading - initialReading))
            const errorPercentage = roundNumber(((Number(indicatedVolume.value) - Number(actualVolume)) / Number(actualVolume)) * 100)
            error.value = errorPercentage
            let status

            if (errorPercentage >= -Qn && errorPercentage <= Qn) {
                status = 'PASS'
                tag.setAttribute('placeholder', 'Seal No')
            } else {
                tag.setAttribute('placeholder', 'Rejection No')
                status = 'FAIL'

            }
            decision.value = status

            console.table({
                indicatedVolume: indicatedVolume.value,
                actualVolume: actualVolume,
                errorPercentage: errorPercentage
            })

        }


        // const netQuantity = parent.children[2].children[0]
        // const comment = parent.children[3].children[0]
        // const status = parent.children[4].children[0]
    }




    function calculateTotalAmount() {
        const itemAmount = document.querySelectorAll('.itemAmount')
        const billBlock = document.querySelector('#billBlock')
        let total = 0
        for (amount of itemAmount) {
            total += Number(amount.value)
        }
        document.querySelector('#totalAmount').value = total

        if (total > 0) {
            billBlock.style.display = 'block'
        } else {
            billBlock.style.display = 'none'

        }

    }

    function clearRow(id) {
    // Get the data-id attribute
    let domId = $('#' + id).data('id');
    
    // Remove the tooltip for the clicked button
    $('[data-id="' + domId + '"]').tooltip('dispose');

    // Remove the element
    $('#' + id).remove();

    // Calculate the total amount
    let total = 0;
    const amounts = document.querySelectorAll('.itemAmount');
    for (const amount of amounts) {
        total += Number(amount.value);
    }

    // Update the total amount
    document.querySelector('#totalAmount').value = total;
}



    function loading() {
        spinner.style.display = 'inline-block'
        submit.classList.add('disabled')

    }

    //remove loading animation
    function done() {

        spinner.style.display = 'none'
        submit.classList.remove('disabled')
    }

    const waterMeterBillForm = document.querySelector('#waterMeterBillForm')
    waterMeterBillForm.addEventListener('submit', (e) => {
        e.preventDefault()
        submitInProgress(e.submitter)
        const formData = new FormData(waterMeterBillForm)
        formData.append('csrf_hash', document.querySelector('.token').value)
        formData.append('customerId', document.querySelector('#customerId').value)
        fetch('publishWaterMeterData', {
                method: 'POST',
                headers: {
                    // 'Content-Type': 'application/json;charset=utf-8',
                    "X-Requested-With": "XMLHttpRequest",
                    'X-CSRF-TOKEN': document.querySelector('.token').value
                },

                body: formData,

            }).then(response => response.json())
            .then(data => {
                // }
                const {
                    status,
                    token,
                    msg,

                } = data
                document.querySelector('.token').value = token

                submitDone(e.submitter)
                if (status == 1) {
                    getWaterMeters()
                    printBill(data)

                }else{
                    swal({
                        title: msg,
                        icon: "warning",
                    });
                }

                // if (status == 1) {
                //     swal({
                //         title: msg,
                //         // text: "You clicked the button!",
                //         icon: "success",
                //         // timer: 3500
                //     });
                //     controlNumber.value = ''
                //     setTimeout(() => {
                //         // location.reload()
                //     }, "2000")

                //     printBill(bill)
                //     document.querySelector('#unpaidMeters').innerHTML = ''
                //     document.querySelector('#totalAmount').value = ''
                // } else {
                //     swal({
                //         title: msg,
                //         // text: "You clicked the button!",
                //         icon: "warning",
                //         // timer: 2500
                //     });
                console.log(data)
            })
    })




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
        const expiryDate = `${date.getDate()}-${date.toLocaleString('default',{month: 'long' })} - ${date.getFullYear()}`

        document.querySelector('#expiryDate').value = expiryDate
    }


    function printBill(billData) {
        const {
            status,
            bill,
            heading,
            qrCodeObject,
            token,

        } = billData
        // console.log(qrCodeObject)
        // console.log(heading)
        // console.log(token)

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

        //console.log(bill)

        document.querySelector('#heading').textContent = heading
        document.querySelector('#billDetails').innerHTML = ''
        document.querySelector('#billDetails').innerHTML = bill
        qrCode.append(document.getElementById("canvas"));
        $('#printModal').modal({
            open: true,
            backdrop: 'static'
        })



    }
</script>

<?= $this->endSection(); ?>