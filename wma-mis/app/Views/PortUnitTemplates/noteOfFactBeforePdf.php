<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title>Note Of Fact Before Discharge</title>
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

    #noteOfFact table {
        margin: 0 auto;
        width: 100%;
        border-collapse: collapse;

    }

    #noteOfFact table td {
        padding: 18px;
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
    <p class="contacts"> Tel: <?=$note->phone_number?> Fax: <?=$note->fax?> ,<?=$note->postal_address?>, e-mail:
        <?=$note->email?></p>
    <div class="wrapper">
        <table class="details">

            <tr>
                <td>Vessel Name: <b><?=$note->ship_name?></b> </td>

                <td>Port: <b><?=$note->port?></b> </td>
                <td>Terminal: <b><?=$note->terminal?></b> </td>

                <td>Product: <b><?=$note->cargo?></b> </td>

                <td>Arrival: <b><?=dateFormatter($note->arrival_date)?></b> </td>

            </tr>


        </table>

        <h3>NOTE OF FACT BEFORE DISCHARGING</h3>
    </div>
    <div class="wrapper" id="noteOfFact">
        <table class="table" border="1">
            <tr>
                <td>BILL OF LADING (MT)</td>
                <td><?=$note->billOfLading1?></td>
                <td>VESSEL FIGURE AFTER LOADING (MT)</td>
                <td><?=$note->vesselFigAfterLoading1?></td>
            </tr>
            <tr>
                <td>VESSEL ARRIVAL QUANTITIES (MT)</td>
                <td><?=$note->arrivalQuantity1?></td>
                <td>VESSEL ARRIVAL QUANTITIES (MT)</td>
                <td><?=$note->arrivalQuantity2?></td>
            </tr>
            <tr>
                <td>DIFFERENCE</td>
                <td><?=$note->Difference1?></td>
                <td>DIFFERENCE</td>
                <td><?=$note->Difference2?></td>
            </tr>
            <tr>
                <td>% DIFFERENCE</td>
                <td><?=$note->DifferencePercent1?></td>
                <td>% DIFFERENCE</td>
                <td><?=$note->DifferencePercent2?></td>
            </tr>

        </table>
        <br>

        <p>After adjusting figures with vessel experience factor (VEF) <b><?=$note->vesselExperienceFactor?></b></p>
        <p>The following Noticed:</p>
        <br>
        <table class="table" border="1" style="border: 1px solid #e1e1e1;">
            <tr>
                <td>BILL OF LADING (MT)</td>
                <td><?=$note->billOfLading1_b?></td>
                <td>VESSEL FIGURE AFTER LOADING (MT)</td>
                <td><?=$note->vesselFigAfterLoading1_b?></td>
            </tr>
            <tr>
                <td>VESSEL ARRIVAL QUANTITIES (MT)</td>
                <td><?=$note->arrivalQuantity1_b?></td>
                <td>VESSEL ARRIVAL QUANTITIES (MT)</td>
                <td><?=$note->arrivalQuantity2_b?></td>
            </tr>
            <tr>
                <td>DIFFERENCE</td>
                <td><?=$note->Difference1_b?></td>
                <td>DIFFERENCE</td>
                <td><?=$note->Difference2_b?></td>
            </tr>
            <tr>
                <td>% DIFFERENCE</td>
                <td><?=$note->DifferencePercent1_b?></td>
                <td>% DIFFERENCE</td>
                <td><?=$note->DifferencePercent2_b?></td>
            </tr>

        </table>

    </div>










    <div class="wrapper">
        <table class="doc-footer">
            <tr>
                <th>CAPTAIN/CHIEF OFFICER </th>
                <th>WEIGHTS AND MEASURES OFFICER </th>
            <tr>
                <td><b><?=$note->captain?></b></td>
                <td><b><?=$note->first_name . ' ' . $note->last_name?></b></td>
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