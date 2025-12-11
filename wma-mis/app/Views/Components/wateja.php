<div class="modal fade" id="add-new">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Customer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="customerForm" name="customerForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Client Name / Company Name </label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter First Name" required>


                    </div>


                    <div class="row">
                        <?= $this->include('Widgets/DependentSelect'); ?>
                    </div>

                    <div class="row">


                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Street/Ward</label>
                                <input id="ward" name="ward" type="text" class="form-control" placeholder="Enter Street or Ward" required>


                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Village</label>
                                <input id="village" name="village" type="text" class="form-control" placeholder="Enter  Village" value="">


                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Postal Address</label>
                                <input id="postalAddress" name="postalAddress" type="text" class="form-control postal" placeholder="Enter Postal Address">


                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Postal Code</label>
                                <input id="postalCode" name="postalCode" type="text" class="form-control " placeholder="Enter Postal Code">


                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Physical Address</label>
                                <input id="physicalAddress" name="physicalAddress" type="text" class="form-control " placeholder="Enter Physical Address">


                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input id="phoneNumber" name="phoneNumber" type="text" class="form-control phone" id="" placeholder="Enter Phone Number" required>


                            </div>
                        </div>



                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="save-customer">Save
                    </button>
            </form>
        </div>
    </div>
    <!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->
</div>
<!-- Search customer -->
<div class="modal fade" id="search">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Search Existing Customer</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="input-group">

                    <input id="searchKeyWord" class="form-control border-right-0" type="text">
                    <button type="button" id="searchButton" class="btn btn-primary"><i class="far fa-search"></i>Search</button>
                </div>
                <div class="searchResults mt-2">






                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <!-- <button type="button" class="btn btn-primary">Proceed</button> -->
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<script>
    //getting current geolocation
    function getCoordinates() {
        return new Promise((resolve, reject) => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;
                        resolve({
                            latitude,
                            longitude
                        });
                    },
                    (error) => {
                        reject(error);
                    }
                );
            } else {
                reject("Geolocation is not supported by this browser.");
            }
        });
    }

    $("#customerForm").validate()
    const customerForm = document.querySelector("#customerForm");
    customerForm.addEventListener("submit", (e) => {
        e.preventDefault();

        // Create a new FormData object

        if ($('#customerForm').valid()) {
            const formData = new FormData(customerForm);

        if (!navigator.geolocation) {
            alert("Geolocation is not supported by your browser. Please enable location services manually and try again.");
            return;
        }

        // Get the coordinates using the getCoordinates() function
        getCoordinates()
            .then((coords) => {
                // Add the coordinates to the FormData object

                console.log(coords)
                formData.append("latitude", coords.latitude);
                formData.append("longitude", coords.longitude);

                // Make the POST request to the server
                fetch("newCustomer", {
                        method: "POST",
                        headers: {
                            "X-Requested-With": "XMLHttpRequest",
                            "X-CSRF-TOKEN": document.querySelector(".token").value,
                        },
                        body: formData,
                    })
                    .then((response) => response.json())
                    .then((data) => {
                        const {
                            token,
                            status
                        } = data;
                        document.querySelector(".token").value = token;
                        // console.log(data);
                    });
            })
            .catch((error) => {
                // console.log(error.message);
                swal({
                    title: error.message,
                    text: 'Please Enable Location To Proceed',
                    icon: "warning",
                    timer: 42500
                });
            });
        }
        
    });


































   


    // $('#customerForm').on('submit', function(e) {
    //     e.preventDefault()
    //     if ($('#customerForm').valid()) {

    //         let formData = new FormData(this);
    //         // console.log(formData);
    //         // formData.append("customerId", document.querySelector('#customerId').value);
    //         // formData.append("csrf_hash", document.querySelector('.token').value);
    //         $.ajax({
    //             type: "POST",
    //             url: "newCustomer",
    //             data: formData,
    //             cache: false,
    //             processData: false,
    //             contentType: false,
    //             dataType: "json",
    //             beforeSend: function() {
    //                 // $('#preloader').show();
    //             },
    //             success: function(response) {
    //                 $("#customerForm")[0].reset()
    //                 $("#add-new").modal('hide')

    //                 // console.log(response);
    //                 selectCustomer(response.lastCustomer.hash);

    //                 swal({
    //                     title: response.msg,
    //                     icon: "success",
    //                 });


    //             },
    //             error: function(err) {
    //                 console.log(err);
    //             }

    //         });
    //     } else {
    //         return false
    //         // console.log('invalid');
    //     }

    // })

    function selectCustomer(hash) {



        $.ajax({
            type: "POST",
            url: "selectCustomer",
            data: {
                // csrf_hash: document.querySelector('.token').value,
                customerHash: hash
            },
            dataType: "json",
            success: function(response) {
                console.log(response);
                // document.querySelector('.token').value = response.token
                renderPersonalDetails(response.data)
            }
        });

    }
</script>