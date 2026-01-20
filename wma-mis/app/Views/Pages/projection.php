<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"><?= $page['heading'] ?></h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?= $page['heading'] ?></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<div class="container-fluid">
    <div class="row">
        <?php if ($role == 3 || $role == 7) : ?>
            <div class="col-md-4 col-sm-12">
                <label for="enableRegion">Region</label>
                <select id="region" class="form-control select2bs4">
                    <option value="Tanzania">All Regions</option>
                    <?php foreach (renderRegions() as $region) : ?>
                        <option value="<?= $region['region'] ?>"><?= $region['region'] ?></option>
                    <?php endforeach; ?>
                </select>

            </div>

        <?php endif; ?>
        <div class=" <?= ($role == 3 || $role == 7) ? 'col-md-4' : 'col-md-6' ?> col-sm-12">
            <div class="form-group">
                <label for="my-select">Month</label>
                <select id="month" class="form-control" name="">
                    <option value="0">Annually</option>
                    <option value="1">January</option>
                    <option value="2">February</option>
                    <option value="3">March</option>
                    <option value="4">April</option>
                    <option value="5">May</option>
                    <option value="6">June</option>
                    <option value="7">July</option>
                    <option value="8">August</option>
                    <option value="9">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>
            </div>
        </div>
        <div class=" <?= ($role == 3 || $role == 7) ? 'col-md-4' : 'col-md-6' ?> col-sm-12">
            <div class="form-group">
                <label for="">Year</label>
                <select id="year" class="form-control" name="">
                    <?php for ($i = date('Y'); $i >= 2016; $i--) : ?>
                        <option value="<?= $i ?>"><?= $i ?></option>
                    <?php endfor; ?>

                    <!-- <option value="2021">2021</option>
                    <option value="2020" >2020</option>
                    <option value="2019">2019</option>
                    <option value="2018">2018</option> -->
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <button type="button" onclick="getProgress()" class="btn btn-primary btn-sm mb-3">Generate Data</button>
            <div class="card elevation-1">
                <div class="card-header">
                    <b>TOTAL COLLECTION</b> <span id="totalInfo" style="margin-left:10px"></span>


                </div>
                <div class="card-body">
                    <div class="progress">
                        <div id="totalCollection" class="progress-bar" role="progressbar"><span id="label"></span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="card elevation-1">
                <div class="card-header">
                    <b>Vehicle Tank Verification</b>
                </div>
                <div class="card-body">
                    <div id="vtcProgress" class="the-progress mb-3">
                        <span class="the-progress-left">
                            <span class="the-progress-bar"></span>
                        </span>
                        <span class="the-progress-right">
                            <span class="the-progress-bar"></span>
                        </span>
                        <div class="the-progress-value">
                            <div>
                                <span id="vtcCompleted"></span>
                                <br>
                                <span>Collection</span>
                            </div>
                        </div>
                    </div>
                    <h5 class="text-center" id="vtcLabel"></h5>
                </div>


            </div>
        </div>
        <div class="col-md-3">
            <div class="card elevation-1">
                <div class="card-header">
                    <b>Sandy & Ballast Lorries</b>
                </div>
                <div class="card-body">
                    <div id="sblProgress" class="the-progress mb-3">
                        <span class="the-progress-left">
                            <span class="the-progress-bar"></span>
                        </span>
                        <span class="the-progress-right">
                            <span class="the-progress-bar"></span>
                        </span>
                        <div class="the-progress-value">
                            <div>
                                <span id="sblCompleted"></span>
                                <br>
                                <span>Collection</span>
                            </div>
                        </div>
                    </div>
                    <h5 class="text-center" id="sblLabel"></h5>
                </div>

            </div>
        </div>
        <div class="col-md-3">
            <div class="card elevation-1">
                <div class="card-header">
                    <b>Meters</b>
                </div>
                <div class="card-body">
                    <div id="waterMeterProgress" class="the-progress mb-3">
                        <span class="the-progress-left">
                            <span class="the-progress-bar"></span>
                        </span>
                        <span class="the-progress-right">
                            <span class="the-progress-bar"></span>
                        </span>
                        <div class="the-progress-value">
                            <div>
                                <span id="waterMeterCompleted"></span>
                                <br>
                                <span>Collection</span>
                            </div>
                        </div>
                    </div>
                    <h5 class="text-center" id="waterMeterLabel"></h5>
                </div>

            </div>
        </div>

    </div>




