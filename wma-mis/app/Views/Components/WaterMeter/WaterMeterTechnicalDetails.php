<button type="button" class="btn btn-primary btn-sm" id="addWaterMeterButton"><i class="far fa-plus"></i> Add</button>
<button type="button" class="btn btn-success btn-sm" onclick="syncMeters()"><i class="far fa-sync"></i> Check</button>

<div class="card mt-3">
    <div class="card-body">
        <div class="form-group">
            <label for="my-input">Total Amount</label>
            <input id="totalAmount" class="form-control" type="text" name="">
        </div>

    </div>
</div>



<!-- <div class="customerMeters"></div> -->


<div class="modal WaterMeter-modal fade" id="add-WaterMeter">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Meters</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="customerForm">
                <div class="modal-body">
                    <div class="form-group">
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="">Date</label>
                            <input id="createdAt" class="form-control clearIt" type="date" data-clear>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="">Activity</label>
                            <select class="form-control" name="" id="activity">
                                <!-- <option disabled selected>-Select Activity-</option> -->
                                <option value="Verification">Verification</option>
                                <option value="Reverification">Reverification</option>
                                <option value="Inspection">Inspection</option>
                            </select>
                        </div>
                    </div>




                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Meter Size</label>
                            <input type="number" class="form-control clearIt" id="meterSize" placeholder="Enter Meter Size" data-clear>


                        </div>
                        <div class="form-group col-md-6">
                            <label>Brand Name </label>
                            <input type="text" class="form-control clearIt" id="brandName" placeholder="Enter Brand Name" data-clear>


                        </div>
                    </div>

                    <div class="row">

                        <div class="form-group col-md-6">
                            <label>Quantity</label>
                            <input type="number" class="form-control clearIt" id="quantity" placeholder="Enter Quantity" data-clear>

                        </div>
                        <div class="form-group col-md-6">
                            <label>Flow Rate</label>
                            <input type="number" class="form-control clearIt" id="flowRate" placeholder="Enter Flow Rate" data-clear>

                        </div>

                    </div>
                    <div class="row">

                        <div class="form-group col-md-6">
                            <label>Initial Seal Number</label>
                            <input type="text" class="form-control clearIt" id="initialSeal" placeholder="Initial Seal Number" data-clear>

                        </div>
                        <div class="form-group col-md-6">
                            <label>Final Seal Number</label>
                            <input type="text" class="form-control clearIt" id="finalSeal" placeholder="Final Seal Number" data-clear>

                        </div>

                    </div>


                    <div class="row">

                        <div class="form-group col-md-6">
                            <label>Class</label>
                            <input type="text" class="form-control clearIt" id="class" placeholder="Enter Class" data-clear>

                        </div>
                        <div class="form-group col-md-6">
                            <label>Testing Laboratory</label>
                            <input type="text" class="form-control clearIt" id="lab" placeholder="Enter Testing Laboratory" data-clear>

                        </div>

                    </div>
                    <div class="form-group col-md-12">
                        <label>Testing Method</label>
                        <input type="text" class="form-control clearIt" id="method" placeholder="Enter Testing Method" data-clear>

                    </div>

                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="">Status</label>
                            <select class="form-control" id="status">
                                <option value="Pass"> Pass</option>
                                <option value="Rejected"> Rejected</option>

                            </select>
                        </div>

                        <div class="form-group col-md-12">
                            <label>Other Charges</label>
                            <input type="number" class="form-control clearIt" id="charges" placeholder="Other Charges" data-clear>

                        </div>
                    </div>
                    <div class="form-group">
                        <label for="my-textarea">Remark</label>
                        <textarea id="remark" class="form-control clearIt" name="" rows="3" data-clear></textarea>
                    </div>
                </div>
            </form>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btn-sm" id="save-WaterMeter">Save</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- Search customer -->

