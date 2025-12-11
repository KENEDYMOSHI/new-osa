<?=$this->extend('Layouts/coreLayout');?>
<?=$this->section('content');?>
<script>
const downloadPdf = (shipId) => {
    const downloadBtn = document.querySelector('#downloadNoteOfFactAfter')
    downloadBtn.setAttribute('href', '<?=base_url()?>/downloadNoteOfFactAfter/' + shipId)
}
</script>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><?=$page['heading']?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?=base_url()?>/Dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?=$page['heading']?></li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>
<section class="content">
    <div class="container-fluid">
    <?=$this->include('Widgets/shipOptions.php')?>
        <?=$this->include('Components/shipDetails.php')?>
        <?=$this->include('Components/PortUnit/searchShip.php')?>

        <div class="card">
            <div class="card-header">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#note-modal"
                    aria-pressed="false" autocomplete="off"><i class="far fa-plus-circle" aria-hidden="true"></i>Add
                    Note</button>
                <button type="button" onclick="getNoteOfFactAfter()" class="btn btn-success btn-sm"
                    id="refreshTimeLogs"><i class="far fa-sync" aria-hidden="true"></i> Check
                    Note</button>

                <h4 id="selectedShip"></h4>
            </div>
            <div class="card-body">
                <input hidden class="txt_csrfname" name="<?=csrf_token()?>" value="<?=csrf_hash()?>" />
                <div id="noteOfFactAfter">

                </div>

            </div>
            <div class="card-footer">
                <a id="downloadNoteOfFactAfter" target="_blank" class="btn btn-success btn-sm"><i
                        class="far fa-download"></i>Download</a>
            </div>
        </div>


        <div id="note-modal" class="modal fade " tabindex="-1" role="dialog" aria-labelledby="my-modal-title"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="my-modal-title">ADD NOTE OF FACT AFTER DISCHARGING</h5>
                        <button class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">

                            <input id="shipId" class="form-control" type="number" name="" hidden>


                        </div>
                        <!-- ====== -->

                        <div class="card">
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check form-check-inline">
                                            <input id="atLoading" class="form-check-input" type="checkbox" name="">
                                            <label class="form-check-label">At Loading</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check form-check-inline">
                                            <input id="atDischarging" class="form-check-input" type="checkbox">
                                            <label class="form-check-label">At Discharging</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check form-check-inline">
                                            <input id="atTransfer" class="form-check-input" type="checkbox" name="">
                                            <label class="form-check-label">At Transfer</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check form-check-inline">
                                            <input id="atShore" class="form-check-input" type="checkbox" name="">
                                            <label class="form-check-label">At Shore</label>
                                        </div>
                                    </div>

                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <div class="form-check form-check-inline">
                                            <input id="atVessel" class="form-check-input" type="checkbox" name="">
                                            <label class="form-check-label">At Vessel</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-3">
                                        <div class="form-group ">
                                            <label class="">To the Master /Owner
                                                /Agent of the Vessel of</label>
                                            <input id="master" class="form-control" type="text" name="" data-clear>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-3">
                                        <div class="form-group ">
                                            <label class="">To the Terminal Representative of</label>
                                            <input id="terminalRep" class="form-control" type="text" name="" data-clear>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                        <h4 class="text-center mt-1">THE CARGO DIFFERENCE</h4>
                        <h5 class="text-center">BILL OF LADING QUANTITY AGAINST SHIP'S DISCHARGED QUANTITY OF
                            GASOLINE</h5>

                        <div class="row">
                            <div class="form-group col-12">
                                <label>Receiver</label>
                                <input type="text" class="form-control " id="receiver" placeholder="Receiver"
                                    data-clear>
                            </div>
                            <div class="form-group col-6">
                                <label>Bill Of Lading Quantity</label>
                                <input type="number" class="form-control " id="billOfLadingQty"
                                    placeholder="Bill Of Quantity" data-clear>
                            </div>



                            <div class="form-group col-6">
                                <label>Ship's Discharge Quantity</label>
                                <input type="number" class="form-control " id="shipDischargeQuantity"
                                    placeholder="Ship's Discharge Quantity" oninput="getQuantityDifference(this.value)"
                                    data-clear>
                            </div>
                            <div class="form-group col-6">
                                <label> Quantity Difference</label>
                                <input type="number" class="form-control" id="quantityDifference"
                                    placeholder=" Quantities Difference %" readonly data-clear>
                            </div>



                            <div class="form-group col-6">
                                <label>Difference %</label>
                                <input type="number" class="form-control " id="differencePercentage"
                                    placeholder="Difference %" readonly data-clear>
                            </div>



                        </div>
                        <hr>
                        <h5 class="text-center mt-1 mb-1">SHIP'S DISCHARGED QUANTITY AGAINST SHORE OUTTURN QUANTITY</h5>
                        <div class="row">
                            <div class="form-group col-6">
                                <label>Ship's Discharge Quantity At 15 &deg;C</label>
                                <input type="number" class="form-control " id="dischargeQuantity15c"
                                    placeholder="Discharge Quantity At 15" data-clear>
                            </div>
                            <div class="form-group col-6">
                                <label>Ship's Discharge Quantity At 20 &deg;C</label>
                                <input type="number" class="form-control " id="dischargeQuantity20c"
                                    placeholder="Discharge Quantity At 20" data-clear>
                            </div>


                        </div>
                        <div class="row">
                            <div class="form-group col-6">
                                <label>Shore Outturn Quantity</label>
                                <input type="number" class="form-control " id="shoreOutturnQuantity20c"
                                    placeholder="Shore Outturn Quantity" oninput="calcQuantityDifference2(this.value)"
                                    data-clear>
                            </div>
                            <div class="form-group col-6">
                                <label>Quantity Difference</label>
                                <input type="number" class="form-control " id="quantityDifference2"
                                    placeholder="Quantity Difference" readonly data-clear>
                            </div>
                            <div class="form-group col-12">
                                <label>Difference %</label>
                                <input type="number" class="form-control " id="differencePercentage2"
                                    placeholder="Difference %" readonly data-clear>
                            </div>


                        </div>

                        <div class="modal-footer">
                            <button type="button" id="saveNoteAfter" class="btn btn-primary btn-sm">Save Note</button>
                            <!-- <button type="button" onclick="myFunction()">test</button> -->
                        </div>
                    </div>
                </div>
            </div>


        </div><!-- /.container-fluid -->

        <script>
        function formatNumber(number) {
            return new Intl.NumberFormat().format(number)
        }


        const csrfName = document.querySelector('.txt_csrfname').getAttribute('name'); // CSRF Token name
        const csrfHash = document.querySelector('.txt_csrfname').value; // CSRF hash


        //=================*********====================

        const atLoading = document.querySelector('#atLoading')
        const atDischarging = document.querySelector('#atDischarging')
        const atTransfer = document.querySelector('#atTransfer')
        const atShore = document.querySelector('#atShore')
        const atVessel = document.querySelector('#atVessel')

        const master = document.querySelector('#master')
        const terminalRep = document.querySelector('#terminalRep')
        const receiver = document.querySelector('#receiver')

        const billOfLadingQty = document.querySelector('#billOfLadingQty')
        const shipDischargeQuantity = document.querySelector('#shipDischargeQuantity')

        const quantityDifference = document.querySelector('#quantityDifference')
        const differencePercentage = document.querySelector('#differencePercentage')

        const dischargeQuantity15c = document.querySelector('#dischargeQuantity15c')
        const dischargeQuantity20c = document.querySelector('#dischargeQuantity20c')

        const shoreOutturnQuantity20c = document.querySelector('#shoreOutturnQuantity20c')
        const quantityDifference2 = document.querySelector('#quantityDifference2')
        const differencePercentage2 = document.querySelector('#differencePercentage2')









        function getQuantityDifference(dischargeQty) {
            const diff1 = (+dischargeQty - +billOfLadingQty.value).toFixed(2)
            quantityDifference.value = diff1


            differencePercentage.value = ((diff1 / billOfLadingQty.value) * 100).toFixed(1)

            dischargeQuantity15c.value = dischargeQty

        }

        function calcQuantityDifference2(dischargeQty2) {
            const diff2 = (+dischargeQty2 - +dischargeQuantity20c.value).toFixed(2)
            quantityDifference2.value = diff2

            differencePercentage2.value = ((diff2 / +dischargeQuantity20c.value) * 100).toFixed(1)


        }

        function isChecked(input) {
            if (input.checked == true) {
                return 1
            } else {
                return 0
            }
        }

        function makeChecked(val) {
            if (val == 1) {
                return 'checked'
            } else {
                return ''

            }
        }



        function updateToken(token) {
            document.querySelector('.txt_csrfname').value = token
        }


        const saveNoteAfter = document.querySelector('#saveNoteAfter');
        saveNoteAfter.addEventListener('click', (e) => {
            e.preventDefault()
            const inputs = document.querySelectorAll('#note-modal .form-control')
            // const =



            const shipId = document.querySelector('#shipId')


            function validateInput(formInput) {

                if (formInput.value == '') {

                    formInput.style.border = '1px solid #ff6348'
                    return false
                } else {
                    formInput.style.border = '1px solid #2ed573'
                    return true
                }

            }

            if (validateInput(master) && validateInput(terminalRep) && validateInput(receiver) && validateInput(
                    billOfLadingQty) &&
                validateInput(shipDischargeQuantity) && validateInput(quantityDifference) && validateInput(
                    quantityDifference) && validateInput(dischargeQuantity15c) && validateInput(
                    dischargeQuantity20c) && validateInput(shoreOutturnQuantity20c) && validateInput(
                    quantityDifference2) && validateInput(differencePercentage2)) {
                if (shipId == '') {
                    swal({
                        title: 'Please Select Ship First',
                        icon: "warning",
                        timer: 2500
                    });
                } else {
                    $.ajax({
                        type: "POST",
                        url: "addNoteOfFactAfter",
                        dataType: "json",
                        data: {
                            shipId: shipId.value,
                            // [csrfName]: csrfHash,
                            atLoading: isChecked(atLoading),
                            atDischarging: isChecked(atDischarging),
                            atTransfer: isChecked(atTransfer),
                            atShore: isChecked(atShore),
                            atVessel: isChecked(atVessel),
                            master: master.value,
                            terminalRep: terminalRep.value,
                            receiver: receiver.value,
                            billOfLadingQty: billOfLadingQty.value,
                            shipDischargeQuantity: shipDischargeQuantity.value,
                            quantityDifference: quantityDifference.value,
                            differencePercentage: differencePercentage.value,
                            dischargeQuantity15c: dischargeQuantity15c.value,
                            dischargeQuantity20c: dischargeQuantity20c.value,
                            shoreOutturnQuantity20c: shoreOutturnQuantity20c.value,
                            quantityDifference2: quantityDifference2.value,
                            differencePercentage2: differencePercentage2.value



                        },
                        success: function(response) {


                            updateToken(response.token)
                            // console.table(response)
                            if (response.message == 'Added') {

                                getNoteOfFactAfter()
                                clearInputs()
                                $('#note-modal').modal('hide');

                                swal({
                                    title: 'Note Of Fact Saved',
                                    icon: "success",
                                    button: "Ok",
                                });

                            } else {
                                swal({
                                    title: 'Something Went Wrong!',
                                    icon: "error",
                                    button: "Ok",
                                });
                            }
                        }
                    }, );
                }

            }



        }, {
            once: false
        })


        function getNoteOfFactAfter() {


            const shipId = document.querySelector('#shipId').value
            if (shipId == '') {
                swal({
                    title: 'Please Select Ship First',
                    icon: "warning",
                    timer: 2500
                });
            } else {
                $.ajax({
                    type: "POST",
                    url: "getNoteOfFactAfter",
                    data: {
                        shipId: shipId
                    },
                    dataType: "json",
                    success: function(response) {

                        console.table(response);

                        if (response == 'nothing') {
                            $('#noteOfFactAfter').html('')
                            swal({
                                title: 'No Data Available',
                                icon: "warning",
                                timer: 3500
                            });
                        } else {
                            downloadPdf(response.ship_id)
                            $('#noteOfFactAfter').html('')
                            $('#noteOfFactAfter').append(`



                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-check form-check-inline">
                                <input id="" class="form-check-input" type="checkbox" ${makeChecked(response.at_loading)} onclick="return false;">
                                <label class="form-check-label"  >At Loading</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check form-check-inline">
                                <input id="" class="form-check-input" type="checkbox" ${makeChecked(response.at_discharging)} onclick="return false;">
                                <label class="form-check-label">At Discharging</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check form-check-inline">
                                <input id="" class="form-check-input" type="checkbox" ${makeChecked(response.at_transfer)} onclick="return false;">
                                <label class="form-check-label">At Transfer</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check form-check-inline">
                                <input id="" class="form-check-input" type="checkbox" ${makeChecked(response.at_shore)} onclick="return false;">
                                <label class="form-check-label">At Shore</label>
                            </div>
                        </div>

                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <div class="form-check form-check-inline">
                                <input id="" class="form-check-input" type="checkbox" ${makeChecked(response.at_vessel)} onclick="return false;">
                                <label class="form-check-label">At Vessel</label>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <div class="form-check form-check-inline">
                                <input id="" class="form-check-input" type="checkbox"  onclick="return false;">
                                <label for="" class="form-check-label">To the Master /Owner
                                    /Agent of the Vessel of <span><b>${response.ship_name}</b></span></label>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <div class="form-check form-check-inline">
                                <input id="" class="form-check-input" type="checkbox" onclick="return false;">
                                <label class="form-check-label">To the Terminal Representative of <span><b>${response.terminal_rep}</b></span></label>
                            </div>
                        </div>



                    </div>

                    <!-- table -->
                    <h5 class="text-center mt-4">THE CARGO DIFFERENCE</h5>
                    <h6 class="text-center">BILL OF LADING QUANTITY AGAINST SHIP'S DISCHARGED QUANTITY OF GASOLINE</h6>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>RECEIVER</th>
                                <th>B/LADING QTY</th>
                                <th>SHIP'S DISCH QTY</th>
                                <th>DIFFERENT QTY</th>
                                <th>DIFFERENCE %</th>

                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>${response.receiver}</td>
                                <td>${response.bill_of_lading_qty}</td>
                                <td>${response.ship_discharging_qty}</td>
                                <td>${response.qty_diff}</td>
                                <td>${response.diff_percentage}</td>
                            </tr>
                            <tr>
                                <th colspan="5" class="text-center">SHIP'S DISCHARGED QUANTITY AGAINST SHORE OUTTURN
                                    QUANTITY</th>
                            </tr>
                            <tr>
                                <th colspan="2">SHIP'S DISCHARGE QTY</th>
                                <th>SHORE OUTTURN QTY</th>
                                <th>DIFFERENT QTY</th>
                                <th>DIFFERENCE %</th>
                            </tr>

                            <tr>
                                <th>At 15⁰C</th>
                                <td>${response.discharging_qty_15c}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>

                            <tr>
                                <th>At 20⁰C</th>
                                <td>${response.discharging_qty_20c}</td>
                                <td>${response.shore_outturn_qty}</td>
                                <td>${response.qty_diff_2}</td>
                                <td>${response.diff_percentage_2}</td>
                            </tr>
                        </tbody>

                    </table>


                           `)
                        }
                    }
                });
            }

        }


        // function formatDate(dateInput) {
        //     const date = new Date(dateInput);
        //     const formattedDate = date.toLocaleDateString('en-GB', {
        //         day: 'numeric',
        //         month: 'short',
        //         year: 'numeric'
        //     }).replace(/ /g, '-');

        //     return formattedDate
        // }
        //=================Processing Certificate of Quantity====================
        </script>
</section>
<?=$this->endSection();?>