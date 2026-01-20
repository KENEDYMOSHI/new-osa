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

        <div class=" <?=($role == 3) ? 'col-md-6' : 'col-md-6'?> col-sm-12">
            <div class="form-group">
                <label for="my-select">Month</label>
                <select id="month" class="form-control" name="">
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
        <div class=" <?=($role == 3) ? 'col-md-6' : 'col-md-6'?> col-sm-12">
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
                    TOTAL COLLECTION TANZANIA<span id="totalInfo" style="margin-left:10px"></span>
                    <!-- <span> <i class="far fa-arrow-alt-down" style="color: red; margin-left:10px"></i><span
                            id="remainingTotal"></span></span> -->
                    <!-- <button type="button" onclick="fetchData()" class="btn btn-primary">Check</button> -->
                </div>
                <div class="card-body">
                    <div class="progress">
                        <div id="totalCollection" class="progress-bar progress-bar-striped" role="progressbar"><span
                                id="label"></span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>



    <div id="activities-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-center" id="regionTitle"></h5>
                    <button class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <table class="table ">
                        <thead>
                            <tr>
                                <th>Activity</th>
                                <th>Collected</th>
                                <th>Paid</th>
                                <th>Pending</th>
                                <th>Instruments</th>
                                <th>Graph <span style="visibility: hidden;">Graph</span> </th>
                            </tr>
                        </thead>
                        <tbody id="activityData">

                        </tbody>
                        <tfoot>

                        </tfoot>
                    </table>

                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>




    <?php if ($role == 3): ?>




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
                    <tbody id="data">


                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                Footer
            </div>
        </div>
    </div>
    <?php endif;?>


</div>
<!-- <button type="button" onclick="getData()">Click Me</button> -->
<script>
function calcPercentage(collected, target) {
    const percentage = ((collected / target) * 100)
    return Math.floor(percentage) + '%'
}

