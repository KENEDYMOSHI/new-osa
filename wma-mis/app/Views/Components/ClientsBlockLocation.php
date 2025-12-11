<!-- Modal -->
<div class="modal fade" id="mapModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">GEOLOCATION</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" >
                <div class="mapBox">
                <div id="map"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                <!-- <button type="button" class="btn btn-primary btn-sm">Save</button> -->
            </div>
        </div>
    </div>
</div>
<div class="clientsBlock">
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Customer </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addCustomerForm" name="addCustomerForm">
                        <?= regionsAndDistricts(6) ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Search</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="searchForm" name="searchForm">
                        <div class="input-group">
                            <input class="form-control" name="keyword" type="text" placeholder="Search here..." id="keyword" required>
                            <button class="btn btn-success btn-flat" type="submit">Search</button>
                        </div>
                    </form>

                    <ul class="list-group mt-2" id="searchResults">



                    </ul>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    <!-- <button type="button" class="btn btn-primary btn-sm">Save</button> -->
                </div>
            </div>
        </div>
    </div>

    <!-- /.content-header -->
    <div class="container111">

        <div class="card">
            <div class="card-header">

                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#searchModal">
                    <i class="fal fa-search"></i> Search Customer
                </button>

                <!-- visible only for add prepackage page -->

                <?php if (url_is('registeredPrepackages')) : ?>
                <?php else : ?>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addModal">
                        <i class="fal fa-plus"></i> Add Customer
                    </button>
                <?php endif; ?>




            </div>
            <div class="card-body">
               
                <table class="table table-sm">

                    <tbody id="customerInfo">


                    </tbody>
                </table>
            </div>
            <!-- <div class="card-footer text-muted">
            Footer
        </div> -->
        </div>



    </div>
</div>

<script>
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
    //get customer info based on hash value


    //saving customer info
    // $('#addCustomerForm').validate()
    $('#addCustomerForm').validate({
        rules: {
            'phoneNumber': {
                required: true,
                minlength: 10,
                maxlength: 10,
            }
        },
        messages: {
            'phoneNumber': {
                required: 'Please enter your mobile number',
                minlength: 'Your mobile number must be 10 characters long',
                maxlength: 'Your mobile number must be 10 characters long'
            }
        }
    });




    $("#addCustomerForm").validate()
    const customerForm = document.querySelector("#addCustomerForm");
    customerForm.addEventListener("submit", (e) => {
        e.preventDefault();

        // Create a new FormData object

        if ($('#addCustomerForm').valid()) {
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
                    fetch("addCustomer", {
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
                                status,
                                customer,
                                msg,
                            } = data;
                            document.querySelector(".token").value = token;

                            if (status == 1) {
                                customerForm.reset()
                                $('#addModal').modal('hide')
                                document.querySelector('#customerInfo').innerHTML = customer
                                swal({
                                    title: msg,
                                    icon: "success",
                                });

                            }
                            console.log(data);
                        });
                })
                .catch((error) => {
                    // console.log(error.message);
                    swal({
                        title: error.message,
                        text: 'Please enable location services manually and try again.',
                        icon: "warning",
                        // timer: 42500
                    });
                });
        }

    });


    //get location data and render a google map
    function openMap(latitude, longitude, name) {
        $('#mapModal').modal('show')
        let map;

        async function initMap() {
            const position = {
                lat: parseFloat(latitude),
                lng: parseFloat(longitude)
            };
            // Request needed libraries.
            //@ts-ignore
            const {
                Map
            } = await google.maps.importLibrary("maps");
            const {
                AdvancedMarkerView
            } = await google.maps.importLibrary(
                "marker"
            );

            // The map, centered at 
            map = new Map(document.getElementById("map"), {
                zoom: 15,
                center: position,
                mapId:'2023_WMA'
            });

            // The marker, positioned at 
            const marker = new AdvancedMarkerView({
                map: map,
                position: position,
                title: name,
            });

            const infoWindow = new google.maps.InfoWindow({
                content: name,
                position: position,
            });

            // Open the InfoWindow on the marker
            infoWindow.open(map, marker);

            // Create a div for the location name box
            const locationBox = document.createElement("div");
            locationBox.classList.add("location-box");
            locationBox.innerHTML = name;

            // Add the location name box to the map
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(locationBox);
        }

        initMap();
    }

    //-6.8580858
    //39.2271486



    // searching customer
    searchForm.addEventListener('submit', (e) => {
        e.preventDefault()
        const data = {
            url: 'searchCustomer',
            formData: new FormData(searchForm)
        }
        $.ajax({
            type: "POST",
            url: data.url,
            data: data.formData,
            cache: false,
            processData: false,
            contentType: false,
            dataType: "json",
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $('.token').val());
            },

            success: function(response) {

                console.log(response)
                document.querySelector('.token').value = response.token
               
                renderSearchResults(response)




            },
            error: function(err) {
                // console.log(err);
            }

        });



    })


    // rendering search results
    function renderSearchResults(res) {
        $('#searchResults').html('')
        console.log(res.data.length);

        let list = ``
        if (res.data.length == 0) {
            $('#searchResults').html('<h6>No Match Found!</h6>');
            console.log('no match');
        } else {
            res.data.forEach(customer => {
                list += `
           <li onclick="getCustomerInfo('${customer.hash}')" class="list-group-item d-flex justify-content-between align-items-center " style="cursor:pointer">
             ${customer.name}|  ${customer.physical_address} |  ${customer.phone_number}
           </li>
          `
            });


            $('#searchResults').append(list)
        }




    }


    //getting customer details ans render on the dom
    function getCustomerInfo(hash) {
        $('#searchModal').modal('hide')
        fetch('selectClient', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    'X-CSRF-TOKEN': document.querySelector('.token').value
                },

                body: JSON.stringify({
                    hash: hash
                }),

            }).then(response => response.json())
            .then(data => {
                const {
                    token,
                    status,
                    customer
                } = data
                document.querySelector('.token').value = token
                document.querySelector('#customerInfo').innerHTML = customer
                console.log(data)
            })
    }
</script>