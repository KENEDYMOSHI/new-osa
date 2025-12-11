<?=$this->extend('Layouts/coreLayout');?>
<?=$this->section('content');?>
<script>
const fetchTheShipId = (id) => {
    const logDownload = document.querySelector('#downloadFile')
    logDownload.setAttribute('href', '<?=base_url()?>/downloadUllageB4Discharging/' + id)
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
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#timeLog-modal"
                    aria-pressed="false" autocomplete="off"><i class="far fa-plus-circle" aria-hidden="true"></i>Add
                    Tank </button>
                <button type="button" onclick="fetchAllShipUllageB4Discharge()" class="btn btn-success btn-sm"
                    id="refreshTimeLogs"><i class="far fa-sync" aria-hidden="true"></i> Compile All Tanks
                </button>

                <h4 id="selectedShip"></h4>
            </div>
            <div class="card-body">

                <table class="table">
                    <thead>
                        <tr>

                            <th>TANK NO.</th>
                            <th scope="col">CORRECTED ULLAGE(m)</th>
                            <th scope="col">OBSVD TEMP &deg;C</th>
                            <th scope="col">TOTAL OBSVD VOL(m<sup>3</sup>)</th>
                            <th>FREE WATER(m)</th>
                            <th>FREE WATER VOL(m<sup>3</sup>)</th>
                            <th scope="col">GROSS OBSVD VOL(m<sup>3</sup>)</th>
                            <th>V.C.F TABLE 54B @ 15&deg;C</th>
                            <th>G.S.V @15&deg;C (m<sup>3</sup>)</th>
                            <th>V.C.F TABLE 60B @ 20&deg;C</th>
                            <th>G.S.V @20&deg;C (m<sup>3</sup>)</th>
                            <!-- <th>ACTION</th> -->
                        </tr>
                    </thead>
                    <tbody id="currentUllageB4Discharging">


                    </tbody>
                </table>
                <br>

                <div class="standardTemperature">

                </div>



            </div>
            <div class="card-footer">
                <a id="downloadFile" target="_blank" class="btn btn-success btn-sm"><i class="far fa-download"
                        aria-hidden="true"></i>Download</a>
            </div>
        </div>


        <div id="timeLog-modal" class="modal fade " tabindex="-1" role="dialog" aria-labelledby="my-modal-title"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="my-modal-title">ADD TANK</h5>
                        <button class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">

                            <input id="shipId" class="form-control" type="text" hidden>
                        </div>
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="my-input">Tank Number</label>
                                <input id="tankNumber" class="form-control" type="text" placeholder="Tank Number"
                                    data-clear>
                            </div>
                            <div class="form-group col-6">
                                <label for="my-input">Corrected Ullage(m)</label>
                                <input type="number" class="form-control " id="correctedUllage"
                                    placeholder="Corrected Ullage" data-clear>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="my-textarea">Observed Temperature &deg;C</label>
                                <input type="number" id="observedTemperature" class="form-control"
                                    placeholder="Observed Temperature" data-clear>
                            </div>
                            <div class="form-group col-6">
                                <label for="my-textarea">Total Observed Volume (m<sup>3</sup>)</label>
                                <input type="number" id="totalObservedVolume" class="form-control"
                                    placeholder="Total Observed Volume" data-clear>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="my-textarea">Free Water (m)</label>
                                <input type="number" id="freeWater" class="form-control" placeholder="Free Water"
                                    data-clear>
                            </div>
                            <div class="form-group col-6">
                                <label for="my-textarea">Free Water Volume (m <sup>3</sup> )</label>
                                <input type="number" id="freeWaterVolume" class="form-control"
                                    placeholder="Free Water Volume" oninput="calculateGrossObservedVol(this.value)"
                                    data-clear>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="my-textarea">Gross Observed Volume (m<sup>3</sup>)</label>
                                <input type="number" id="grossObservedVolume" class="form-control"
                                    placeholder="Gross Observed Volume" readonly data-clear>
                            </div>

                            <div class="form-group col-6">
                                <label for="my-textarea">V.C.F Table 54B @ 15&deg;C</label>
                                <input type="number" id="VCF54B15Centigrade" class="form-control"
                                    placeholder="V.C.F Table 54B @ 15&deg;C"
                                    oninput="calculateGSV15Centigrade(this.value)" data-clear>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="my-textarea">G.S.V @15°C (m <sup>3</sup>)</label>
                                <input type="number" id="GSV15Centigrade" class="form-control"
                                    placeholder="G.S.V 54B @ 15&deg;C" readonly data-clear>
                            </div>
                            <div class="form-group col-6">
                                <label for="my-textarea">V.C.F Table 60B @ 20&deg;C</label>
                                <input type="number" id="VCF60B20Centigrade" class="form-control"
                                    placeholder="V.C.F Table 60B @ 20&deg;C"
                                    oninput="calculateGSV20Centigrade(this.value)" data-clear>
                            </div>
                        </div>
                        <div class="form-group ">
                            <label for="my-textarea">G.S.V @20°C (m <sup>3</sup>)</label>
                            <input type="number" id="GSV20Centigrade" class="form-control"
                                placeholder="G.S.V  @ 20&deg;C" readonly data-clear>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" id="saveTankDetails" class="btn btn-primary btn-sm">Save</button>
                    </div>
                </div>
            </div>
        </div>


    </div><!-- /.container-fluid -->

    <script>
    const shipId = document.querySelector('#shipId')

    function formatNumber(number) {
        return new Intl.NumberFormat().format(number)
    }

    //====================================================================
    const grossObservedVol = document.querySelector('#grossObservedVolume')

    function calculateGrossObservedVol(freeWater) {
        const totalObservedVol = document.querySelector('#totalObservedVolume')
        grossObservedVol.value = parseFloat(totalObservedVol.value) - parseFloat(freeWater)
    }

    function calculateGSV15Centigrade(VSF54B) {
        const GSV15Centigrade = document.querySelector('#GSV15Centigrade')
        GSV15Centigrade.value = parseFloat(grossObservedVol.value) * parseFloat(VSF54B)

    }

    function calculateGSV20Centigrade(VSF60B) {
        const GSV20Centigrade = document.querySelector('#GSV20Centigrade')
        GSV20Centigrade.value = parseFloat(grossObservedVol.value) * parseFloat(VSF60B)

    }
    //=====================================================================
    const saveTankDetails = document.querySelector('#saveTankDetails');

    saveTankDetails.addEventListener('click', (e) => {
        e.preventDefault()

        const getFormValue = (value) => {
            return document.querySelector(value)
        }

        // const shipId = getFormValue('#shipId')
        const tankNumber = getFormValue('#tankNumber')
        const correctedUllage = getFormValue('#correctedUllage')
        const observedTemperature = getFormValue('#observedTemperature')
        const totalObservedVolume = getFormValue('#totalObservedVolume')
        const freeWater = getFormValue('#freeWater')
        const freeWaterVolume = getFormValue('#freeWaterVolume')
        const grossObservedVolume = getFormValue('#grossObservedVolume')
        const VCF54B15Centigrade = getFormValue('#VCF54B15Centigrade')
        const GSV15Centigrade = getFormValue('#GSV15Centigrade')
        const VCF60B20Centigrade = getFormValue('#VCF60B20Centigrade')
        const GSV20Centigrade = getFormValue('#GSV20Centigrade')






        function validateInput(formInput) {

            if (formInput.value == '') {

                formInput.style.border = '1px solid #ff6348'
                return false
            } else {
                formInput.style.border = '1px solid #2ed573'
                return true
            }

        }

        if (validateInput(tankNumber) && validateInput(correctedUllage) && validateInput(observedTemperature) &&
            validateInput(totalObservedVolume) && validateInput(freeWater) && validateInput(freeWaterVolume) &&
            validateInput(grossObservedVolume) && validateInput(VCF54B15Centigrade) && validateInput(
                GSV15Centigrade) && validateInput(VCF60B20Centigrade) && validateInput(GSV20Centigrade)) {
            $.ajax({
                type: "POST",
                url: "addShipOilTank",
                data: {
                    shipId: shipId.value,
                    tankNumber: tankNumber.value,
                    correctedUllage: correctedUllage.value,
                    observedTemperature: observedTemperature.value,
                    totalObservedVolume: totalObservedVolume.value,
                    freeWater: freeWater.value,
                    freeWaterVolume: freeWaterVolume.value,
                    grossObservedVolume: grossObservedVolume.value,
                    VCF54B15Centigrade: VCF54B15Centigrade.value,
                    GSV15Centigrade: GSV15Centigrade.value,
                    VCF60B20Centigrade: VCF60B20Centigrade.value,
                    GSV20Centigrade: GSV20Centigrade.value,

                },
                dataType: "json",
                success: function(response) {


                    console.log(response)
                    if (response == 'Added') {
                        // grabLastLog()
                        clearInputs()
                        fetchAllShipUllageB4Discharge(shipId.value)
                        fetchTheShipId(shipId.value)
                        $('#timeLog-modal').modal('hide');

                        swal({
                            title: 'Ship Oil Tank Saved',
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
        }



    })

    function computeColumn(obsvdTemp, obsvdVol, freeWtr, freeWtrVol, grossObsvdVol, VCF54B, GSV15C, VCF60B, GSV20C) {


        $('#currentUllageB4Discharging').append(
            `
            <td></td>
            <td><b>TOTAL G.S.V</b></td>
            <td><b>${obsvdTemp.toFixed(3)}</b></td>
            <td><b>${obsvdVol.toFixed(3)}</b></td>
            <td><b>${freeWtr.toFixed(3)}</b></td>
            <td><b>${freeWtrVol.toFixed(3)}</b></td>
            <td><b>${grossObsvdVol.toFixed(3)}</b></td>
            <td><b>${VCF54B.toFixed(3)}</b></td>
            <td><b>${GSV15C.toFixed(3)}</b></td>
            <td><b>${VCF60B.toFixed(3)}</b></td>
            <td><b>${GSV20C.toFixed(3)}</b></td>

            `
        )
    }




    function fetchAllShipUllageB4Discharge() {
        $('.standardTemperature').html('')

        if (shipId.value != '') {



            let obsvdTemp = 0
            let obsvdVol = 0;
            let freeWtr = 0;
            let freeWtrVol = 0;
            let grossObsvdVol = 0;
            let VCF54B = 0;
            let GSV15C = 0;
            let VCF60B = 0;
            let GSV20C = 0;
            $('#currentUllageB4Discharging').html('')
            // const shipId = document.querySelector('#shipId')
            $.ajax({
                type: "POST",
                url: "getAvailableShipUllageB4Discharge",
                data: {
                    shipId: shipId.value
                },
                dataType: "json",
                success: function(response) {

                    console.log(response)


                    if (response != '') {




                        fetchTheShipId(shipId.value)
                        for (let tank of response) {
                            obsvdTemp += (parseFloat(tank.observedTemperature) / response.length)
                            obsvdVol += parseFloat(tank.totalObservedVolume)
                            freeWtr += parseFloat(tank.freeWater)
                            freeWtrVol += parseFloat(tank.freeWaterVolume)
                            grossObsvdVol += parseFloat(tank.grossObservedVolume)
                            VCF54B += parseFloat(tank.VCF54B15Centigrade)
                            GSV15C += parseFloat(tank.GSV15Centigrade)
                            VCF60B += parseFloat(tank.VCF60B20Centigrade)
                            GSV20C += parseFloat(tank.GSV20Centigrade)




                            $('#currentUllageB4Discharging').append(`
                <tr>

                    <td>${tank.tankNumber}</td>
                    <td>${tank.correctedUllage}</td>
                    <td>${tank.observedTemperature}</td>
                    <td>${tank.totalObservedVolume}</td>
                    <td>${tank.freeWater}</td>
                    <td>${tank.freeWaterVolume}</td>
                    <td>${tank.grossObservedVolume}</td>
                    <td>${tank.VCF54B15Centigrade}</td>
                    <td>${tank.GSV15Centigrade}</td>
                    <td>${tank.VCF60B20Centigrade}</td>
                    <td>${tank.GSV20Centigrade}</td>


                </tr>

                `)
                        }
                    } else {
                        //$('#currentUllageB4Discharging').html('<h4>Nothing Found</h4>')
                        //$('.ullageTable').html('')
                        // $('#currentUllageB4Discharging').html('<h4>Nothing Found</h4>')
                    }


                    computeColumn(obsvdTemp, obsvdVol, freeWtr, freeWtrVol, grossObsvdVol, VCF54B, GSV15C,
                        VCF60B, GSV20C)



                    const density15C = response[0].density_15C
                    const density20C = response[0].density_20C

                    const WCFT_15 = density15C - 0.0011
                    const WCFT_20 = density20C - 0.0011


                    $('.standardTemperature').append(`

                        <table border="1" class="ullageTable">

                    <tr>
                        <th colspan="2" class="text-center">PARTICULAR</th>
                        <th colspan="2" class="text-center">STANDARD TEMPERATURE</th>

                    </tr>
                    <tr>
                        <th colspan="2"></th>
                        <th class="text-center">15&deg;C</th>
                        <th class="text-center">20 &deg;C</th>

                    </tr>
                    <tr>

                        <td colspan="2"><b>TOTAL G.S.V (m<sup>3</sup>)</b></td>
                        <td id="GCF15C">${GSV15C.toFixed(3)}</td>
                        <td id="GCF20C">${GSV20C.toFixed(3)}</td>

                    </tr>
                    <tr>

                        <td colspan="2"><b>W.C.F.T-56/C</b></td>
                        <td>${density15C - 0.0011}</td>
                        <td>${density20C - 0.0011}</td>

                    </tr>
                    <td colspan="2"><b>Reference Density</b>
                    </td>
                        <td>${response[0].density_15C}</td>
                        <td>${response[0].density_20C}</td>
                    <tr>

                        <td colspan="2"><b>TOTAL QTY BEFORE DISCHARGING (MT)</b></td>
                        <td>${(GSV15C * WCFT_15).toFixed(3)}</td>
                        <td>${(GSV20C * WCFT_20).toFixed(3)}</td>


                    </tr>
                    <tr>

                        <td colspan="2"><b>VOLUME (L)</b></td>
                        <td id="GSV15C">${formatNumber(GSV15C.toFixed(3)*1000)}</td>
                        <td id="GSV20C">${formatNumber(GSV20C.toFixed(3)*1000)}</td>

                    </tr>
                </table>

                        `)

                    // $('#GCF15C').html(GSV15C.toFixed(3))
                    // $('#GCF20C').html(GSV20C.toFixed(3))

                    // $('#GSV15C').html(GSV15C.toFixed(3))
                    // $('#GSV20C').html(GSV20C.toFixed(3))




                }
            });
        } else {
            swal({
                position: 'top-end',
                icon: 'warning',
                title: 'Please Select Ship First',
                showConfirmButton: false,
                timer: 2500
            })
        }
    }


    function formatDate(dateInput) {
        const date = new Date(dateInput);
        const formattedDate = date.toLocaleDateString('en-GB', {
            day: 'numeric',
            month: 'short',
            year: 'numeric'
        }).replace(/ /g, '-');

        return formattedDate
    }
    //=================select all ullage b4 discharge====================
    </script>
</section>
<?=$this->endSection();?>