function getTotalCollection(month, year, location) {

    const regions = ['Arusha', 'Dar-es-salaam', 'Dodoma', 'Geita', 'Iringa', 'Kagera', 'Katavi', 'Kigoma',
        'Kilimanjaro', 'Lindi', 'Manyara', 'Mara', 'Mbeya', 'Morogoro', 'Mtwara', 'Mwanza', 'Njombe', 'Pwani',
        'Rukwa', 'Ruvuma', 'Shinyanga', 'Simiyu', 'Singida', 'Tabora', 'Tanga'
    ]

    // const regions = ['Geita', 'Iringa', 'Morogoro', 'Arusha']
    // const regions = ['Geita', 'Iringa', 'Morogoro', 'Arusha']




    $.ajax({
        type: "POST",
        url: "xxx",
        data: {
            month: month,
            year: year,
            location: location
        },
        dataType: "json",
        success: function(response) {
            $('#data').html('')
            $('#regionsAndActivities').html('')
            console.log(response);
            // console.log(response[0]);

            const targets = response[0];
            renderTotalCollection(targets, response)
            regions.forEach(region => {

                const vtcInRegion = response.filter(data => data.region === region).filter(data =>
                    data.activity === 'vtc').map(data => +data.amount).reduce((a, b) => a + b,
                    0)
                const sblInRegion = response.filter(data => data.region === region).filter(data =>
                    data.activity === 'sbl').map(data => +data.amount).reduce((a, b) => a + b,
                    0)

                // const waterMeterInRegion = response.filter(data => data.region === region).filter(
                //     data =>
                //     data.activity === 'waterMeter').map(data => +data.amount).reduce((a, b) =>
                //     a + b,
                //     0)




                const target = targets.filter(data => data.region === region).map(data => data
                    .targetAmount)

                // console.log(vtcInRegion);

                // if (vtcInRegion != 0 && sblInRegion != 0) {
                // console.log('Total IN ' + region + ' : ' + parseInt(vtcInRegion + sblInRegion));
                // console.log('VTC ' + region + ' : ' + vtcInRegion + 'Target' + target);
                // console.log('SBL ' + region + ' : ' + sblInRegion + 'Target' + target);
                // console.log('WATER ' + region + ' : ' + waterMeterInRegion + 'Target' + target);






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




                if (target > 0) {
                    // console.log('Target: ' + target + ' in ' + region);

                }
                if (sum != 0) {
                    const percentage = calcPercentage(sum, target)

                    // console.log(region + ' Total = ' + sum + ' Paid = ' + paidAmount +
                    //     ' Pending = ' + pendingAmount + ' Instruments = ' + instruments +
                    //     ' Target ' + target + ' Percentage ' + percentage);


                    $('#data').append(
                        `
                            <tr>
                            <td> ${region}</td>
                            <td>Tsh ${sum} of  ${target} </td>
                            <td> Tsh ${paidAmount}</td>
                            <td>Tsh  ${pendingAmount}</td>
                            <td> ${instruments}</td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar bg-primary" style="width:${percentage}" role="progressbar"
                                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">${percentage}</div>
                                </div>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-success" onclick="viewActivities('${region}')">
                                <i class="far fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                            `
                    )

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


function viewActivities(region) {
    const month = $('#month').val()
    const year = $('#collectionYear').val()

    console.log(region);
    console.log(month);
    console.log(year);



    $.ajax({
        type: "POST",
        url: "activitiesInRegion",
        data: {
            month: month,
            year: year,
            region: region

        },
        dataType: "json",
        success: function(response) {
            console.log(response);
            console.log(response[0].region);

            $('#activityData').html('')

            const regionTitle = response[0].region.toUpperCase()


            const vtcTotal = response.filter(data => data.activity == 'vtc').map(data => +data.amount)
                .reduce((a, b) => a + b, 0)
            const vtcTarget = response.filter(data => data.activity == 'vtc').map(data => +data.target)[0]
            const vtcInstrumentsTarget = response.filter(data => data.activity == 'vtc').map(data => +data
                .instrumentsTarget)[0]

            const vtcPercentage = Math.floor((vtcTotal / vtcTarget) * 100)


            const vtcPaid = response.filter(data => data.activity == 'vtc').filter(data => data.payment ==
                    'Paid').map(data => +data.amount)
                .reduce((a, b) => a + b, 0)
            const vtcPending = response.filter(data => data.activity == 'vtc').filter(data => data
                    .payment ==
                    'Pending').map(data => +data.amount)
                .reduce((a, b) => a + b, 0)

            const vtcQuantity = response.filter(data => data.activity == 'vtc').map(data => data
                .instruments)[0]
            //=================***************************====================

            const sblTotal = response.filter(data => data.activity == 'sbl').map(data => +data.amount)
                .reduce((a, b) => a + b, 0)
            const sblTarget = response.filter(data => data.activity == 'sbl').map(data => +data.target)[0]
            const sblInstrumentsTarget = response.filter(data => data.activity == 'sbl').map(data => +data
                .instrumentsTarget)[0]

            const sblPercentage = Math.floor((sblTotal / sblTarget) * 100)


            const sblPaid = response.filter(data => data.activity == 'sbl').filter(data => data.payment ==
                    'Paid').map(data => +data.amount)
                .reduce((a, b) => a + b, 0)
            const sblPending = response.filter(data => data.activity == 'sbl').filter(data => data
                    .payment ==
                    'Pending').map(data => +data.amount)
                .reduce((a, b) => a + b, 0)

            const sblQuantity = response.filter(data => data.activity == 'sbl').map(data => data
                .instruments)[0]
            //=================***************************====================

            const waterMeterTotal = response.filter(data => data.activity == 'waterMeter').map(data => +data
                    .amount)
                .reduce((a, b) => a + b, 0)
            const waterMeterTarget = response.filter(data => data.activity == 'waterMeter').map(data => +
                data.target)[0]
            const waterMeterInstrumentsTarget = response.filter(data => data.activity == 'waterMeter').map(
                data => +data
                .instrumentsTarget)[0]

            const waterMeterPercentage = Math.floor((waterMeterTotal / waterMeterTarget) * 100)


            const waterMeterPaid = response.filter(data => data.activity == 'waterMeter').filter(data =>
                    data.payment ==
                    'Paid').map(data => +data.amount)
                .reduce((a, b) => a + b, 0)
            const waterMeterPending = response.filter(data => data.activity == 'waterMeter').filter(data =>
                    data
                    .payment ==
                    'Pending').map(data => +data.amount)
                .reduce((a, b) => a + b, 0)

            const waterMeterQuantity = response.filter(data => data.activity == 'waterMeter').map(data =>
                data
                .instruments)[0]


            function cleanValue(variable) {
                if (variable === undefined || isNaN(variable) || variable === '') {
                    return 0
                } else {
                    return variable
                }
            }

            function cleanPercentage(variable) {
                if (isNaN(variable) || variable == undefined) {
                    return 0 + '%'
                } else {
                    return variable + '%'
                }
            }


            // console.log('TOTAL :' + vtcTotal);
            // console.log('PAID :' + vtcPaid);
            // console.log('PENDING :' + vtcPending);
            $('#regionTitle').html(regionTitle)
            $('#activityData').append(`
                 <tr>
                     <td>Vehicle Tank Verification</td>
                     <td> Tsh ${formatNumber(vtcTotal)} Of ${formatNumber(cleanValue(vtcTarget))}</td>
                     <td>Tsh ${formatNumber(vtcPaid)}</td>
                     <td>Tsh ${formatNumber(vtcPending)}</td>
                     <td>${cleanValue(vtcQuantity)} Of ${cleanValue(vtcInstrumentsTarget)}</td>
                     <td>
                         <div class="progress">
                             <div class="progress-bar progress-bar-striped bg-primary" style="width:${cleanValue(vtcPercentage)+'%'} "
                                 role="progressbar">
                                 ${cleanValue(vtcPercentage)+'%'}</div>
                         </div>
                     </td>
                 </tr>
                 <tr>
                     <td>Sandy & Ballast Lories</td>
                     <td> Tsh ${formatNumber(sblTotal)} Of ${formatNumber(cleanValue(sblTarget))}</td>
                     <td>Tsh ${formatNumber(sblPaid)}</td>
                     <td>Tsh ${formatNumber(sblPending)}</td>
                     <td>${cleanValue(sblQuantity)} Of ${cleanValue(sblInstrumentsTarget)}</td>
                     <td>
                         <div class="progress">
                             <div class="progress-bar progress-bar-striped bg-primary" style="width: ${cleanPercentage(sblPercentage)}"
                                 role="progressbar">
                               ${cleanPercentage(sblPercentage)}</div>
                         </div>
                     </td>
                 </tr>
                 <tr>
                     <td>Meters</td>
                     <td> Tsh ${formatNumber(waterMeterTotal)} Of ${formatNumber(cleanValue(waterMeterTarget))}</td>
                     <td>Tsh ${formatNumber(waterMeterPaid)}</td>
                     <td>Tsh ${formatNumber(waterMeterPending)}</td>
                     <td>${cleanValue(waterMeterQuantity)} Of ${cleanValue(waterMeterInstrumentsTarget)}</td>
                     <td>
                         <div class="progress">
                             <div class="progress-bar progress-bar-striped bg-primary" style="width:${cleanPercentage(waterMeterPercentage)} "
                                 role="progressbar">
                                 ${cleanPercentage(waterMeterPercentage)}</div>
                         </div>
                     </td>
                 </tr>
              `)



            $('#activities-modal').modal({
                show: true,
                backdrop: 'static'

            })
        }

    });
}

//preSelected(collectionMonth.value, collectionYear.value)
const collectionMonth = document.querySelector('#month')
const collectionYear = document.querySelector('#collectionYear')
const collectionRegion = document.querySelector('#region')

collectionMonth.addEventListener('change', (e) => {
    const month = e.target.value
    const year = collectionYear.value
    // const location = collectionRegion.value

    // console.log([month, year, location]);
    getTotalCollection(month, year)


})

//=====================================
collectionYear.addEventListener('change', (e) => {
    const month = collectionMonth.value
    const year = e.target.value
    // const collectionRegion = collectionRegion.value
    getTotalCollection(month, year)


})


function preSelected(month, year) {
    getTotalCollection(month, year)

}



function getTotalCollectionXX(month, year) {

    $.ajax({
        type: "GET",
        url: "getActivityCollection",
        data: {
            month: collectionMonth.value,
            year: collectionYear.value,
            // collectionRegion: collectionRegion.value,
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





function renderTotalCollection(targets, collectionData) {

    const target = targets.map(data => +data.targetAmount).reduce((a, b) => a + b, 0)
    const collectedAmount = collectionData.slice(1).map(data => parseInt(data.amount)).reduce((a, b) => a + b, 0)

    let percentage = 0

    if (target == 0 && collectedAmount == 0) {
        percentage = 0
    } else {

        percentage = ((collectedAmount / target) * 100).toFixed(0)
    }


    $('#totalCollection').css('width', percentage + '%')
    $('#totalInfo').html(` ( Tsh <b>${formatNumber(collectedAmount)}</b>   Of  Tsh <b>${formatNumber(target)}</b>)`)
    $('#label').html(percentage + '%')




    console.log('PERCENT ' + percentage);
    console.log('TARGET IS ' + target);
    console.log('WE GOT ' + collectedAmount);






}
</script>


<?=$this->endSection();?>