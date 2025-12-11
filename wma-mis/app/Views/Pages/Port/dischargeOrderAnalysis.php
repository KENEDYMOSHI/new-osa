<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
<script>
    const downloadDocument = (id) => {
        const logDownload = document.querySelector('#downloadDischargeOrder')
        logDownload.setAttribute('href', '<?= base_url() ?>/downloadDischargeOrder/' + id)
    }
</script>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><?= $page['heading'] ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>/Dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?= $page['heading'] ?></li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>
<section class="content">
    <div class="container-fluid">
        <?= $this->include('Widgets/shipOptions.php') ?>
        <?= $this->include('Components/shipDetails.php') ?>
        <?= $this->include('Components/PortUnit/searchShip.php') ?>

        <div class="card">
            <div class="card-header">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#DischargeOrder-modal" aria-pressed="false" autocomplete="off"><i class="far fa-plus-circle" aria-hidden="true"></i>Add
                    Discharge Order</button>
                <button type="button" onclick="getAllDischargeOrder()" class="btn btn-success btn-sm" id="refreshDischargeOrder"><i class="far fa-sync" aria-hidden="true"></i> Check
                </button>

                <h4 id="selectedShip"></h4>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <div id="qty" class="row">
                        </div>
                        <tr>

                            <th scope="col">#</th>
                            <th scope="col">RECEIVING TERMINAL</th>
                            <th scope="col">RECEIVER / OWNER</th>
                            <th scope="col">QUANTITY</th>
                            <th scope="col">DESTINATION</th>

                        </tr>
                    </thead>
                    <tbody id="currentLogs">


                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <a id="downloadDischargeOrder" target="_blank" class="btn btn-success btn-sm"><i class="far fa-download" aria-hidden="true"></i>Download</a>
            </div>
        </div>


        <div id="DischargeOrder-modal" class="modal fade " tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="my-modal-title">ADD DISCHARGE ORDER</h5>
                        <button class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row">
                        <div class="form-group col-md-12">

                            <input id="shipId" class="form-control" type="number" hidden>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="my-input">Receiving Terminal</label>
                            <input id="receivingTerminal" class="form-control" type="text" name="" data-clear>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="my-input">Receiver</label>
                            <input id="receiver" class="form-control" type="text" name="" data-clear>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="my-textarea">Quantity</label>
                            <input id="receivedQuantity" class="form-control" type="number" name="" data-clear>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="my-textarea">Destination</label>
                            <input id="theDestination" class="form-control" type="text" name="">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" id="saveDischargeOrder" class="btn btn-primary btn-sm">Save</button>
                    </div>
                </div>
            </div>
        </div>


    </div><!-- /.container-fluid -->

    <script>
        const saveDischargeOrder = document.querySelector('#saveDischargeOrder');

        saveDischargeOrder.addEventListener('click', (e) => {
            e.preventDefault()



            const shipId = document.querySelector('#shipId')
            const receivingTerminal = document.querySelector('#receivingTerminal')
            const receiver = document.querySelector('#receiver')
            const quantity = document.querySelector('#receivedQuantity')
            const destination = document.querySelector('#theDestination')





            function validateInput(formInput) {

                if (formInput.value == '') {

                    formInput.style.border = '1px solid #ff6348'
                    return false
                } else {
                    formInput.style.border = '1px solid #2ed573'
                    return true
                }

            }


            if (validateInput(receivingTerminal) && validateInput(receiver) && validateInput(quantity) &&
                validateInput(destination)) {

                if (shipId.value != '') {

                    $.ajax({
                        type: "POST",
                        url: "addDischargeOrder",
                        data: {
                            shipId: shipId.value,
                            receivingTerminal: receivingTerminal.value,
                            receiver: receiver.value,
                            quantity: quantity.value,
                            destination: destination.value,

                        },
                        dataType: "json",
                        success: function(response) {


                            console.table(response)
                            if (response == 'Added') {
                                clearInputs()
                                downloadDocument(shipId.value)
                                getAllDischargeOrder()
                                $('#DischargeOrder-modal').modal('hide');

                                swal({
                                    title: 'Discharge Order Saved',
                                    // text: "You clicked the button!",
                                    icon: "success",
                                    button: "Ok",
                                });

                            } else {
                                swal({
                                    title: 'Something Went Wrong!',
                                    // text: "You clicked the button!",
                                    icon: "error",
                                    button: "Ok",
                                });
                            }
                        }
                    }, );
                } else {
                    swal({
                        title: 'Please Select  The Ship First!',
                        // text: "You clicked the button!",
                        icon: "warning",
                        button: "Ok",
                        timer: 3500
                    });
                }
            }



        })




        //=================select all the tim logs====================
        function getAllDischargeOrder() {

            const shipId = document.querySelector('#shipId')

            if (shipId.value != '') {
                $.ajax({
                    type: "POST",
                    url: "getDischargeOrder",
                    data: {
                        shipId: shipId.value
                    },
                    dataType: "json",
                    success: function(response) {
                        $('#currentLogs').html('')
                        if (response == 'nothing') {
                            $('#currentLogs').html('<h3>No Discharge Order Found</h3>')
                        } else {

                            downloadDocument(response.data[0].ship_id)

                            console.log(response)
                            let index = 1;
                            let quantityTotal = 0
                            $('#qty').html('')
                            $('#qty').append(`
                                 <div class="col-md-12 mb-2">B/L QUANTITY :<b>${response.billOfLading}</b></div>
                                 <div class="col-md-12">ARRIVAL QTY AT 20&deg;C :<b>${response.arrivalQuantity}</b></div>


                               `)

                            for (let order of response.data) {
                                quantityTotal += +order.quantity

                                $('#currentLogs').append(`

                                    <tr>
                                        <td>${index++}</td>
                                        <td>${order.receiving_terminal}</td>
                                        <td>${order.receiver}</td>
                                        <td>${order.quantity}</td>
                                        <td>${order.destination}</td>

                                    </tr>
                        `)



                            }

                            $('#currentLogs').append(`
                            <tr>
                                <th colspan = "3" >SUBTOTAL</th>
                                <th>${(quantityTotal).toFixed(3)}</th>
                                <><
                            </tr>
                        `)
                        }




                    }
                });
            } else {
                swal({
                    title: 'Please Select The Ship First!',
                    icon: "warning",
                    timer: 3500
                });
            }

        }
    </script>
</section>
<?= $this->endSection(); ?>