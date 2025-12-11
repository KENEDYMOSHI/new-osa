<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title>Shore Tank Measurement Data</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: sans-serif;
        font-size: 12px;
    }

    header {
        position: relative;
    }

    .wrapper {
        width: 90%;
        margin: 20px 40px;
        top: 0;

    }

    .logo-left {
        position: absolute;
        left: 50px;
    }

    .logo-right {
        position: absolute;
        right: 50px;
        top: 0;
    }

    .headings {
        text-align: center;
        line-height: 2;
        color: #3a3a3a;
    }



    .contacts {
        text-align: center;
    }

    .time {
        width: 100%;
        text-align: center;
    }

    .doc-footer {
        width: 100%;
        text-align: center;
    }

    h3 {
        text-align: center;
        text-decoration: underline;
    }

    #logOfFact table {
        margin: 0 auto;
        width: 100%;
        border-collapse: collapse;

    }

    #logOfFact table td {
        padding: 9px 12px;
    }

    #logOfFact table th {
        padding: 9px 2px;
    }

    .details {
        width: 100%;
        margin-bottom: 15px;
    }

    .form-check {
        transform: scale(1.7);
        color: #333;
    }

    .check-label {
        font-size: 16px;
    }

    .top {
        margin-bottom: 20px;
    }

    .top .checkWrapper {
        /* float: left; */
        margin-top: 5px
    }

    .text-center {
        text-align: center;
        margin-bottom: 5px;
    }

    #check {
        transform: scale(1.6);
        margin-right: 5px;
        margin-bottom: -15px;
    }
    </style>
</head>



<body>
    <header>
        <div class="wrapper">
            <div class="logo-left">
                <img src='data:image/jpeg;base64,<?=coatOfArm()?>' alt="">
            </div>
            <div class="headings">
                <h5><b>THE UNITED REPUBLIC OF TANZANIA</b></h5>
                <h5><b>MINISTRY OF INDUSTRY AND TRADE </b></h5>
                <h5>WEIGHTS AND MEASURES AGENCY </h5>
                <h5>PORTS UNIT</h5>

            </div>
            <div class="logo-right">
                <img src='data:image/jpeg;base64,<?=wmaLogo()?>' alt="">
            </div>
        </div>

    </header>
    <p class="contacts"> Tel: <?=$tank->phone_number?> Fax: <?=$tank->fax?> ,<?=$tank->postal_address?>, e-mail:
        <?=$tank->email?></p>
    <div class="wrapper">


        <h3>SHORE TANK MEASUREMENT DATA</h3>

        <?php
function filterSeal($sealsArr, $sealName)
{
    $sealList = '';
    foreach ($sealsArr as $seal) {
        if ($seal->seal_name === $sealName) {
            $sealList .= $seal->seal_number . ' , ';
        }
    }

    return $sealList;
}

function atLoading($before, $after)
{
    if ($before == '1' && $after == '0') {
        return '<input type="checkbox" checked  id="check"> Before Discharge/Loading';
    }
    if ($before == '0' && $after == '1') {
        return '<input type="checkbox" checked  id="check"> After Discharge/Loading';

    }
}
?>
    </div>

    <div class="wrapper" id="logOfFact">
        <?php $index = 1;?>
        <table border="1">
            <tr>
                <td>Vessel Name : <b>Black Perl</b></td>
                <td>Cargo Name : <b> <?=$tank->product?></b></td>
            </tr>
            <tr>
                <td>Terminal Name : <b><?=$tank->terminal?></b></td>
                <td>Tank No : <b> <?=$tank->tank_number?></b></td>
            </tr>
            <tr>
                <td colspan="2">
                    <?=atLoading($tank->before_loading, $tank->after_loading)?>
                </td>

            </tr>
            <tr>
                <td>Date : <b><?=dateFormatter($tank->date)?></b></td>
                <td>Time : <b> <?=$tank->time?></b></td>
            </tr>
        </table>
        <br>

        <table border="1">


            <tr>
                <th>PARTICULARS</th>
                <th colspan="4" class="text-center">MEASUREMENTS</th>
            </tr>


            <tr>
                <th></th>
                <th>1<sup>st</sup> Measurement</th>
                <th>2<sup>nd</sup> Measurement</th>
                <th>1<sup>rd</sup> Measurement</th>
                <th>Average Measurement</th>

            </tr>
            <?php foreach ($measurements as $measurement): ?>
            <tr>
                <td><?=$measurement->title?></td>
                <td><?=$measurement->measurement1?></td>
                <td><?=$measurement->measurement2?></td>
                <td><?=$measurement->measurement3?></td>
                <td><?=$measurement->average?></td>
            </tr>
            <?php endforeach;?>

        </table>
        <br>
        <table border="1">

            <tr>
                <th>WMA SEAL POSITIONS</th>
                <th>WMA SEAL NUMBERS</th>
            </tr>

            <tbody>
                <tr>
                    <td>Outlet</td>
                    <td><?=filterSeal($seals, 'Outlet')?></td>
                </tr>
                <tr>
                    <td>Inlet</td>
                    <td><?=filterSeal($seals, 'Inlet')?></td>
                </tr>
                <tr>
                    <td>Terminal Manifold at KOJ</td>
                    <td><?=filterSeal($seals, 'Terminal Manifold at KOJ')?></td>
                </tr>
                <tr>
                    <td>TIPER Manifold at KOJ</td>
                    <td><?=filterSeal($seals, 'TIPER Manifold at KOJ')?></td>
                </tr>
                <tr>
                    <td>Other Branching lines (terminal or else where)</td>
                    <td><?=filterSeal($seals, 'Other Branching lines (terminal or else where)')?></td>
                </tr>
            </tbody>

        </table>
        <br>
        <table border="1">


            <tr>
                <th>LINE STATUS (FULL / PARTIAL)</th>
                <th>PRODUCT</th>
                <th>HOW VERIFIED</th>

            </tr>


            <tr>
                <td><?=$status->status?></td>
                <td><?=$status->product?></td>
                <td><?=$status->verified?></td>

            </tr>


        </table>









    </div>


    <div class="wrapper">
        <table class="doc-footer">
            <tr>
                <th>TERMINAL REPRESENTATIVE </th>
                <th>WEIGHTS AND MEASURES OFFICER </th>
            <tr>
                <td><b><?=$tank->captain?></b></td>
                <td><b><?=$tank->first_name . ' ' . $tank->last_name?></b></td>
            </tr>
            <tr>
                <td>
                    <p>Signature & Stamp</p>
                    <br>
                    ......................
                </td>
                <td>
                    <p>Signature & Stamp</p>
                    <br>
                    ......................
                </td>
            </tr>
            </tr>

        </table>
    </div>

</body>

</html>