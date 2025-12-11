<?=$this->extend('Layouts/coreLayout');?>
<?=$this->section('content');?>

<script>
function getTheShipId(id) {
    const printBtn = document.querySelector('#printDocument')
    printBtn.setAttribute('href', '<?=base_url()?>/downloadPortDocsPDF/' + id)
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
        <!-- <button type="button" class="btn btn-info" data-toggle="modal" data-target="#documents-modal"
            aria-pressed="false" autocomplete="off">Log</button> -->



        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <div id="selectedShip">

                        </div>
                    </div>
                    <div class="col-md-4">

                        <a href="" id="preview" class="btn btn-success btn-sm pull-right" data-toggle="modal"
                            data-target="#doc-modal" onclick="getShipDocuments()"> <i class="far fa-eye"></i>
                            Preview</a>
                    </div>

                </div>
            </div>
            <div class="card-body">
                <div class="form-group">

                    <input id="shipId" class="form-control" type="number" hidden>
                </div>

                <div class="icheck-primary">
                    <input type="checkbox" id="checkboxPrimary2All" class="allDocuments">
                    <label for="checkboxPrimary2All">
                        <h6>All Documents Available</h6>
                    </label>
                </div>
                <hr>
                <div class="icheck-primary">
                    <input type="checkbox" id="checkboxPrimary2" class="StowagePlan ">
                    <label for="checkboxPrimary2">
                        <h6>Stowage Plan</h6>
                    </label>
                </div>
                <div class="icheck-primary ">
                    <input type="checkbox" id="checkboxPrimary3" class="ShipParticulars ">
                    <label for="checkboxPrimary3">
                        <h6>Ship Particulars</h6>
                    </label>
                </div>
                <div class="icheck-primary ">
                    <input type="checkbox" id="checkboxPrimary4" class="TankCalibrationCertificate ">
                    <label for="checkboxPrimary4">
                        <h6>Tank Calibration Certificate</h6>
                    </label>
                </div>
                <div class="icheck-primary ">
                    <input type="checkbox" id="checkboxPrimary5" class="BillOfLading">
                    <label for="checkboxPrimary5">
                        <h6>Bill Of Lading</h6>
                    </label>
                </div>
                <div class="icheck-primary ">
                    <input type="checkbox" id="checkboxPrimary6" class="CargoManifest">
                    <label for="checkboxPrimary6">
                        <h6>Cargo Manifest</h6>
                    </label>
                </div>
                <div class="icheck-primary ">
                    <input type="checkbox" id="checkboxPrimary7" class="UllageReportOfLoadingPorts">
                    <label for="checkboxPrimary7">
                        <h6>Ullage Report OfLoading Ports</h6>
                    </label>
                </div>
                <div class="icheck-primary ">
                    <input type="checkbox" id="checkboxPrimary8"
                        class="UllageTemperatureInterfaceCalibrationCertificate">
                    <label for="checkboxPrimary8">
                        <h6>Ullage Temperature Interface Calibration Certificate</h6>
                    </label>
                </div>

                <div class="icheck-primary">
                    <input type="checkbox" id="checkboxPrimary9" class="CertificateOfQuantity">
                    <label for="checkboxPrimary9">
                        <h6>Certificate Of Quantity</h6>
                    </label>
                </div>
                <div class="icheck-primary">
                    <input type="checkbox" id="checkboxPrimary20" class="CertificateOfQuality">
                    <label for="checkboxPrimary20">
                        <h6>Certificate OfQuality</h6>
                    </label>
                </div>

                <div class="icheck-primary ">
                    <input type="checkbox" id="checkboxPrimary22" class="LastArrivalPortBunker">
                    <label for="checkboxPrimary22">
                        <h6>Last Arrival Port Bunker</h6>
                    </label>
                </div>
                <div class="icheck-primary ">
                    <input type="checkbox" id="checkboxPrimary23" class="NoticeOfReadinessSignedByCargoReceiver">
                    <label for="checkboxPrimary23">
                        <h6>Notice Of Readiness Signed By CargoReceiver</h6>
                    </label>
                </div>
                <div class="icheck-primary ">
                    <input type="checkbox" id="checkboxPrimary24" class="VesselExperienceFactor">
                    <label for="checkboxPrimary24">
                        <h6>Vessel Experience Factor</h6>
                    </label>
                </div>

                <div class="icheck-primary ">
                    <input type="checkbox" id="checkboxPrimary25" class="CargoDischargingOrder">
                    <label for="checkboxPrimary25">
                        <h6>Cargo Discharging Order</h6>
                    </label>
                </div>
                <div class="icheck-primary ">
                    <input type="checkbox" id="checkboxPrimary26" class="CertificateOfOrigin">
                    <label for="checkboxPrimary26">
                        <h6>Certificate Of Origin</h6>
                    </label>
                </div>
            </div>
            <div class="card-footer">

                <button type="button" class="btn btn-primary btn-sm pull-right" id="docSaveBtn">Save</button>


            </div>
        </div>

        <div id="doc-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title"
            aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">

                        <button class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="row">
                        <div class="col-12" id="">
                            <!-- Main content -->
                            <div class="invoice p-3 mb-3">
                                <!-- title row -->
                                <div class="row mb-5" id="demoReport">
                                    <div class="col-md-4"> <img src="<?=base_url()?>/assets/images/emblem.png" alt="">
                                    </div>
                                    <div class="col-md-4">
                                        <h5 class="text-center"><b>THE UNITED REPUBLIC OF TANZANIA</b></h5>
                                        <h5 class="text-center"><b>MINISTRY OF INDUSTRY AND TRADE </b></h5>
                                        <h5 class="text-center">WEIGHTS AND MEASURES AGENCY </h5>
                                        <h5 class="text-center">PORTS UNIT</h5>
                                        <p class="text-center"><?=date("d M Y")?></p>

                                    </div>
                                    <div class="col-md-4 align-right"><img class="float-right"
                                            src="<?=base_url()?>/assets/images/wma1.png" alt="">
                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- info row -->

                                <!-- /.row -->

                                <!-- Table row -->
                                <h4 class="selectedVessel"></h4>

                                <div class="row">
                                    <div class="col-12 table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>

                                                    <th>SHIP DOCUMENTS</th>
                                                    <th>REMARK</th>
                                                    <th>APPENDIX</th>


                                                </tr>
                                            </thead>
                                            <tbody id="documents">
                                                <tr>


                                                </tr>


                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- /.row -->

                                <!-- /.row -->

                                <!-- this row will not appear when printing -->
                                <div class="row no-print">
                                    <div class="col-12">
                                        <a id="printDocument" target="_blank" class="btn btn-success pull-right"><i
                                                class="far fa-download"></i>
                                            Download</a>

                                    </div>
                                </div>
                            </div>
                            <!-- /.invoice -->
                        </div><!-- /.col -->
                    </div>
                    <div class="modal-footer">

                        Footer
                    </div>
                </div>
            </div>
        </div>



    </div><!-- /.container-fluid -->
    <script>
    //=================initialize a time log====================

    // function printPdf() {

    // }


    const getDocumentValue = (value) => {
        return document.querySelector(value)
    }

    function getShipDocuments() {
        const shipId = getDocumentValue('#shipId').value
        const printBtn = document.querySelector('#printDocument')
        printBtn.setAttribute('href', '<?=base_url()?>/downloadPortDocsPDF/' + shipId)
        selectShipDocuments(shipId)

    }


    function selectShipDocuments(id) {
        // const shipId = getDocumentValue('#shipId')

        function checkValue(val) {
            if (val == '1') {
                return '<img src="<?=base_url()?>/assets/check.png" alt="" width="25px">'
            } else if (val == '0') {
                return '<img src="<?=base_url()?>/assets/cancel.png" alt="" width="25px">'
            }
        }
        $.ajax({
            type: "POST",
            url: "selectShipDocuments",
            data: {
                id: id
            },
            dataType: "json",
            success: function(response) {

                const output = getDocumentValue('#documents')


                const template = `
                <tr>
                   <td>STOWAGE PLAN</td>
                   <td>${checkValue(response.StowagePlan)}</td>
                   <td></td>
                 </tr>
                <tr>
                   <td>SHIP PARTICULARS</td>
                   <td>${checkValue(response.ShipParticulars)}</td>
                   <td></td>
                 </tr>
                <tr>
                   <td>TANKS CALIBRATION CERTIFICATE</td>
                   <td>${checkValue(response.TankCalibrationCertificate)}</td>
                   <td></td>
                 </tr>
                <tr>
                   <td>BILL OF LADING</td>
                   <td>${checkValue(response.BillOfLading)}</td>
                   <td></td>
                 </tr>

                   <td>CARGO MANIFEST</td>
                   <td>${checkValue(response.CargoManifest)}</td>
                   <td></td>
                 </tr>
                <tr>
                   <td>ULLAGE REPORT OF THE LAST LOADING POSTS</td>
                   <td>${checkValue(response.UllageReportOfLoadingPorts)}</td>
                   <td></td>
                 </tr>
                <tr>
                   <td>ULLAGE TEMPERATURE INTERFACE CALIBRATION CERTIFICATE</td>
                   <td>${checkValue(response.UllageTemperatureInterfaceCalibrationCertificate)}</td>
                   <td></td>
                 </tr>
                <tr>
                   <td>CERTIFICATES OF QUANTITY</td>
                   <td>${checkValue(response.CertificateOfQuantity)}</td>
                   <td></td>
                 </tr>
                <tr>
                   <td>CERTIFICATES OF QUALITY</td>
                   <td>${checkValue(response.CertificateOfQuality)}</td>
                   <td></td>
                 </tr>
                <tr>
                   <td>NOTICES OF READINESS SIGNED BY CARGO RECEIVER</td>
                   <td>${checkValue(response.NoticeOfReadinessSignedByCargoReceiver)}</td>
                   <td></td>
                 </tr>
                <tr>
                   <td>VESSEL EXPERIENCE FACTOR(V.E.F)</td>
                   <td>${checkValue(response.VesselExperienceFactor)}</td>
                   <td></td>
                 </tr>
                <tr>
                   <td>LAST/ ARRIVAL PORT BUNKER</td>
                   <td>${checkValue(response.LastArrivalPortBunker)}</td>
                   <td></td>
                 </tr>
                <tr>
                   <td>DISCHARGING ORDER/INSTRUCTION</td>
                   <td>${checkValue(response.CargoDischargingOrder)}</td>
                   <td></td>
                 </tr>
                <tr>
                   <td>CERTIFICATE OF ORIGIN</td>
                   <td>${checkValue(response.CertificateOfOrigin)}</td>
                   <td></td>
                 </tr>
                `
                if (response == 'empty') {
                    output.innerHTML = '<h3 class="text-center">No Documents</h3>'
                } else {

                    output.innerHTML = template
                }

            }
        });
    }
    //=================All documents selection====================
    function selectAllDocuments() {

        const checkAll = getDocumentValue('.allDocuments');

        checkAll.addEventListener('click', () => {
            const allCheckBoxes = document.querySelectorAll("input[type='checkbox']")
            if (checkAll.checked == true) {


                for (let checkbox of allCheckBoxes) {

                    checkbox.setAttribute('checked', true)

                }
            } else if (checkAll.checked == false) {

                for (let checkbox of allCheckBoxes) {

                    checkbox.removeAttribute('checked', true)


                }
            }

        })

    }
    selectAllDocuments()

    function uncheckAll() {
        const allCheckBoxes = document.querySelectorAll('input[type="checkbox"]')

        for (let chk of allCheckBoxes) {
            if (chk.checked == true) {
                chk.checked = false
            }

        }
    }

    const docSaveBtn = document.querySelector('#docSaveBtn');

    docSaveBtn.addEventListener('click', e => {
        e.preventDefault()
        // const customerHash = getDocumentValue('#customerHash')
        const shipId = getDocumentValue('#shipId')
        const StowagePlan = getDocumentValue('.StowagePlan')
        const ShipParticulars = getDocumentValue('.ShipParticulars')
        const TankCalibrationCertificate = getDocumentValue('.TankCalibrationCertificate')
        const BillOfLading = getDocumentValue('.BillOfLading')
        const CargoManifest = getDocumentValue('.CargoManifest')
        const UllageReportOfLoadingPorts = getDocumentValue('.UllageReportOfLoadingPorts')
        const UllageTemperatureInterfaceCalibrationCertificate = getDocumentValue(
            '.UllageTemperatureInterfaceCalibrationCertificate')
        const CertificateOfQuantity = getDocumentValue('.CertificateOfQuantity')
        const CertificateOfQuality = getDocumentValue('.CertificateOfQuality')
        const LastArrivalPortBunker = getDocumentValue('.LastArrivalPortBunker')
        const NoticeOfReadinessSignedByCargoReceiver = getDocumentValue(
            '.NoticeOfReadinessSignedByCargoReceiver')
        const VesselExperienceFactor = getDocumentValue('.VesselExperienceFactor')
        const CargoDischargingOrder = getDocumentValue('.CargoDischargingOrder')
        const CertificateOfOrigin = getDocumentValue('.CertificateOfOrigin')
        const region = getDocumentValue('.region')

        function itemIsChecked(item) {
            if (item.checked) {
                return item.value = 1
            } else {
                return item.value = 0
            }
        }





        $.ajax({
            type: "POST",
            url: "saveShipDocumentsInfo",
            data: {
                //customerHash: customerHash.value,
                shipId: shipId.value,
                StowagePlan: itemIsChecked(StowagePlan),
                ShipParticulars: itemIsChecked(ShipParticulars),
                TankCalibrationCertificate: itemIsChecked(TankCalibrationCertificate),
                BillOfLading: itemIsChecked(BillOfLading),
                CargoManifest: itemIsChecked(CargoManifest),
                UllageReportOfLoadingPorts: itemIsChecked(UllageReportOfLoadingPorts),
                UllageTemperatureInterfaceCalibrationCertificate: itemIsChecked(
                    UllageTemperatureInterfaceCalibrationCertificate),
                CertificateOfQuantity: itemIsChecked(CertificateOfQuantity),
                CertificateOfQuality: itemIsChecked(CertificateOfQuality),
                LastArrivalPortBunker: itemIsChecked(LastArrivalPortBunker),
                NoticeOfReadinessSignedByCargoReceiver: itemIsChecked(
                    NoticeOfReadinessSignedByCargoReceiver),
                VesselExperienceFactor: itemIsChecked(VesselExperienceFactor),
                CargoDischargingOrder: itemIsChecked(CargoDischargingOrder),
                CertificateOfOrigin: itemIsChecked(CertificateOfOrigin),



            },
            dataType: "json",
            success: function(response) {


                // console.table(response)
                if (response == 'Added') {
                    uncheckAll()
                    swal({
                        title: 'Document Info Saved',
                        // text: "You clicked the button!",
                        icon: "success",
                        button: "Ok",
                    });

                    //grabLastMeter()
                } else {
                    swal({
                        title: 'Something Went Wrong!',
                        // text: "You clicked the button!",
                        icon: "error",
                        button: "Ok",
                    });
                }
            }
        });




    })
    </script>
</section>
<?=$this->endSection();?>