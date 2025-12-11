<div class="modal edit-sbl-modal fade" id="edit-sbl">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Updating Vehicle</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- <form id="customerForm"> -->
            <div class="modal-body">
                <div class="form-group">
                    <input id="customerHash" class="form-control" type="text" hidden>
                </div>
                <input type="text" id="theId" hidden>
                <div class="row">
                    <!-- <div class="form-group col-md-6">
                            <label for="">Date</label>
                            <input id="createdAt" class="form-control" type="date">
                        </div> -->
                    <div class="form-group col-md-12">
                        <label for="">Activity</label>
                        <select class="form-control" name="" id="activityUpdate">
                            <!-- <option disabled selected>-Select Activity-</option> -->
                            <option value="On Verification">On Verification</option>
                            <option value="Reverification">Reverification</option>
                            <option value="Inspection">Inspection</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Supervisor Name</label>
                        <input type="text" class="form-control" id="supervisorUpdate" placeholder="Enter Supervisor">


                    </div>
                    <div class="form-group col-md-6">
                        <label>Phone Number</label>
                        <input type="text" class="form-control phone" id="supervisorPhoneUpdate" placeholder="Enter owner or company">


                    </div>
                </div>
                <div class="form-group">
                    <label>Tin Number </label>
                    <input type="text" class="form-control tin" id="tinNumberUpdate" placeholder="Enter Tin Number">


                </div>
                <div class="row">


                    <div class="form-group col-md-6">
                        <label for="my-input">Driver's Full Name</label>

                        <div class="input-group">
                            <input class="form-control" id="driverNameUpdate" type="text" placeholder=" Enter Driver's full Name" value="">

                        </div>

                    </div>
                    <div class="form-group col-md-6">
                        <label for="my-input">Driver's License</label>

                        <div class="input-group">
                            <input class="form-control license" id="driverLicenseUpdate" type="text" placeholder=" Enter Driver's License" value="">

                        </div>


                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Vehicle Brand</label>
                        <input type="text" class="form-control" id="vehicleBrandUpdate" placeholder="Enter Brand">


                    </div>
                    <div class="form-group col-md-6">
                        <label>Vehicle Plate Number </label>
                        <input type="text" class="form-control" id="licensePlateUpdate" placeholder="Enter Plate Number" oninput="this.value = this.value.toUpperCase().replaceAll(/\s/g,'')">

                    </div>
                </div>

                <div class="row">

                    <div class="form-group col-md-6">
                        <label>Tank Capacity</label>
                        <input type="number" class="form-control" id="tankCapacityUpdate" placeholder="Enter  Tank Capacity">

                    </div>
                    <div class="form-group col-md-6">
                        <label for="">Status</label>
                        <select class="form-control" id="statusUpdate">
                            <option value="Valid"> Valid</option>
                            <option value="Not valid"> Not valid</option>

                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="my-input">Sticker Number</label>
                    <input id="stickerNumberUpdate" class="form-control sblSticker" type="text">
                </div>

                <div class="form-group">
                    <label for="my-textarea">Remark</label>
                    <textarea id="remarkUpdate" class="form-control" name="" rows="3"></textarea>
                </div>
            </div>
            <!-- </form> -->
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="update-sbl">Update</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>