</div>

<script>
    function test() {
        console.log(4464);
    }

    function getProgress() {


        const queryData = {
            // csrf_hash: document.querySelector('.token').value,
            month: document.querySelector('#month').value,
            year: document.querySelector('#year').value,

        }

        <?php if ($role == 7 || $role == 3) : ?>
            queryData.region = document.querySelector('#region').value
        <?php endif; ?>

        //mapping collection data to calculate the percentage
        const dataScale = (current, miniCollection, maxCollection, miniPercent, maxPercent) => {
            const percent = (
                ((current - miniCollection) * (maxPercent - miniPercent)) / (maxCollection - miniCollection) + miniPercent
            );

            if (percent == 'Infinity' || isNaN(percent)) {

                return 0
            } else {

                return percent.toFixed(1)
            }



        };

        $.ajax({
            type: "POST",
            url: "xxx",
            data: queryData,
            dataType: "json",
            success: function(response) {
                // document.querySelector('.token').value = response.token
                console.log(response);



                renderProgressData(response)



            }
        });


        function renderProgressData(data) {
            const total = dataScale(data.total, 0, data.target, 0, 100)
            const vtc = dataScale(data.vtc.collectedAmount, 0, data.vtc.targetAmount, 0, 100)
            const sbl = dataScale(data.sbl.collectedAmount, 0, data.sbl.targetAmount, 0, 100)
            const waterMeter = dataScale(data.waterMeter.collectedAmount, 0, data.waterMeter.targetAmount, 0, 100)

            //=================overall====================
            const totalCollection = document.querySelector('#totalCollection')
            totalCollection.style.width = total + '%'
            document.querySelector('#label').textContent = total + '%'

            //=================VTC====================
            const vtcProgress = document.querySelector('#vtcProgress')
            const vtcLabel = document.querySelector('#vtcLabel')
            const vtcAmount = document.querySelector('#vtcAmount')

            const completed = document.querySelector('#vtcCompleted')
            vtcProgress.setAttribute('data-percentage', Math.round(vtc))
            completed.textContent = vtc + ' %'
            // vtcLabel.textContent = data.vtcRegisteredInstruments + ' Of  ' + data.vtcTargetInstruments + 'Vehicle(s)'
            // vtcAmount.textContent = data.vtcCollectedAmount + ' Of ' + data.vtcTargetAmount

            //=================SBL====================

            const sblProgress = document.querySelector('#sblProgress')
            const sblLabel = document.querySelector('#sblLabel')
            const sblAmount = document.querySelector('#sblAmount')

            const sblCompleted = document.querySelector('#sblCompleted')
            sblProgress.setAttribute('data-percentage', Math.round(sbl))
            sblCompleted.textContent = sbl + ' %'
            // sblLabel.textContent = data.sblRegisteredInstruments + ' Of  ' + data.sblTargetInstruments + 'Vehicle(s)'
            // sblAmount.textContent = data.sblCollectedAmount + ' Of ' + data.sblTargetAmount


            //=================METERS====================  

            const waterMeterProgress = document.querySelector('#waterMeterProgress')
            const waterMeterLabel = document.querySelector('#waterMeterLabel')
            const waterMeterAmount = document.querySelector('#waterMeterAmount')

            const waterMeterCompleted = document.querySelector('#waterMeterCompleted')
            waterMeterProgress.setAttribute('data-percentage', Math.round(waterMeter))
            waterMeterCompleted.textContent = waterMeter + ' %'
            // waterMeterLabel.textContent = data.waterMeterRegisteredInstruments + ' Meters'
            // waterMeterLabel.textContent = data.waterMeterRegisteredInstruments + ' Of  ' + data.waterMeterTargetInstruments +
            //     'Meters'
            // waterMeterAmount.textContent = data.waterMeterCollectedAmount + ' Of ' + data.waterMeterTargetAmount





        }
    }
</script>


<?= $this->endSection(); ?>