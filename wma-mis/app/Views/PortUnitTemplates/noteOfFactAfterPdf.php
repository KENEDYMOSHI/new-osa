<?=$this->include('PortUnitTemplates/includes/templateHeader') ?>



<?php
function makeChecked($val)
{
    if ($val == 1) {
        return 'checked';
    } else {
        return '';

    }
}

?>


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


        <h3>NOTE OF FACT AFTER DISCHARGING</h3>
    </div>
    <div class="wrapper" id="noteOfFact">

        <div class="top">
            <div class="checkWrapper">
                <input id="" class="form-check" type="checkbox" <?=makeChecked($note->at_loading)?>>
                <label class="check-label">At Loading</label>
            </div>
            <div class="checkWrapper">
                <input id="" class="form-check" type="checkbox" <?=makeChecked($note->at_discharging)?>>
                <label class="check-label">At Discharging</label>
            </div>
            <div class="checkWrapper">
                <input id="" class="form-check" type="checkbox" <?=makeChecked($note->at_transfer)?>>
                <label class="check-label">At Transfer</label>
            </div>
            <div class="checkWrapper">
                <input id="" class="form-check" type="checkbox" <?=makeChecked($note->at_shore)?>>
                <label class="check-label">At Shore</label>
            </div>
            <div class="checkWrapper">
                <input id="" class="form-check" type="checkbox" <?=makeChecked($note->at_vessel)?>>
                <label class="check-label">At Vessel</label>
            </div>
            <div class="checkWrapper">
                <input id="" class="form-check" type="checkbox">
                <label for="" class="check-label">To the Master /Owner
                    /Agent of the Vessel of <span style="font-size: 20px;"><b><?=$note->ship_name?></b></span></label>
            </div>
            <div class="checkWrapper">
                <input id="" class="form-check" type="checkbox">
                <label for="" class="check-label">To the Terminal Representative of <span
                        style="font-size: 20px;"><b><?=$note->terminal_rep?></b></span></label>
            </div>
        </div>

        <h5 class="text-center">THE CARGO DIFFERENCE</h5>
        <h6 class="text-center">BILL OF LADING QUANTITY AGAINST SHIP' S DISCHARGED QUANTITY OF GASOLINE</h6>

        <div class="bottom">
            <table border="1">
                <thead>
                    <tr>
                        <th>RECEIVER</th>
                        <th>B/LADING QTY</th>
                        <th>SHIP'S DISCH QTY</th>
                        <th>DIFFERENT QTY</th>
                        <th>DIFFERENCE %</th>

                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?=$note->receiver?></td>
                        <td><?=$note->bill_of_lading_qty?></td>
                        <td><?=$note->ship_discharging_qty?></td>
                        <td><?=$note->qty_diff?></td>
                        <td><?=$note->diff_percentage?></td>
                    </tr>
                    <tr>
                        <th colspan="5" class="text-center">SHIP'S DISCHARGED QUANTITY AGAINST SHORE OUTTURN
                            QUANTITY</th>
                    </tr>
                    <tr>
                        <th colspan="2">SHIP'S DISCHARGE QTY</th>
                        <th>SHORE OUTTURN QTY</th>
                        <th>DIFFERENT QTY</th>
                        <th>DIFFERENCE %</th>
                    </tr>

                    <tr>
                        <th>At 15&deg;C</th>
                        <td><?=$note->discharging_qty_15c?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <th>At 20&deg;C</th>
                        <td><?=$note->discharging_qty_20c?></td>
                        <td><?=$note->shore_outturn_qty?></td>
                        <td><?=$note->qty_diff_2?></td>
                        <td><?=$note->diff_percentage_2?></td>
                    </tr>
                </tbody>

            </table>
        </div>


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