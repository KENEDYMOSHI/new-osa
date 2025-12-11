<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title><?=$title?></title>
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

    #discharging table {
        /* margin: 0 auto; */
        width: 100%;
        border-collapse: collapse;


    }

    #discharging table td {
        padding: 10px;
    }

    #discharging table th {
        padding: 10px;
    }

    .details {
        width: 100%;
        margin-bottom: 15px;
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

            </div>
            <div class="logo-right">
                <img src='data:image/jpeg;base64,<?=wmaLogo()?>' alt="">
            </div>
        </div>

    </header>
    <?php foreach ($tanks as $tank): ?>

    <?php endforeach;?>
    <p class="contacts"> Tel: <?=$tank->phone_number?> Fax: <?=$tank->fax?> ,<?=$tank->postal_address?>, e-mail:
        <?=$tank->email?></p>
    <div class="wrapper">
        <table class="details">

            <tr>
                <td>Vessel Name: <b><?=$tank->ship_name?></b> </td>

                <td>Port Name: <b><?=$tank->port_name?></b> </td>

                <td>Cargo Name: <b><?=$tank->cargo?></b> </td>

                <td>Arrival Date: <b><?=dateFormatter($tank->arrival_date)?></b> </td>
                <!-- <td>Time: <b>12:30 Hours</b> </td> -->
            </tr>
            <tr>
                <td>B/L Quantity : <b><?=$tank->billOfLading1?> MT</b></td>
                <td>Arrival Quantity : <b><?=$tank->arrivalQuantity1?> MT</b> </td>
            </tr>


        </table>



        <div class="wrapper">


            <h3>PROPOSED DISCHARGING SEQUENCE</h3>
        </div>
        <div id="discharging">

            <table border="1">

                <tr>
                    <th >Receiving Terminal</th>
                    <th>Product</th>
                    <th>Quantity M/Tonnes</th>
                    <th colspan="2">From</th>
                    <th colspan="2">To</th>

                </tr>


                <?php foreach ($tanks as $tank): ?>

                <?php if ($tank->date_to != '' && $tank->date_to != ''): ?>



                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><b>Time</b></td>
                    <td><b>Date</b></td>
                    <td><b>Time</b></td>
                    <td><b>Date</b></td>

                </tr>
                <tr>

                    <td><?=$tank->terminal?></td>
                    <td><?=$tank->cargo?></td>
                    <td><?=$tank->arrivalQuantity1?></td>
                    <td><?=to24Hours($tank->time_from)?></td>
                    <td><?=$tank->date_from?></td>
                    <td><?=to24Hours($tank->time_to)?></td>
                    <td style=""><?=dateFormatter($tank->date_to)?></td>
                </tr>
                <tr>
                    <td>Tank No <?=$tank->tank_number?></td>
                    <td>Line Displ</td>
                    <td><?=$tank->line_displacement?></td>
                    <td colspan="4" style="text-align: center;"><b>Duration
                            <?=timeDifference($tank->time_from, $tank->time_to)?> Hours</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="7" style="text-align: center;">Ship Stop</td>
                </tr>
                <?php endif;?>
                <?php endforeach;?> 

            </table>





        </div>



        <div class="wrapper">
            <table class="doc-footer">
                <tr>
                    <th>CAPTAIN/CHIEF OFFICER </th>
                    <th>WEIGHTS AND MEASURES OFFICER </th>
                <tr>
                    <td><b><?=$tank->captain?></b></td>
                    <td><b><?=$tank->first_name?> <?=$tank->last_name?></b></td>
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