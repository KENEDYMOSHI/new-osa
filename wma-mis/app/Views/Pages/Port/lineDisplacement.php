<?=$this->extend('Layouts/coreLayout');?>
<?=$this->section('content');?>
<script>
const downloadPdf = (shipId) => {
    const downloadBtn = document.querySelector('#downloadLineDisplacement')
    downloadBtn.setAttribute('href', '<?=base_url()?>/downloadLineDisplacement/' + shipId)
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
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#discharge-modal"
                    aria-pressed="false" autocomplete="off"><i class="far fa-plus-circle" aria-hidden="true"></i>Add
                    Figures</button>

                <button type="button" onclick="getLineDisplacement()" class="btn btn-success btn-sm" id=""><i
                        class="far fa-eye" aria-hidden="true"></i> View</button>

                <h4 id="selectedShip"></h4>
            </div>
            <div class="card-body">






                <div id="LineDisplacement" style="display:none">
                    <table class="table table-bordered">
                        <thead class="">
                            <tr>
                                <th colspan="9" class="text-center">LINE DISPLACEMENT DIFFERENCES</th>
                            </tr>
                            <tr>
                                <th rowspan="2">Terminal</th>
                                <th colspan="2">SHIP FIGURE</th>
                                <th colspan="2">SHORE FIGURE</th>
                                <th colspan="2">DIFFERENCE</th>
                                <th colspan="2">DIFFERENCE</th>
                            </tr>
                            <tr>
                                <th colspan="2">Discharged Quantity</th>
                                <th colspan="2">Received Quantity</th>
                                <th colspan="2">Received Qty Vs Discharged Qty</th>
                                <th colspan="2">Received Qty Vs Discharged Qty</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th>M/TONS</th>
                                <th>VOL @ 20&deg; C</th>
                                <th>M/TONS</th>
                                <th>VOL @ 20&deg; C</th>

                                <th>M/TONS</th>
                                <th>% DIFF</th>

                                <th>VOL @ 20&deg; C</th>
                                <th>% DIFF</th>

                            </tr>
                        </thead>

                        <tbody id="lineDetails">



                        </tbody>



                    </table>

                </div>


            </div>
            <div class="card-footer">
                <a id="downloadLineDisplacement" target="_blank" class="btn btn-success btn-sm"><i
                        class="far fa-download" aria-hidden="true"></i>Download</a>
            </div>
        </div>


        <div id="discharge-modal" class="modal fade " tabindex="-1" role="dialog" aria-labelledby="my-modal-title"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="my-modal-title">ADD LINE DISPLACEMENT</h5>
                        <button class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input id="shipId" class="form-control" type="number" hidden>

                        <div class="card">
                            <div class="card-header">
                                SHIP FIGURES
                            </div>
                            <div class="card-body">
                                <p>Discharged Quantity</p>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="my-input">Metric Tons</label>
                                        <input id="shipMetricTons" class="form-control" type="number"
                                            placeholder="Enter Metric Tons" data-clear>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="my-input">Volume</label>
                                        <input id="shipVolume" class="form-control" type="number"
                                            placeholder="Enter Volume" data-clear>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                SHORE FIGURES
                            </div>
                            <div class="card-body">
                                <p>Received Quantity</p>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="my-input">Metric Tons</label>
                                        <input id="shoreMetricTons" class="form-control" type="number"
                                            placeholder="Enter Metric Tons"
                                            oninput="calcMetricTonsDifference(this.value)" data-clear>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="my-input">Volume</label>
                                        <input id="shoreVolume" class="form-control" type="number"
                                            placeholder="Enter Volume" oninput="calcVolumeDifference(this.value)"
                                            data-clear>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                DIFFERENCE (METRIC TONS)
                            </div>
                            <div class="card-body">
                                <!-- <p>Received Quantity Vs Discharged Quantity</p> -->
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="my-input">Difference Metric Tons</label>
                                        <input id="metricTonsDifference" class="form-control" type="number"
                                            placeholder="" readonly data-clear>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="my-input">Difference in %</label>
                                        <input id="metricTonsPercentage" class="form-control" type="number"
                                            placeholder="" readonly data-clear>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                DIFFERENCE (VOLUME)
                            </div>
                            <div class="card-body">

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="my-input">Difference Volume</label>
                                        <input id="volumeDifference" class="form-control" type="number" placeholder=""
                                            readonly data-clear>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="my-input">Difference in %</label>
                                        <input id="volumePercentage" class="form-control" type="number" placeholder=""
                                            readonly data-clear>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="modal-footer">
                            <button type="button" id="saveLineDisplacement" class="btn btn-primary btn-sm">Save</button>
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

        //=================get metric tons difference====================

        const shipMetricTons = document.querySelector('#shipMetricTons')
        const shoreMetricTons = document.querySelector('#shoreMetricTons')
        const metricTonsDifference = document.querySelector('#metricTonsDifference')
        const metricTonsPercentage = document.querySelector('#metricTonsPercentage')

        function calcMetricTonsDifference(shoreMetricTons) {
            const tonsDifference = +shoreMetricTons - +shipMetricTons.value
            console.log(tonsDifference);
            metricTonsDifference.value = tonsDifference.toFixed(3)
            const tonsPercentage = (tonsDifference / +shipMetricTons.value) * 100
            metricTonsPercentage.value = tonsPercentage.toFixed(1)
        }


        //=================volume difference====================

        const shipVolume = document.querySelector('#shipVolume')
        const shoreVolume = document.querySelector('#shoreVolume')
        const volumeDifference = document.querySelector('#volumeDifference')
        const volumePercentage = document.querySelector('#volumePercentage')

        function calcVolumeDifference(shoreVolume) {
            const volDifference = +shoreVolume - +shipVolume.value
            console.log(volDifference);
            volumeDifference.value = volDifference.toFixed(3)
            const volPercentage = (volDifference / +shipVolume.value) * 100
            volumePercentage.value = volPercentage.toFixed(1)
        }






        const shipId = document.querySelector('#shipId')

        const saveLineDisplacement = document.querySelector('#saveLineDisplacement');
        saveLineDisplacement.addEventListener('click', (e) => {


            const tankNumber = document.querySelector('#tankNumber')
            const lineDisplacement = document.querySelector('#lineDisplacement')
            const timeFrom = document.querySelector('#timeFrom')
            const dateFrom = document.querySelector('#dateFrom')


            function validateInput(formInput) {

                if (formInput.value == '') {

                    formInput.style.border = '1px solid #ff6348'
                    return false
                } else {
                    formInput.style.border = '1px solid #2ed573'
                    return true
                }

            }


            if (validateInput(shipMetricTons) && validateInput(shoreMetricTons) && validateInput(
                    metricTonsDifference) &&
                validateInput(metricTonsPercentage) && validateInput(shipVolume) && validateInput(
                    shoreVolume) && validateInput(volumeDifference) && validateInput(volumePercentage)) {
                e.preventDefault()
                if (shipId.value == '') {
                    swal({
                        title: 'Please Select Ship First',
                        icon: "warning",
                        timer: 2500
                    });
                } else {


                    $.ajax({
                        type: "POST",
                        url: "addLineDisplacement",
                        dataType: "json",
                        data: {
                            shipId: shipId.value,
                            shipMetricTons: shipMetricTons.value,
                            shoreMetricTons: shoreMetricTons.value,
                            metricTonsDifference: metricTonsDifference.value,
                            metricTonsPercentage: metricTonsPercentage.value,
                            shipVolume: shipVolume.value,
                            shoreVolume: shoreVolume.value,
                            volumeDifference: volumeDifference.value,
                            volumePercentage: volumePercentage.value,

                        },
                        success: function(response) {


                            console.log(response)
                            if (response == 'Added') {
                                clearInputs()
                                getLineDisplacement()
                                downloadPdf(shipId.value)

                                $('#discharge-modal').modal('hide');

                                swal({
                                    title: 'Line Displacement Added',
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



        })

        function getLineDisplacement() {
            if (shipId.value == '') {
                swal({
                    title: 'Please Select Ship First',
                    icon: "warning",
                    timer: 2500
                });
            } else {
                $.ajax({
                    type: "POST",
                    url: "getLineDisplacement",
                    data: {
                        shipId: shipId.value
                    },
                    dataType: "text",
                    success: function(response) {
                        downloadPdf(shipId.value)
                        document.querySelector('#LineDisplacement').style.display = 'block'
                        // console.log(response);
                        document.querySelector('#lineDetails').innerHTML = response
                    }
                });
            }
        }
        </script>
</section>
<?=$this->endSection();?>