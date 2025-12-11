<?=$this->extend('Layouts/coreLayout');?>
<?=$this->section('content');?>
<script>
const downloadPdf = (shipId) => {
    const downloadBtn = document.querySelector('#downloadNoteOfFactBefore')
    downloadBtn.setAttribute('href', '<?=base_url()?>/downloadNoteOfFactBefore/' + shipId)
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
                <button type="button" onclick="getNoteOfFactBefore()" class="btn btn-success btn-sm"
                    id="refreshTimeLogs"><i class="far fa-sync" aria-hidden="true"></i>Check
                    Note</button>

                <h4 id="selectedShip"></h4>
            </div>
            <div class="card-body">
                <div id="noteOfFactBefore">

                </div>

            </div>
            <div class="card-footer">
                <a id="downloadNoteOfFactBefore" target="_blank" class="btn btn-success btn-sm"><i
                        class="far fa-download" aria-hidden="true"></i>Download</a>
            </div>
        </div>


        <div id="note-modal" class="modal fade " tabindex="-1" role="dialog" aria-labelledby="my-modal-title"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="my-modal-title">ADD NOTE OF FACT BEFORE DISCHARGING</h5>
                        <button class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">

                            <input id="shipId" class="form-control" type="number" name="" hidden>
                        </div>
                        <!-- ====== -->

                        <div class="row">
                            <div class="form-group col-6">
                                <label for="my-input">Bill Of Lading (MT)</label>
                                <input type="number" class="form-control " id="billOfLading1"
                                    placeholder="Bill Of Lading" data-clear>
                            </div>

                            <div class="form-group col-6">
                                <label for="my-input">Vessel Figure After After Loading (MT)</label>
                                <input type="number" class="form-control " id="vesselFigAfterLoading1"
                                    placeholder="Vessel Figure After After Loading (MT)" data-clear>
                            </div>

                        </div>
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="my-input">Vessel Arrival Quantities (MT)</label>
                                <input type="number" class="form-control " id="arrivalQuantity1"
                                    placeholder="Vessel Arrival Quantity (MT)"
                                    oninput="calcBillOfLadingFirst(this.value)" data-clear>
                            </div>
                            <div class="form-group col-6">
                                <label for="my-input">Vessel Arrival Quantities (MT)</label>
                                <input type="number" class="form-control" id="arrivalQuantity2"
                                    placeholder="Vessel Arrival Quantity (MT)"
                                    oninput="calcBillOfLadingSecond(this.value)" data-clear>
                            </div>


                        </div>
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="my-input">Difference</label>
                                <input type="number" class="form-control " id="Difference1" placeholder="Difference"
                                    data-clear>
                            </div>
                            <div class="form-group col-6">
                                <label for="my-input">Difference</label>
                                <input type="number" class="form-control " id="Difference2" placeholder="Difference"
                                    data-clear>
                            </div>


                        </div>
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="my-input">% - Difference</label>
                                <input type="number" class="form-control " id="DifferencePercent1"
                                    placeholder="% - Difference" data-clear>
                            </div>
                            <div class="form-group col-6">
                                <label for="my-input">% - Difference</label>
                                <input type="number" class="form-control " id="DifferencePercent2"
                                    placeholder="Difference" data-clear>
                            </div>


                        </div>
                        <hr>

                        <div class="row">
                            <div class="col-9">
                                <p>After adjusting ship's figure with the Vessel Experience Factor (VEF) </p>
                            </div>
                            <div class="col-3">
                                <input id="vesselExperienceFactor" class="form-control pull-left" type="number"
                                    oninput="vesselExperienceFactor(this.value)" data-clear>
                            </div>
                        </div>
                        <p>The following is noted:</p>
                        <!-- ########################################################################## -->
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="my-input">Bill Of Lading (MT)</label>
                                <input type="number" class="form-control " id="billOfLading1_b"
                                    placeholder="Bill Of Lading" data-clear>
                            </div>

                            <div class="form-group col-6">
                                <label for="my-input">Vessel Figure After After Loading (MT)</label>
                                <input type="number" class="form-control " id="vesselFigAfterLoading1_b"
                                    placeholder="Vessel Figure After After Loading (MT)" data-clear>
                            </div>

                        </div>
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="my-input">Vessel Arrival Quantities (MT) </label>
                                <input type="number" class="form-control " id="arrivalQuantity1_b"
                                    placeholder="Vessel Arrival Quantity (MT)"
                                    oninput="calcBillOfLadingFirst(this.value)" data-clear>
                            </div>
                            <div class="form-group col-6">
                                <label for="my-input">Vessel Arrival Quantities (MT) </label>
                                <input type="number" class="form-control" id="arrivalQuantity2_b"
                                    placeholder="Vessel Arrival Quantity (MT)"
                                    oninput="calcBillOfLadingSecond(this.value)" data-clear>
                            </div>


                        </div>
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="my-input">Difference </label>
                                <input type="number" class="form-control " id="Difference1_b" placeholder="Difference"
                                    data-clear>
                            </div>
                            <div class="form-group col-6">
                                <label for="my-input">Difference </label>
                                <input type="number" class="form-control " id="Difference2_b" placeholder="Difference"
                                    data-clear>
                            </div>


                        </div>
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="my-input">% - Difference</label>
                                <input type="number" class="form-control " id="DifferencePercent1_b"
                                    placeholder="% - Difference" data-clear>
                            </div>
                            <div class="form-group col-6">
                                <label for="my-input">% - Difference</label>
                                <input type="number" class="form-control " id="DifferencePercent2_b"
                                    placeholder="Difference" data-clear>
                            </div>


                        </div>
                        <!-- ########################################################################## -->

                        <div class="modal-footer">
                            <button type="button" id="saveNoteBefore" class="btn btn-primary btn-sm">Save Note</button>
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


        //=================*********====================

        //=================right side====================
        const billOfLading1 = document.querySelector('#billOfLading1')
        const DifferencePercent1 = document.querySelector('#DifferencePercent1')
        Difference1 = document.querySelector('#Difference1')

        function calcBillOfLadingFirst(arrivalQty1) {
            const diffOne = arrivalQty1 - billOfLading1.value
            Difference1.value = diffOne.toFixed(3)

            DifferencePercent1.value = ((diffOne / billOfLading1.value) * 100).toFixed(3)

        }
        //=================left side====================
        const vesselFigAfterLoading1 = document.querySelector('#vesselFigAfterLoading1')
        const DifferencePercent2 = document.querySelector('#DifferencePercent2')
        Difference2 = document.querySelector('#Difference2')

        function calcBillOfLadingSecond(arrivalQty2) {

            const diffTwo = arrivalQty2 - vesselFigAfterLoading1.value
            Difference2.value = diffTwo.toFixed(3)

            DifferencePercent2.value = ((diffTwo / vesselFigAfterLoading1.value) * 100).toFixed(3)

        }

        //=================second block====================
        const billOfLading1_b = document.querySelector('#billOfLading1_b')
        const arrivalQuantity1 = document.querySelector('#arrivalQuantity1')
        const arrivalQuantity1_b = document.querySelector('#arrivalQuantity1_b')
        const arrivalQuantity2_b = document.querySelector('#arrivalQuantity2_b')
        const vesselFigAfterLoading1_b = document.querySelector('#vesselFigAfterLoading1_b')
        const DifferencePercent1_b = document.querySelector('#DifferencePercent1_b')
        const DifferencePercent2_b = document.querySelector('#DifferencePercent2_b')
        const Difference1_b = document.querySelector('#Difference1_b')
        const Difference2_b = document.querySelector('#Difference2_b')

        function vesselExperienceFactor(vef) {
            billOfLading1_b.value = billOfLading1.value
            const ArrivalB = (arrivalQuantity1.value / vef).toFixed(3)
            arrivalQuantity1_b.value = ArrivalB

            const DifferenceB = ((arrivalQuantity1.value / vef) - billOfLading1.value).toFixed(3)

            Difference1_b.value = DifferenceB
            DifferencePercent1_b.value = ((DifferenceB / ArrivalB) * 100).toFixed(3)
            //=====================================
            vesselFigAfterLoading1_b.value = (vesselFigAfterLoading1.value / vef).toFixed(3)
            arrivalQuantity2_b.value = ArrivalB

            // const Difference2B = (arrivalQuantity2_b.value - (vesselFigAfterLoading1.value / vef)).toFixed(3)
            const Difference2B = (arrivalQuantity2_b.value - (vesselFigAfterLoading1.value / vef)).toFixed(3)
            Difference2_b.value = Difference2B
            Diff100Percent = ((Difference2B / ArrivalB) * 100).toFixed(3)
            DifferencePercent2_b.value = Diff100Percent



        }






        const saveNoteBefore = document.querySelector('#saveNoteBefore');
        saveNoteBefore.addEventListener('click', (e) => {
            e.preventDefault()
            const inputs = document.querySelectorAll('#note-modal .form-control')
            const vesselExperienceFactor = document.querySelector('#vesselExperienceFactor')



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

            if (validateInput(shipId)) {
                $.ajax({
                    type: "POST",
                    url: "addNoteOfFactBefore",
                    dataType: "json",
                    data: {
                        shipId: shipId.value,
                        billOfLading1: billOfLading1.value,
                        vesselFigAfterLoading1: vesselFigAfterLoading1.value,
                        arrivalQuantity1: arrivalQuantity1.value,
                        arrivalQuantity2: arrivalQuantity2.value,
                        Difference1: Difference1.value,
                        Difference2: Difference2.value,
                        DifferencePercent1: DifferencePercent1.value,
                        DifferencePercent2: DifferencePercent2.value,
                        vesselExperienceFactor: vesselExperienceFactor.value,
                        billOfLading1_b: billOfLading1_b.value,
                        vesselFigAfterLoading1_b: vesselFigAfterLoading1_b.value,
                        arrivalQuantity1_b: arrivalQuantity1_b.value,
                        arrivalQuantity2_b: arrivalQuantity2_b.value,
                        Difference1_b: Difference1_b.value,
                        Difference2_b: Difference2_b.value,
                        DifferencePercent1_b: DifferencePercent1_b.value,
                        DifferencePercent2_b: DifferencePercent2_b.value,


                    },
                    success: function(response) {


                        console.log(response)
                        if (response == 'Added') {
                            clearInputs()
                            getNoteOfFactBefore()
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



        })


        function getNoteOfFactBefore() {
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
                    url: "getNoteOfFactBefore",
                    data: {
                        shipId: shipId
                    },
                    dataType: "json",
                    success: function(response) {

                        if (response == 'nothing') {
                            $('#noteOfFactBefore').html('')
                            swal({
                                title: 'No Data Available',
                                icon: "warning",
                                timer: 3500
                            });
                        } else {
                            downloadPdf(response.ship_id)
                            $('#noteOfFactBefore').html('')
                            $('#noteOfFactBefore').append(`
                    <table class="table" border="1" style="border: 1px solid #e1e1e1;">
                        <tr>
                            <td>BILL OF LADING (MT)</td>
                            <td>${response.billOfLading1}</td>
                            <td>VESSEL FIGURE AFTER LOADING (MT)</td>
                            <td>${response.vesselFigAfterLoading1}</td>
                        </tr>
                        <tr>
                            <td>VESSEL ARRIVAL QUANTITIES (MT)</td>
                            <td>${response.arrivalQuantity1}</td>
                            <td>VESSEL ARRIVAL QUANTITIES (MT)</td>
                            <td>${response.arrivalQuantity2}</td>
                        </tr>
                        <tr>
                            <td>DIFFERENCE</td>
                            <td>${response.Difference1}</td>
                            <td>DIFFERENCE</td>
                            <td>${response.Difference2}</td>
                        </tr>
                        <tr>
                            <td>% DIFFERENCE</td>
                            <td>${response.DifferencePercent1}</td>
                            <td>% DIFFERENCE</td>
                            <td>${response.DifferencePercent2}</td>
                        </tr>

                    </table>

                    <p>After adjusting figures with vessel experience factor (VEF) <b>${response.vesselExperienceFactor}</b></p>
                    <p>The following Noticed:</p>
                    <table class="table" border="1" style="border: 1px solid #e1e1e1;">
                        <tr>
                            <td>BILL OF LADING (MT)</td>
                            <td>${response.billOfLading1_b}</td>
                            <td>VESSEL FIGURE AFTER LOADING (MT)</td>
                            <td>${response.vesselFigAfterLoading1_b}</td>
                        </tr>
                        <tr>
                            <td>VESSEL ARRIVAL QUANTITIES (MT)</td>
                            <td>${response.arrivalQuantity1_b}</td>
                            <td>VESSEL ARRIVAL QUANTITIES (MT)</td>
                            <td>${response.arrivalQuantity2_b}</td>
                        </tr>
                        <tr>
                            <td>DIFFERENCE</td>
                            <td>${response.Difference1_b}</td>
                            <td>DIFFERENCE</td>
                            <td>${response.Difference2_b}</td>
                        </tr>
                        <tr>
                            <td>% DIFFERENCE</td>
                            <td>${response.DifferencePercent1_b}</td>
                            <td>% DIFFERENCE</td>
                            <td>${response.DifferencePercent2_b}</td>
                        </tr>

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