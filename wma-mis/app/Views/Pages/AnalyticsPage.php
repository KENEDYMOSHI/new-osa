<?=$this->extend('layouts/CoreLayout');?>
<?=$this->section('content');?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"><?=$page['heading']?></h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?=base_url()?>/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?=$page['heading']?></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<div class="container-fluid">
    <div class="row">
        <?php if ($role == 3): ?>
        <div class="col-md-4 col-sm-12">
            <label for="enableRegion">Region</label>
            <select id="region" class="form-control select2bs4">
                <option value="Tanzania">All Regions</option>
                <?php foreach (renderRegions() as $region): ?>
                <option value="<?=$region['region']?>"><?=$region['region']?></option>
                <?php endforeach;?>
            </select>

        </div>

        <?php endif;?>
        <div class=" <?=($role == 3 || $role ==7) ? 'col-md-4' : 'col-md-6'?> col-sm-12">
            <div class="form-group">
                <label for="my-select">Month</label>
                <select id="collectionMonth" class="form-control" name="">
                    <option value="0">All Months</option>
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
        <div class=" <?=($role == 3|| $role == 7) ? 'col-md-4' : 'col-md-6'?> col-sm-12">
            <div class="form-group">
                <label for="">Year</label>
                <select id="collectionYear" class="form-control" name="">
                    <option value="2021">2021</option>
                    <option value="2020">2020</option>
                    <option value="2019">2019</option>
                    <option value="2018">2018</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card elevation-1">
                <div class="card-header">
                    TOTAL COLLECTION <span id="totalInfo" style="margin-left:10px"></span>
                    <span> <i class="far fa-arrow-alt-down" style="color: red; margin-left:10px"></i><span
                            id="remainingTotal"></span></span>
                    <!-- <button type="button" onclick="fetchData()" class="btn btn-primary">Check</button> -->
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

    <?php if ($role == 3|| $role == 7): ?>




    <div class="regions">
        <div class="card">
            <div class="card-header">
                Regions
            </div>
            <div class="card-body">
                <table class="table ">
                    <thead>
                        <tr>
                            <th>Region</th>
                            <th>Collected</th>
                            <th>Paid</th>
                            <th>Pending</th>
                            <th>Instruments</th>
                            <th>Graph</th>
                            <th>View</th>


                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Arusha</td>
                            <td>Tsh 10,450,000 of 50,000,000 </td>
                            <td>Tsh 6,450,000</td>
                            <td>Tsh 4,450,000</td>
                            <td>150</td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar bg-primary" style="width: 45%" role="progressbar"
                                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">45%</div>
                                </div>
                            </td>
                            <td>
                                <i class="fas fa-eye"></i>
                            </td>
                        </tr>
                        <tr>
                            <td>Dodoma</td>
                            <td>Tsh 10,450,000 of 50,000,000 </td>
                            <td>Tsh 6,450,000</td>
                            <td>Tsh 4,450,000</td>
                            <td>400</td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar bg-primary" style="width: 60%" role="progressbar"
                                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">60%</div>
                                </div>
                            </td>
                            <td>
                                <i class="fas fa-eye"></i>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                Footer
            </div>
        </div>
    </div>
    <?php endif;?>

    <div class="row">
        <div class="col-md-4">
            <div class="card elevation-1">
                <div class="card-header">
                    VTV (<span id="vtcAmount"></span>)
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
        <div class="col-md-4">
            <div class="card elevation-1">
                <div class="card-header">
                    SBL (<span id="sblAmount"></span>)
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
        <div class="col-md-4">
            <div class="card elevation-1">
                <div class="card-header">
                    METER (<span id="waterMeterAmount"></span>)
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
<!-- <button type="button" onclick="getData()">Click Me</button> -->
<script>
function getData() {
    const regions = ['Arusha', 'Dar-es-Salaam', 'Dodoma', 'Geita', 'Iringa', 'Kagera', 'Katavi', 'Kigoma',
        'Kilimanjaro', 'Lindi', 'Manyara', 'Mara', 'Mbeya', 'Morogoro', 'Mtwara', 'Mwanza', 'Njombe', 'Pwani',
        'Rukwa', 'Ruvuma', 'Shinyanga', 'Simiyu', 'Singida', 'Tabora', 'Tanga'
    ]

    $.ajax({
        type: "GET",
        url: "xxx",
        // data: "data",
        dataType: "json",
        success: function(response) {
            console.log(response);
            regions.forEach(region => {

                const total = response.filter(data => data.region == region)
                    .map(data => parseInt(data.amount))
                const paid = response.filter(data => data.region == region && data.payment ==
                        'Paid')
                    .map(data => parseInt(data.amount))
                const pending = response.filter(data => data.region == region && data.payment ==
                        'Pending')
                    .map(data => parseInt(data.amount))

                const sum = total.reduce((next, accumulator) => next + accumulator, 0)
                const paidAmount = paid.reduce((next, accumulator) => next + accumulator, 0)
                const pendingAmount = pending.reduce((next, accumulator) => next + accumulator, 0)
                const instruments = response.filter(data => data.region == region)
                    .map(data => data.instruments)
                    .filter((item, index, self) => {
                        return self.indexOf(item) === index
                    }).reduce((next, accumulator) => next + accumulator, 0)

                if (sum != 0) {
                    console.log(region + ' Total = ' + sum + ' Paid = ' + paidAmount +
                        ' Pending = ' + pendingAmount + ' Instruments = ' + instruments);

                }

            })
            // let total = 0
            // const result = response.filter(data => data.region == 'Arusha');
            // console.log(result);

            // result.forEach(data => {
            //     total += parseInt(data.amount)
            // })

            // console.log(total);

        }
    });
}
const collectionMonth = document.querySelector('#collectionMonth')
const collectionYear = document.querySelector('#collectionYear')