<script>
    //=================Publish WaterMeter data to transaction table with meter id customer hash and control number====================
    function publishWaterMeterData() {
        const meterIds = document.querySelectorAll('.waterMeterId')
        const hash = document.querySelector('#customerId')
        const totalAmount = document.querySelector('#totalAmount')
        const payment = document.querySelector('#payment')
        const controlNumber = document.querySelector('#controlNumber')

        let waterMeterIds = []

        for (let id of meterIds) {
            waterMeterIds.push(id.value)
        }


        $.ajax({
            type: "POST",
            url: "publishWaterMeterData",
            data: {
                meterId: waterMeterIds,
                customerHash: hash.value,
                controlNumber: controlNumber.value,
                totalAmount: totalAmount.value,
                payment: payment.value

            },
            dataType: "json",
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $('.token').val());
            },
            success: function(response) {
                console.log(response);
                document.querySelector('.token').value = response.token
                if (response.status == 1) {
                    $('#customerMeters').html('')
                    controlNumber.value = ''
                    totalAmount.value = ''
                    printBill(response.bill)
                    swal({
                        title: 'WaterMeter  Registered',
                        icon: "success",
                        timer: 4500
                    });
                    // setTimeout(() => {
                    //     location.reload()
                    // }, "2000")
                } else {


                    swal({
                        title: 'Something Went Wrong',
                        // text: "You clicked the button!",
                        icon: "warning",
                        // timer: 2500
                    });
                }
            }
        });


    }


    function printBill(bill) {
        // console.log('printing ......')
        console.log(bill)
        $('#printModal').modal({
            open: true,
            backdrop: 'static'
        })
        $('#billCustomer').html( /*html*/ `
            
            <tr>
               <td>Control Number:</td>
              <td><b>${bill.controlNumber}</b></td>
              </tr>
            <tr>
                <td>Payment Ref:</td>
                <td><b>${bill.paymentRef}</b></td>
            </tr>
            <tr>
                <td>Payer:</td>
                <td>${bill.payer.name}</td>
            </tr>
            <tr>
                <td>Payer Phone:</td>
                <td>${bill.payer.phone_number}</td>
            </tr>
            `)
        let sn = 1
        const items = bill.products.map(item => `
            <tr>
             <td>${sn++}</td>
             <td>${item.product}</td>
             <td>${item.amount}</td>
            </tr>
            
            `)
        $('#billItems').html(items)
        $('#billTotal').html(bill.billTotal)
        $('#billTotalInWords').html(bill.billTotalInWords)
        $('#preparedBy').html(bill.createdBy)
        $('#printedBy').html(bill.printedBy)
        $('#printedOn').html(bill.printedOn)

        const refs = document.querySelectorAll('.ref')
        refs.forEach(r => r.textContent = bill.controlNumber)
    }
    //*************************************************************************** */

    //=================check if there is any customer waterMeter which is not in the transaction table====================
    function syncMeters() {

        $('#customerMeters').html('')
        const totalAmount = document.querySelector('#totalAmount')
        let total = 0
        const hashString = $('#customerId')
        // console.log('Hello world')
        // console.log(hashString)

        if (hashString) {
            $.ajax({
                type: "POST",
                url: "getUnpaidWaterMeters",
                data: {
                    hashString: hashString
                },
                dataType: "json",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', $('.token').val());
                },
                success: function(meters) {



                    // console.log(meters)
                    document.querySelector('.token').value = meters.token

                    if (meters.msg == 'empty') {
                        swal({
                            title: 'No Data Found',
                            // text: "You clicked the button!",
                            icon: "warning",
                            timer: 2500
                        });

                    } else {
                        for (let meter of meters.data) {


                            $('#customerMeters').append(
                                `
                        <li  class="list-group-item" >Brand : ${meter.brand} | Size: ${meter.meter_size}  | Quantity: ${meter.quantity} | Flow Rate: ${meter.flow_rate} | Class: ${meter.class}
                        <i data-remove="remove" class="fas fa-times-square" id="delete-btn"></i>
                        <input type='text' value='${meter.id}' class='waterMeterId'  hidden>
                        <input type='text' value='${meter.amount}' class='waterMeterAmountFigure' hidden >
                        </li>`
                            );

                            total += parseInt(meter.amount)
                        }
                    }


                    totalAmount.value = total
                }
            });
        } else {
            swal({
                title: 'Please Select Customer First!',
                // text: "You clicked the button!",
                icon: "warning",
                timer: 2500
            });
        }


    }
    //*********************************************************** */


    const resultContainer = document.querySelector('#customerMeters')
    resultContainer.addEventListener('click', (e) => {

        if (e.target.hasAttribute('data-remove', 'remove')) {

            const li = e.target.parentElement;

            resultContainer.removeChild(li)

        }
    })

  

    //=================Taking all waterMeter details and store to waterMeter table====================
    function WaterMeterProcessing() {
     

        const saveWaterMeter = document.querySelector('#save-WaterMeter')


        saveWaterMeter.addEventListener('click', () => {
            const WaterMeterCustomerHash = $('#customerId')
            const createdAt = $('#createdAt')
            const activity = $('#activity')
            const meterSize = $('#meterSize')
            const brandName = $('#brandName')
            const quantity = $('#quantity')
            const flowRate = $('#flowRate')
            const meterClass = $('#class')
            const testingLab = $('#lab')
            const testingMethod = $('#method')
            const initialSeal = $('#initialSeal')
            const finalSeal = $('#finalSeal')
            const status = $('#status')
            const charges = $('#charges')
            const remark = $('#remark')



            function validateInput(formInput) {

                if (formInput.val() == '') {
                    formInput.css('border', '1px solid #ff6348')
                    return false
                } else {
                    formInput.css('border', '1px solid #2ed573')
                    return true
                }

            }



            if (validateInput(createdAt) && validateInput(activity) && validateInput(meterSize) && validateInput(
                    brandName) && validateInput(flowRate) && validateInput(quantity) &&
                validateInput(meterClass) && validateInput(testingLab) && validateInput(testingMethod) &&
                validateInput(status)) {


                $.ajax({
                    type: "POST",
                    url: "registerWaterMeter",
                    data: {
                        WaterMeterCustomerHash: WaterMeterCustomerHash.val(),
                        createdAt: createdAt.val(),
                        activity: activity.val(),
                        meterSize: meterSize.val(),
                        brandName: brandName.val(),
                        quantity: quantity.val(),
                        flowRate: flowRate.val(),
                        meterClass: meterClass.val(),
                        testingLab: testingLab.val(),
                        testingMethod: testingMethod.val(),
                        initialSeal: initialSeal.val(),
                        finalSeal: finalSeal.val(),
                        status: status.val(),
                        charges: charges.val(),
                        remark: remark.val(),




                    },
                    dataType: "json",
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('.token').val());
                    },
                    success: function(response) {

                        document.querySelector('.token').value = response.token
                        console.log(response)

                        if (response.status == 1) {
                            clearInputs()
                            $('.WaterMeter-modal').modal('hide');
                            swal({
                                title: 'WaterMeter Added',
                                // text: "You clicked the button!",
                                icon: "success",
                                button: "Ok",
                            });

                            syncMeters()
                        } else {
                            swal({
                                title: 'Something Went Wrong!',
                                // text: "You clicked the button!",
                                icon: "error",
                                button: "Ok",
                            });
                        }


                    },
                    error: function(err) {
                        console.log(err)
                    }

                });
                //************************************** */


                //=================take last   saved meter from the database  ====================
                function grabLastmeter() {
                    $.ajax({
                        type: "GET",
                        url: "grabLastmeter",

                        dataType: "json",
                        success: function(response) {
                            // alert('Working')

                            $('#customerMeters').append(
                                `
                        <li onclick="showId('${response.id}')" class="list-group-item" >${response.meter_brand} | ${response.capacity} Liters | Driver - ${response.driver_name}</li>`
                            );

                        }
                    });
                }

                //*********************************************************** */

                function showId(id) {
                    alert('The id is' + id)
                }



            }

        })
    }

    WaterMeterProcessing()
</script>