<script>
    const plateSearch = document.querySelector('#plateSearch')

    plateSearch.addEventListener('click', (e) => {
        e.preventDefault()
        const licensePlate = document.querySelector('#licensePlate')
        const hash = document.querySelector('#customerId')


        if (hash.value == '') {
            swal({
                title: 'Select Customer First',
                icon: "warning",
                timer: 2500
            });
        } else {
            if (licensePlate.value == '') {
                licensePlate.style.border = '1px solid red'
            } else {
                licensePlate.style.border = '1px solid green'
                renderResult(hash.value, licensePlate.value)
            }


        }

    })

    function renderResult(hash, licensePlate) {

        const resultContainer = document.querySelector('#vehicles')
        $.ajax({
            type: "POST",
            url: "searchSbl",
            data: {
                // csrf_hash: document.querySelector('.token').value,
                hash: hash,
                licensePlate: licensePlate,

            },
            dataType: "json",
            success: function(response) {
                // $('#vehicles').html('')
                // document.querySelector('.token').value= response.token
                console.log(response)
                if (response.data == '') {
                    $('#vehicles').append('<tr></tr>')
                } else {
                    $('#searchPlate').css('visibility', 'visible')

                    $('#vehicles').append(
                        `
                      
                                <li class="list-group-item">${response.data.vehicle_brand}
                                | ${response.data.plate_number}
                                | ${response.data.capacity} m<sup>3</sup>
                                | <span class="amount">${response.data.amount} </span>
                                | Driver:${response.data.driver_name}
                                | ${response.data.status}
                                | ${response.data.next_calibration}
                                
                                  <i data-remove="remove" class="fas fa-times-square" id="delete-btn"></i>
                                  <i class="fas fa-pen-square"
                                        onclick="editVehicle('${response.data.id}')" id="update-btn"></i>
                               
                                        <input type='text' value='${response.data.plate_number}'  class="lorryId" hidden >
                                </li>

                            
                        `
                    )

                }
            }
        });



    }



    const resultContainer = document.querySelector('#vehicles')
    resultContainer.addEventListener('click', (e) => {

        if (e.target.hasAttribute('data-remove', 'remove')) {

            const li = e.target.parentElement;

            resultContainer.removeChild(li)

        }
    })

    function editVehicle(id) {

        $.ajax({
            type: "POST",
            url: "editSbl",
            data: {
                // csrf_hash: document.querySelector('.token').value,
                id: id
            },
            dataType: "json",
            success: function(vehicle) {
                // document.querySelector('.token').value = vehicle.token
                const theId = $('#theId').val(vehicle.data.id)
                const tinNumberUpdate = $('#tinNumberUpdate').val(vehicle.data.tin_number)
                const supervisorUpdate = $('#supervisorUpdate').val(vehicle.data.supervisor)
                const supervisorPhoneUpdate = $('#supervisorPhoneUpdate').val(vehicle.data.supervisor_phone)
                const driverNameUpdate = $('#driverNameUpdate').val(vehicle.data.driver_name)
                const driverLicenseUpdate = $('#driverLicenseUpdate').val(vehicle.data.driver_license)
                const vehicleBrandUpdate = $('#vehicleBrandUpdate').val(vehicle.data.vehicle_brand)
                const licensePlateUpdate = $('#licensePlateUpdate').val(vehicle.data.plate_number)
                const tankCapacityUpdate = $('#tankCapacityUpdate').val(vehicle.data.capacity)
                const statusUpdate = $('#statusUpdate').val(vehicle.data.status)
                const stickerNumberUpdate = $('#stickerNumberUpdate').val(vehicle.data.sticker_number)
                const amountUpdate = $('#amountUpdate').val(vehicle.data.amount)
                const remarkUpdate = $('#remarkUpdate').val(vehicle.data.remark)

                // console.log(response)
                $('.edit-sbl-modal').modal({
                    show: true,
                    focus: true,
                    backdrop: 'static'

                })


            }
        });

        $('.edit-sbl-modal').modal({
            show: true,
            focus: true,
            backdrop: 'static'

        })


    }
    //updateVehicle()

    //function updateVehicle() {
    const updateVehicle = document.querySelector('#update-sbl')

    // updateVehicle.addEventListener('click', () => {

    //     alert(45554546464564564654)
    // })


    updateVehicle.addEventListener('click', () => {
        // const sblCustomerHash = $('#customerHash')
        const theId = $('#theId')
        const activityUpdate = $('#activityUpdate')
        const tinNumberUpdate = $('#tinNumberUpdate')
        const supervisorUpdate = $('#supervisorUpdate')
        const supervisorPhoneUpdate = $('#supervisorPhoneUpdate')
        const driverNameUpdate = $('#driverNameUpdate')
        const driverLicenseUpdate = $('#driverLicenseUpdate')
        const vehicleBrandUpdate = $('#vehicleBrandUpdate')
        const licensePlateUpdate = $('#licensePlateUpdate')
        const tankCapacityUpdate = $('#tankCapacityUpdate')
        const statusUpdate = $('#statusUpdate')
        const stickerNumberUpdate = $('#stickerNumberUpdate')
        const amountUpdate = $('#amountUpdate')
        const remarkUpdate = $('#remarkUpdate')



        function validateInput(formInput) {

            if (formInput.val() == '') {
                formInput.css('border', '1px solid #ff6348')
                return false
            } else {
                formInput.css('border', '1px solid #2ed573')
                return true
            }

        }



        if (validateInput(activityUpdate) && validateInput(supervisorUpdate) && validateInput(
                supervisorPhoneUpdate) &&
            validateInput(tinNumberUpdate) && validateInput(driverNameUpdate) && validateInput(
                vehicleBrandUpdate) &&
            validateInput(licensePlateUpdate) && validateInput(tankCapacityUpdate) && validateInput(
                statusUpdate) &&
            validateInput(amountUpdate)
        ) {



            $.ajax({
                type: "POST",
                url: "updateLorry",
                data: {
                    // csrf_hash: document.querySelector('.token').value,
                    theId: theId.val(),
                    activity: activityUpdate.val(),
                    tinNumber: tinNumberUpdate.val(),
                    supervisor: supervisorUpdate.val(),
                    supervisorPhone: supervisorPhoneUpdate.val(),
                    driverName: driverNameUpdate.val(),
                    driverLicense: driverLicenseUpdate.val(),
                    vehicleBrand: vehicleBrandUpdate.val(),
                    licensePlate: licensePlateUpdate.val(),
                    tankCapacity: tankCapacityUpdate.val(),
                    status: statusUpdate.val(),
                    stickerNumber: stickerNumberUpdate.val(),
                    amount: amountUpdate.val(),
                    remark: remarkUpdate.val(),



                },
                dataType: "json",
                success: function(response) {
                    //clearInputs()
                    // document.querySelector('.token').value = response.token
                    $('.edit-sbl-modal').modal('hide');

                    console.log(response)
                    // syncVehicles()

                    if (response.status == 1) {
                        swal({
                            title: 'Vehicle Updated',
                            // text: "You clicked the button!",
                            icon: "success",
                            button: "Ok",
                        });

                        // grabLastVehicle()
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

        }



    })


    function calcTotal() {
        const amounts = document.querySelectorAll('.amount')
        let total = 0;
        for (let amount of amounts) {
            total += parseInt(amount.innerHTML)
        }
        $('#totalAmount').val(total)

    }
</script>