preSelected(collectionMonth.value, collectionYear.value)

collectionMonth.addEventListener('change', (e) => {
    const month = e.target.value
    const year = collectionYear.value
    getTotalCollection(month, year)


})
collectionYear.addEventListener('change', (e) => {
    const month = collectionMonth.value
    const year = e.target.value
    getTotalCollection(month, year)


})


function preSelected(month, year) {
    getTotalCollection(month, year)

}



function getTotalCollection(month, year) {

    $.ajax({
        type: "POST",
        url: "getActivityCollection",
        data: {
            month: collectionMonth.value,
            year: collectionYear.value
        },
        dataType: "json",
        success: function(response) {
            console.log(response)
            renderTotalCollection(response.overall)
            renderVtcCollection(response.vtc)
            renderSblCollection(response.sbl)
            renderWaterMeterCollection(response.waterMeter)
        }
    });
}





function renderTotalCollection(data) {

    const collected = data.percentage;
    const totalInfo = document.querySelector('#totalInfo')
    const remainingTotal = document.querySelector('#remainingTotal')
    totalInfo.textContent = `(${data.collected} of ${data.overallTarget})`
    remainingTotal.textContent = 100 - data.percentage + '%'


    const totalCollection = document.querySelector('#totalCollection')
    const label = document.querySelector('#label')
    const arrow = document.querySelector('.fa-arrow-alt-down')
    label.textContent = collected + '% Collection'
    totalCollection.style.width = collected + '%'


    if (+collected > 100) {
        arrow.style.color = 'green'
        arrow.classList.remove('fa-arrow-alt-down')
        arrow.classList.add('fa-arrow-alt-up')
    }

}



function renderVtcCollection(data) {
    const percentage = parseInt(data.vtcPercentage)
    const progress = document.querySelector('#vtcProgress')
    const vtcLabel = document.querySelector('#vtcLabel')
    const vtcAmount = document.querySelector('#vtcAmount')

    const completed = document.querySelector('#vtcCompleted')
    progress.setAttribute('data-percentage', percentage)
    completed.textContent = percentage + ' %'
    vtcLabel.textContent = data.vtcRegisteredInstruments + ' Of  ' + data.vtcTargetInstruments + 'Vehicle(s)'
    vtcAmount.textContent = data.vtcCollectedAmount + ' Of ' + data.vtcTargetAmount
}

function renderSblCollection(data) {
    const percentage = parseInt(data.sblPercentage)
    const progress = document.querySelector('#sblProgress')
    const sblLabel = document.querySelector('#sblLabel')
    const sblAmount = document.querySelector('#sblAmount')

    const completed = document.querySelector('#sblCompleted')
    progress.setAttribute('data-percentage', percentage)
    completed.textContent = percentage + ' %'
    sblLabel.textContent = data.sblRegisteredInstruments + ' Of  ' + data.sblTargetInstruments + 'Vehicle(s)'
    sblAmount.textContent = data.sblCollectedAmount + ' Of ' + data.sblTargetAmount
}

function renderWaterMeterCollection(data) {
    const percentage = parseInt(data.waterMeterPercentage)
    const progress = document.querySelector('#waterMeterProgress')
    const waterMeterLabel = document.querySelector('#waterMeterLabel')
    const waterMeterAmount = document.querySelector('#waterMeterAmount')

    const completed = document.querySelector('#waterMeterCompleted')
    progress.setAttribute('data-percentage', percentage)
    completed.textContent = percentage + ' %'
    waterMeterLabel.textContent = data.waterMeterRegisteredInstruments + ' Meters'
    waterMeterLabel.textContent = data.waterMeterRegisteredInstruments + ' Of  ' + data.waterMeterTargetInstruments +
        'Meters'
    waterMeterAmount.textContent = data.waterMeterCollectedAmount + ' Of ' + data.waterMeterTargetAmount
}
</script>


<?=$this->endSection();?>